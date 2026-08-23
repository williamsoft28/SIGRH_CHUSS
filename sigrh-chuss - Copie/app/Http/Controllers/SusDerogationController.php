<?php

namespace App\Http\Controllers;

use App\Models\Derogation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SusDerogationController extends Controller
{
    /**
     * Historique des demandes de dérogation du service du SUS connecté.
     */
    public function index(Request $request): View
    {
        $service = $request->user()->service;

        $derogations = Derogation::where('service_id', $service?->id)
            ->orderByDesc('date')
            ->get();

        return view('derogations.sus_index', compact('derogations'));
    }

    /**
     * Le SUS demande une saisie d'urgence après l'heure limite pour son service.
     */
    public function store(Request $request): RedirectResponse
    {
        $service = $request->user()->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $data = $request->validate([
            'date' => ['required', 'date'],
            'motif' => ['nullable', 'string', 'max:500'],
        ]);

        $existante = Derogation::where('service_id', $service->id)
            ->whereDate('date', $data['date'])
            ->first();

        if ($existante && $existante->statut === 'autorisee') {
            return back()->with('status', 'Une dérogation est déjà autorisée pour cette date.');
        }

        Derogation::updateOrCreate(
            ['service_id' => $service->id, 'date' => $data['date']],
            [
                'statut' => 'demandee',
                'motif' => $data['motif'] ?? null,
                'demande_par_id' => $request->user()->id,
                'autorisee_par_id' => null,
            ]
        );

        return back()->with('status', "Demande de dérogation envoyée à l'administrateur.");
    }
}
