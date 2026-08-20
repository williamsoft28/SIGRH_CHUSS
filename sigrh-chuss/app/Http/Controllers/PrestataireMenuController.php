<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Plat;
use App\Models\Sauce;
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
            'menuJours.repas.sauce',
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
                    'sauce_id' => $repas->sauce_id,
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
                'sauces' => Sauce::orderBy('nom')->get(),
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
     * Envoie les observations au service hôtellerie : fait passer le menu
     * au statut "en_observation".
     */
    public function envoyerObservations(Menu $menu): RedirectResponse
    {
        abort_unless($menu->statut === 'soumis', 409, "Ce menu n'est plus ouvert aux observations.");

        if ($menu->observations()->count() === 0) {
            return back()->withErrors(['observations' => 'Ajoutez au moins une observation avant d’envoyer.']);
        }

        $menu->update(['statut' => 'en_observation']);

        return redirect()
            ->route('prestataire.menus.index')
            ->with('status', 'Observations envoyées au service hôtellerie.');
    }
}
