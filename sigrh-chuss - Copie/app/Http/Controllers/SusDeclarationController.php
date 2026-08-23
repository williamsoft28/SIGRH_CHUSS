<?php

namespace App\Http\Controllers;

use App\Models\Beneficiaire;
use App\Models\DeclarationJour;
use App\Support\DeclarationLock;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SusDeclarationController extends Controller
{
    /**
     * Liste des déclarations déjà saisies pour le service du SUS connecté.
     */
    public function index(Request $request): View
    {
        $service = $request->user()->service;

        $declarations = DeclarationJour::query()
            ->whereHas('beneficiaire', fn ($q) => $q->where('service_id', $service?->id))
            ->with(['beneficiaire', 'bonRepas'])
            ->orderByDesc('date_repas')
            ->paginate(20);

        return view('declarations.index', compact('declarations'));
    }

    /**
     * Formulaire de saisie des bénéficiaires qui mangent à une date donnée.
     */
    public function create(Request $request): View
    {
        $service = $request->user()->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $date = $request->date('date') ?? today();

        $beneficiaires = Beneficiaire::where('service_id', $service->id)
            ->orderBy('nom')
            ->get();

        $dejaDeclares = DeclarationJour::whereIn('beneficiaire_id', $beneficiaires->pluck('id'))
            ->whereDate('date_repas', $date->toDateString())
            ->pluck('beneficiaire_id')
            ->all();

        $ouverte = DeclarationLock::estOuverte($service, $date);

        return view('declarations.creer', compact('beneficiaires', 'date', 'ouverte', 'dejaDeclares'));
    }

    /**
     * Enregistre une DeclarationJour par bénéficiaire sélectionné.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $service = $user->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $sus = $user->sus;
        abort_unless($sus, 403, "Votre compte SUS n'est pas configuré. Contactez l'administrateur.");

        $date = Carbon::parse($request->input('date', today()->toDateString()));

        if (! DeclarationLock::estOuverte($service, $date)) {
            return back()->withErrors([
                'date' => "La saisie est verrouillée pour cette date (après 09h00). Faites une demande de dérogation.",
            ])->withInput();
        }

        $declarer = $request->input('declarer', []);
        $repasInput = $request->input('repas', []);
        $periodeInput = $request->input('type_periode', []);
        $debutInput = $request->input('date_debut', []);
        $finInput = $request->input('date_fin', []);

        $beneficiaireIds = array_keys(array_filter($declarer));

        if (empty($beneficiaireIds)) {
            return back()->withErrors(['declarer' => 'Sélectionnez au moins un bénéficiaire.'])->withInput();
        }

        $beneficiaires = Beneficiaire::where('service_id', $service->id)
            ->whereIn('id', $beneficiaireIds)
            ->get();

        $deroge = ! DeclarationLock::avantHeureLimite($date);

        $erreurs = [];
        $aCreer = [];

        foreach ($beneficiaires as $beneficiaire) {
            $repas = array_values(array_intersect(
                $repasInput[$beneficiaire->id] ?? [],
                ['petit_dejeuner', 'dejeuner', 'diner']
            ));

            if (empty($repas)) {
                $erreurs[] = "Sélectionnez au moins un repas pour {$beneficiaire->nom}.";

                continue;
            }

            if ($beneficiaire->type === 'regulier') {
                $typePeriode = in_array($periodeInput[$beneficiaire->id] ?? null, ['hebdomadaire', 'mensuel'], true)
                    ? $periodeInput[$beneficiaire->id]
                    : 'hebdomadaire';

                $dateDebut = ! empty($debutInput[$beneficiaire->id])
                    ? Carbon::parse($debutInput[$beneficiaire->id])
                    : $date->copy();

                $dateFin = ! empty($finInput[$beneficiaire->id])
                    ? Carbon::parse($finInput[$beneficiaire->id])
                    : $dateDebut->copy()->addDays($typePeriode === 'mensuel' ? 29 : 6);

                if ($dateFin->lt($dateDebut)) {
                    $erreurs[] = "La date de fin doit être postérieure à la date de début pour {$beneficiaire->nom}.";

                    continue;
                }
            } else {
                $typePeriode = 'quotidien';
                $dateDebut = $date->copy();
                $dateFin = $date->copy();
            }

            if (DeclarationJour::where('beneficiaire_id', $beneficiaire->id)
                ->whereDate('date_repas', $dateDebut->toDateString())
                ->exists()) {
                $erreurs[] = "{$beneficiaire->nom} est déjà déclaré(e) pour cette date.";

                continue;
            }

            $aCreer[] = [
                'sus_id' => $sus->id,
                'beneficiaire_id' => $beneficiaire->id,
                'date_repas' => $dateDebut->toDateString(),
                'type_periode' => $typePeriode,
                'date_debut' => $dateDebut->toDateString(),
                'date_fin' => $dateFin->toDateString(),
                'repas' => $repas,
                'statut' => 'en_saisie',
                'deroge' => $deroge,
            ];
        }

        if (! empty($erreurs)) {
            return back()->withErrors($erreurs)->withInput();
        }

        foreach ($aCreer as $donnees) {
            DeclarationJour::create($donnees);
        }

        return redirect()
            ->route('declarations.index')
            ->with('status', count($aCreer).' déclaration(s) enregistrée(s) avec succès.');
    }
}
