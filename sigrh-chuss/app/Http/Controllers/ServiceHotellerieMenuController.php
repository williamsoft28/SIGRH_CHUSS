<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Observation;
use App\Models\Plat;
use App\Models\Sauce;
use App\Models\Viande;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceHotellerieMenuController extends Controller
{
    private const TYPES_REPAS = ['petit_dejeuner', 'dejeuner', 'diner'];

    /**
     * Historique de tous les menus, toutes semaines confondues.
     */
    public function index(): View
    {
        $menus = Menu::orderByDesc('annee')->orderByDesc('numero_semaine')->paginate(20);

        return view('hotellerie.menus.index', compact('menus'));
    }

    /**
     * Formulaire de composition d'un nouveau menu hebdomadaire (ou reprise
     * d'un menu déjà "soumis" mais pas encore traité par le prestataire).
     */
    public function create(Request $request): View|RedirectResponse
    {
        $lundi = $this->lundiDeLaSemaine($request->date('semaine'));
        $dimanche = $lundi->copy()->addDays(6);

        $menu = Menu::where('numero_semaine', $lundi->weekOfYear)
            ->where('annee', $lundi->year)
            ->first();

        if ($menu && $menu->statut !== 'soumis') {
            return redirect()
                ->route('hotellerie.menus.show', $menu)
                ->with('status', 'Un menu existe déjà pour cette semaine (statut : '.$menu->statut.'). Utilisez cet écran pour le modifier.');
        }

        return view('hotellerie.menus.creer', array_merge(
            [
                'lundi' => $lundi,
                'dimanche' => $dimanche,
                'jours' => collect(range(0, 6))->map(fn (int $i) => $lundi->copy()->addDays($i)),
                'repasExistant' => $this->repasExistant($menu),
            ],
            $this->listesReference()
        ));
    }

    /**
     * Compose (ou recompose) le menu de la semaine et le soumet au prestataire.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validerGrille($request, [
            'date_debut' => ['required', 'date'],
        ]);

        $lundi = Carbon::parse($data['date_debut'])->startOfWeek(Carbon::MONDAY);
        $dimanche = $lundi->copy()->addDays(6);

        $menu = Menu::where('numero_semaine', $lundi->weekOfYear)
            ->where('annee', $lundi->year)
            ->first();

        if ($menu && $menu->statut !== 'soumis') {
            return redirect()
                ->route('hotellerie.menus.show', $menu)
                ->with('status', 'Un menu existe déjà pour cette semaine. Utilisez cet écran pour le modifier.');
        }

        $menu = DB::transaction(function () use ($menu, $lundi, $dimanche, $data) {
            $menu = $menu ?: new Menu([
                'numero_semaine' => $lundi->weekOfYear,
                'annee' => $lundi->year,
                'nb_modifications' => 0,
            ]);

            $menu->date_debut = $lundi->toDateString();
            $menu->date_fin = $dimanche->toDateString();
            $menu->statut = 'soumis';
            $menu->date_soumission = now();
            $menu->save();

            $this->enregistrerGrille($menu, $data['repas'] ?? []);

            return $menu;
        });

        return redirect()
            ->route('hotellerie.menus.show', $menu)
            ->with('status', 'Menu soumis au prestataire.');
    }

    /**
     * Écran unique de consultation/édition d'un menu : le contenu et les
     * actions disponibles dépendent de son statut.
     */
    public function show(Menu $menu): View
    {
        $menu->load(['observations' => fn ($q) => $q->orderByDesc('date_emission')]);

        return view('hotellerie.menus.show', array_merge(
            [
                'menu' => $menu,
                'jours' => collect(range(0, 6))->map(fn (int $i) => $menu->date_debut->copy()->addDays($i)),
                'repasExistant' => $this->repasExistant($menu),
                'peutModifier' => in_array($menu->statut, ['soumis', 'en_observation', 'applique'], true),
            ],
            $this->listesReference()
        ));
    }

    /**
     * Enregistre les modifications de la grille. Les règles diffèrent selon
     * le statut actuel du menu.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $this->validerGrille($request);

        if ($menu->statut === 'applique') {
            if ($menu->nb_modifications >= 1) {
                return back()->withErrors([
                    'modification' => "Le menu ne peut être modifié plus d'une fois par semaine.",
                ]);
            }

            $prochainJour = $menu->menuJours()
                ->where('date_jour', '>=', today())
                ->orderBy('date_jour')
                ->first();

            if ($prochainJour && now()->addHours(24)->gt($prochainJour->date_jour->copy()->startOfDay())) {
                return back()->withErrors([
                    'modification' => 'Préavis de 24h non respecté.',
                ]);
            }

            $this->enregistrerGrille($menu, $data['repas'] ?? []);
            $menu->increment('nb_modifications');

            return redirect()->route('hotellerie.menus.show', $menu)->with('status', 'Menu modifié avec succès.');
        }

        if (in_array($menu->statut, ['soumis', 'en_observation'], true)) {
            $this->enregistrerGrille($menu, $data['repas'] ?? []);

            return redirect()->route('hotellerie.menus.show', $menu)->with('status', 'Menu mis à jour.');
        }

        return back()->withErrors([
            'statut' => "Ce menu ne peut pas être modifié dans son état actuel ({$menu->statut}).",
        ]);
    }

    /**
     * Valide puis applique le menu. La règle du jeudi n'est qu'un
     * avertissement : elle ne bloque jamais la validation.
     */
    public function valider(Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'en_observation', 409, "Ce menu n'est pas en attente de validation.");

        $lundiSuivantAttendu = today()->startOfWeek(Carbon::MONDAY)->addWeek();
        $respecteRegleDuJeudi = today()->isThursday() && $menu->date_debut->isSameDay($lundiSuivantAttendu);

        $menu->update([
            'statut' => 'applique',
            'date_validation' => now(),
        ]);

        $message = 'Menu validé et appliqué.';
        if (! $respecteRegleDuJeudi) {
            $message .= ' ⚠ La validation se fait normalement le jeudi pour la semaine qui commence le lundi suivant.';
        }

        return redirect()->route('hotellerie.menus.show', $menu)->with('status', $message);
    }

    /**
     * Marque une observation du prestataire comme traitée.
     */
    public function marquerObservationTraitee(Observation $observation): RedirectResponse
    {
        $observation->update(['statut' => 'traitee']);

        return back()->with('status', 'Observation marquée comme traitée.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validerGrille(Request $request, array $reglesSupplementaires = []): array
    {
        return $request->validate(array_merge($reglesSupplementaires, [
            'repas' => ['nullable', 'array'],
            'repas.*' => ['array'],
            'repas.*.*.plat_id' => ['nullable', 'exists:plats,id'],
            'repas.*.*.sauce_id' => ['nullable', 'exists:sauces,id'],
            'repas.*.*.viande_id' => ['nullable', 'exists:viandes,id'],
            'repas.*.*.dessert_id' => ['nullable', 'exists:plats,id'],
        ]));
    }

    /**
     * Crée/replace les 7 MenuJour et leurs Repas à partir de la grille soumise.
     *
     * @param  array<string, array<string, array<string, mixed>>>  $repasParJour
     */
    private function enregistrerGrille(Menu $menu, array $repasParJour): void
    {
        foreach (range(0, 6) as $i) {
            $jourDate = $menu->date_debut->copy()->addDays($i);
            $dateStr = $jourDate->toDateString();

            $menuJour = $menu->menuJours()->firstOrCreate(
                ['date_jour' => $dateStr],
                ['jour_semaine' => ucfirst($jourDate->locale('fr')->translatedFormat('l'))]
            );

            foreach (self::TYPES_REPAS as $typeRepas) {
                $selection = $repasParJour[$dateStr][$typeRepas] ?? [];

                $menuJour->repas()->updateOrCreate(
                    ['type_repas' => $typeRepas],
                    [
                        'plat_id' => $selection['plat_id'] ?? null,
                        'sauce_id' => $selection['sauce_id'] ?? null,
                        'viande_id' => $selection['viande_id'] ?? null,
                        'dessert_id' => $selection['dessert_id'] ?? null,
                    ]
                );
            }
        }
    }

    /**
     * Sélections déjà enregistrées, indexées par date puis type de repas.
     *
     * @return array<string, array<string, array<string, int|null>>>
     */
    private function repasExistant(?Menu $menu): array
    {
        if (! $menu || ! $menu->exists) {
            return [];
        }

        $menu->loadMissing('menuJours.repas');

        $resultat = [];
        foreach ($menu->menuJours as $menuJour) {
            $dateStr = $menuJour->date_jour->toDateString();
            foreach ($menuJour->repas as $repas) {
                $resultat[$dateStr][$repas->type_repas] = [
                    'plat_id' => $repas->plat_id,
                    'sauce_id' => $repas->sauce_id,
                    'viande_id' => $repas->viande_id,
                    'dessert_id' => $repas->dessert_id,
                ];
            }
        }

        return $resultat;
    }

    /**
     * @return array<string, mixed>
     */
    private function listesReference(): array
    {
        return [
            'platsPetitDej' => Plat::where('type', 'petit_dejeuner')->where('actif', true)->orderBy('nom')->get(),
            'platsBase' => Plat::where('type', 'plat_base')->where('actif', true)->orderBy('nom')->get(),
            'desserts' => Plat::where('type', 'dessert')->where('actif', true)->orderBy('nom')->get(),
            'sauces' => Sauce::where('actif', true)->orderBy('nom')->get(),
            'viandes' => Viande::where('actif', true)->orderBy('nom')->get(),
        ];
    }

    private function lundiDeLaSemaine(?Carbon $date): Carbon
    {
        return ($date ?? today())->copy()->startOfWeek(Carbon::MONDAY);
    }
}
