<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Plat;

use App\Models\Viande;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrestataireMenuController extends Controller
{
    /**
     * File d'attente : menus soumis par le service hôtellerie, en attente
     * d'observations du prestataire.
     */
    public function index(): View
    {
        $menus = Menu::where('statut', 'soumis')
            ->orderBy('date_debut')
            ->get();

        return view('prestataire.menus.index', compact('menus'));
    }

    /**
     * Historique de tous les menus, quel que soit leur statut.
     */
    public function historique(): View
    {
        $menus = Menu::orderByDesc('annee')->orderByDesc('numero_semaine')->paginate(20);

        return view('prestataire.menus.historique', compact('menus'));
    }

    /**
     * Consultation du menu (lecture seule) et de ses observations. Le
     * formulaire d'ajout d'observation n'apparaît que si le menu est "soumis".
     */
    public function show(Menu $menu): View
    {
        $menu->load([
            'menuJours.repas.plat',

            'menuJours.repas.viande',
            'menuJours.repas.dessert',
            'observations' => fn ($q) => $q->orderByDesc('date_emission'),
        ]);

        $jours = collect(range(0, 6))->map(fn (int $i) => $menu->date_debut->copy()->addDays($i));

        $repasExistant = [];
        foreach ($menu->menuJours as $menuJour) {
            $dateStr = $menuJour->date_jour->toDateString();
            foreach ($menuJour->repas as $repas) {
                $repasExistant[$dateStr][$repas->type_repas] = [
                    'plat_id' => $repas->plat_id,

                    'viande_id' => $repas->viande_id,
                    'dessert_id' => $repas->dessert_id,
                ];
            }
        }

        return view('prestataire.menus.show', array_merge(
            compact('menu', 'jours', 'repasExistant'),
            [
                'platsPetitDej' => Plat::where('type', 'petit_dejeuner')->orderBy('nom')->get(),
                'platsBase' => Plat::where('type', 'plat_base')->orderBy('nom')->get(),
                'desserts' => Plat::where('type', 'dessert')->orderBy('nom')->get(),

                'viandes' => Viande::orderBy('nom')->get(),
            ]
        ));
    }

    /**
     * Ajoute une observation au menu (répétable), sans changer son statut.
     */
    public function storeObservation(Request $request, Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'soumis', 409, "Ce menu n'est plus ouvert aux observations.");

        $data = $request->validate([
            'contenu' => ['required', 'string'],
        ]);

        $menu->observations()->create([
            'contenu' => $data['contenu'],
            'date_emission' => now(),
            'statut' => 'ouverte',
        ]);

        return back()->with('status', 'Observation ajoutée.');
    }

    /**
     * Enregistre les modifications de la grille et une observation optionnelle.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'soumis', 409, "Ce menu n'est plus ouvert aux observations.");

        $data = $request->validate([
            'repas' => ['required', 'array'],
            'repas.*' => ['nullable', 'array'],
            'repas.*.*.plat_id' => ['nullable', 'exists:plats,id'],

            'repas.*.*.viande_id' => ['nullable', 'exists:viandes,id'],
            'repas.*.*.dessert_id' => ['nullable', 'exists:plats,id'],
            'contenu' => ['nullable', 'string'],
        ], [
            'repas.*.*.plat_id.required' => 'Vous devez sélectionner un plat principal pour chaque repas.',
        ]);

        $changements = [];
        $plats = \App\Models\Plat::pluck('nom', 'id');

        $viandes = \App\Models\Viande::pluck('nom', 'id');

        $libellesRepas = [
            'petit_dejeuner' => 'Petit-déjeuner',
            'dejeuner' => 'Déjeuner',
            'diner' => 'Dîner',
        ];

        foreach (range(0, 6) as $i) {
            $jourDate = $menu->date_debut->copy()->addDays($i);
            $dateStr = $jourDate->toDateString();
            $nomJour = ucfirst($jourDate->locale('fr')->translatedFormat('l d/m'));

            $menuJour = $menu->menuJours()->firstOrCreate(
                ['date_jour' => $dateStr],
                ['jour_semaine' => ucfirst($jourDate->locale('fr')->translatedFormat('l'))]
            );

            foreach (['petit_dejeuner', 'dejeuner', 'diner'] as $typeRepas) {
                $selection = $data['repas'][$dateStr][$typeRepas] ?? [];
                
                $repasExistant = $menuJour->repas()->where('type_repas', $typeRepas)->first();

                $checkChange = function($idAncien, $idNouveau, $collection, $libelleChamp) use (&$changements, $nomJour, $libellesRepas, $typeRepas) {
                    if ($idAncien != $idNouveau) {
                        $nomAncien = $idAncien ? ($collection[$idAncien] ?? 'Inconnu') : 'Rien';
                        $nomNouveau = $idNouveau ? ($collection[$idNouveau] ?? 'Inconnu') : 'Rien';
                        $changements[] = "• {$nomJour} ({$libellesRepas[$typeRepas]}) : {$libelleChamp} modifié ('{$nomAncien}' ➔ '{$nomNouveau}')";
                    }
                };

                $checkChange($repasExistant?->plat_id, $selection['plat_id'] ?? null, $plats, 'Plat');

                $checkChange($repasExistant?->viande_id, $selection['viande_id'] ?? null, $viandes, 'Viande');
                $checkChange($repasExistant?->dessert_id, $selection['dessert_id'] ?? null, $plats, 'Dessert');

                $menuJour->repas()->updateOrCreate(
                    ['type_repas' => $typeRepas],
                    [
                        'plat_id' => $selection['plat_id'] ?? null,

                        'viande_id' => $selection['viande_id'] ?? null,
                        'dessert_id' => $selection['dessert_id'] ?? null,
                    ]
                );
            }
        }

        // Créer une observation automatique si le système a détecté des changements dans la grille
        if (count($changements) > 0) {
            $contenuAuto = "⚠️ Le prestataire a modifié la composition du menu :\n" . implode("\n", $changements);
            $menu->observations()->create([
                'contenu' => $contenuAuto,
                'date_emission' => now(),
                'statut' => 'ouverte',
            ]);
        }

        // Enregistrer l'observation textuelle du prestataire s'il en a écrit une
        if (!empty($data['contenu'])) {
            $menu->observations()->create([
                'contenu' => $data['contenu'],
                'date_emission' => now(),
                'statut' => 'ouverte',
            ]);
        }

        return back()->with('status', 'Modifications enregistrées avec succès.');
    }

    /**
     * Envoie les observations au service hôtellerie : fait passer le menu
     * au statut "en_observation".
     */
    public function envoyerObservations(Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'soumis', 409, "Ce menu n'est plus ouvert aux observations.");

        // Allow sending even if no observations exist, since they could have just modified the menu grid directly
        // The previous code blocked sending if count === 0, but now that they can edit the menu, 
        // they might not need to leave a text observation.
        $menu->update(['statut' => 'en_observation']);

        return redirect()
            ->route('prestataire.menus.index')
            ->with('status', 'Menu renvoyé au service hôtellerie.');
    }

    /**
     * Valide le menu à l'étape prestataire sans observation.
     */
    public function valider(Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'soumis', 409, "Ce menu n'est plus ouvert à la validation.");

        $menu->update([
            'statut' => 'valide',
            'date_validation' => now(),
        ]);

        return redirect()
            ->route('prestataire.menus.index')
            ->with('status', 'Menu validé. Le service hôtellerie peut maintenant le confirmer finalement.');
    }
}
