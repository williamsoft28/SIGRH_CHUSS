<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\BonRepas;
use App\Models\DeclarationJour;
use App\Models\DroitRepas;
use App\Models\RegimeSpecial;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BeneficiaireController extends Controller
{
    /**
     * Liste des bénéficiaires du service du SUS connecté.
     */
    public function index(Request $request): View
    {
        $beneficiaires = Beneficiaire::query()
            ->where('service_id', $request->user()->service_id)
            ->with([
                'regimeSpecial',
                'declarationJours' => fn ($q) => $q->latest('created_at')->with('bonRepas')
            ])
            ->orderBy('nom')
            ->paginate(20);

        return view('beneficiaires.index', compact('beneficiaires'));
    }

    /**
     * Formulaire de création d'un bénéficiaire avec son calendrier hebdomadaire
     * de repas (lundi à dimanche de la semaine sélectionnée).
     */
    public function create(Request $request): View
    {
        $lundi = $this->lundiDeLaSemaine($request->date('semaine'));

        $jours = collect(range(0, 6))->map(fn (int $i) => $lundi->copy()->addDays($i));

        return view('beneficiaires.create', [
            'jours' => $jours,
            'lundi' => $lundi,
        ]);
    }

    /**
     * Crée le bénéficiaire, sa déclaration hebdomadaire et un unique bon de
     * repas couvrant la semaine. Pour chaque jour, le SUS choisit une
     * catégorie (aucune / après-midi / garde) dont les repas sont déduits
     * automatiquement :
     *   - après-midi -> déjeuner ce jour
     *   - garde      -> dîner ce jour + petit-déjeuner le lendemain matin
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $service = $user->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $sus = $user->sus;
        abort_unless($sus, 403, "Votre compte SUS n'est pas configuré. Contactez l'administrateur.");

        \Illuminate\Support\Facades\Log::info('BENEFICIAIRE_STORE_REQUEST', $request->all());

        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'numero_whatsapp' => ['required', 'string', 'max:20'],
            'lundi' => ['required', 'date'],
            'categorie_jour' => ['nullable', 'array'],
            'categorie_jour.*' => ['in:aucune,apres_midi,garde'],
        ]);

        $lundi = $this->lundiDeLaSemaine(Carbon::parse($data['lundi']));
        $jours = collect(range(0, 6))->map(fn (int $i) => $lundi->copy()->addDays($i));

        $categoriesJour = collect($request->input('categorie_jour', []))
            ->only($jours->map(fn (Carbon $j) => $j->toDateString())->all());

        $droitsParJour = collect();
        $ajouterDroit = function (string $date, string $typeRepas) use (&$droitsParJour) {
            $existants = $droitsParJour->get($date, []);
            if (! in_array($typeRepas, $existants, true)) {
                $existants[] = $typeRepas;
            }
            $droitsParJour->put($date, $existants);
        };

        $categoriesRetenues = collect();

        foreach ($jours as $jour) {
            $dateStr = $jour->toDateString();
            $categorie = $categoriesJour->get($dateStr, 'aucune');

            if ($categorie === 'apres_midi') {
                $categoriesRetenues->push('apres_midi');
                $ajouterDroit($dateStr, 'dejeuner');
            } elseif ($categorie === 'garde') {
                $categoriesRetenues->push('garde');
                $ajouterDroit($dateStr, 'diner');
                $ajouterDroit($jour->copy()->addDay()->toDateString(), 'petit_dejeuner');
            }
        }

        if ($categoriesRetenues->isEmpty()) {
            return back()
                ->withErrors(['categorie_jour' => 'Choisissez au moins une catégorie (après-midi ou garde) sur la semaine.'])
                ->withInput();
        }

        $type = $categoriesRetenues->contains('garde') ? 'variable' : 'regulier';
        $categorieGlobale = match (true) {
            $categoriesRetenues->contains('garde') => 'Personnel de garde',
            $categoriesRetenues->contains('apres_midi') => 'Après-midi',
            default => 'Personnel continu',
        };

        $dimanche = $lundi->copy()->addDays(6);

        $bon = DB::transaction(function () use ($data, $service, $sus, $lundi, $dimanche, $droitsParJour, $type, $categorieGlobale) {
            $beneficiaire = $service->beneficiaires()->create([
                'nom' => trim("{$data['prenom']} {$data['nom']}"),
                'categorie' => $categorieGlobale,
                'numero_whatsapp' => $data['numero_whatsapp'],
                'type' => $type,
            ]);

            $declaration = DeclarationJour::create([
                'sus_id' => $sus->id,
                'beneficiaire_id' => $beneficiaire->id,
                'date_repas' => $lundi->toDateString(),
                'type_periode' => 'hebdomadaire',
                'date_debut' => $lundi->toDateString(),
                'date_fin' => $dimanche->toDateString(),
                'repas' => $droitsParJour->flatten()->unique()->values()->all(),
                'statut' => 'validee',
                'deroge' => false,
            ]);

            $bon = $declaration->bonRepas()->create([
                'code_unique' => BonRepas::genererCodeUnique(),
                'code_court' => BonRepas::genererCodeCourt($service, $droitsParJour->flatten()->unique()->values()->all()),
                'type_periode' => 'hebdomadaire',
                'date_debut' => $lundi->toDateString(),
                'date_fin' => $dimanche->toDateString(),
                'canal_envoi' => 'whatsapp',
                'date_emission' => now(),
            ]);

            foreach ($droitsParJour as $date => $typesRepas) {
                foreach ($typesRepas as $typeRepas) {
                    DroitRepas::create([
                        'bon_repas_id' => $bon->id,
                        'date' => $date,
                        'type_repas' => $typeRepas,
                    ]);
                }
            }

            return $bon;
        });

        return redirect()
            ->route('beneficiaires.bons.show', $bon)
            ->with('status', "Bénéficiaire {$bon->declarationJour->beneficiaire->nom} ajouté(e), bon de repas hebdomadaire généré.");
    }

    /**
     * Formulaire d'édition d'un bénéficiaire.
     */
    public function edit(Request $request, Beneficiaire $beneficiaire): View
    {
        $this->authorizeAccess($request, $beneficiaire);

        $regimesSpeciaux = RegimeSpecial::orderBy('libelle')->get();

        return view('beneficiaires.edit', compact('beneficiaire', 'regimesSpeciaux'));
    }

    /**
     * Met à jour un bénéficiaire du service du SUS connecté.
     */
    public function update(Request $request, Beneficiaire $beneficiaire): RedirectResponse
    {
        $this->authorizeAccess($request, $beneficiaire);

        $beneficiaire->update($this->validatedEdition($request));

        return redirect()
            ->route('beneficiaires.index')
            ->with('status', 'Bénéficiaire mis à jour avec succès.');
    }

    /**
     * Supprime un bénéficiaire.
     */
    public function destroy(Request $request, Beneficiaire $beneficiaire): RedirectResponse
    {
        $this->authorizeAccess($request, $beneficiaire);

        $beneficiaire->delete();

        return redirect()
            ->route('beneficiaires.index')
            ->with('status', 'Bénéficiaire supprimé avec succès.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedEdition(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'categorie' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:regulier,variable'],
            'numero_whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'regime_special_id' => ['nullable', 'exists:regime_specials,id'],
        ]);
    }

    /**
     * Un SUS ne peut agir que sur les bénéficiaires de son propre service.
     */
    private function authorizeAccess(Request $request, Beneficiaire $beneficiaire): void
    {
        abort_unless(
            $beneficiaire->service_id === $request->user()->service_id,
            403
        );
    }

    private function lundiDeLaSemaine(?Carbon $date): Carbon
    {
        return ($date ?? today())->copy()->startOfWeek(Carbon::MONDAY);
    }
}
