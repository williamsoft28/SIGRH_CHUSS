<?php

namespace App\Http\Controllers;

use App\Models\Derogation;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDerogationController extends Controller
{
    /**
     * Toutes les demandes de dérogation, ainsi que le formulaire d'octroi direct.
     */
    public function index(): View
    {
        $derogations = Derogation::with(['service', 'demandePar', 'autoriseePar'])
            ->orderByDesc('date')
            ->get();

        $services = Service::orderBy('nom')->get();

        return view('derogations.admin_index', compact('derogations', 'services'));
    }

    /**
     * Octroi direct d'une dérogation par l'administrateur, sans demande préalable du SUS.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'date' => ['required', 'date'],
        ]);

        Derogation::updateOrCreate(
            ['service_id' => $data['service_id'], 'date' => $data['date']],
            [
                'statut' => 'autorisee',
                'autorisee_par_id' => $request->user()->id,
            ]
        );

        return back()->with('status', 'Dérogation accordée pour le service sélectionné.');
    }

    public function autoriser(Request $request, Derogation $derogation): RedirectResponse
    {
        $derogation->update([
            'statut' => 'autorisee',
            'autorisee_par_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Dérogation autorisée.');
    }

    public function refuser(Request $request, Derogation $derogation): RedirectResponse
    {
        $derogation->update([
            'statut' => 'refusee',
            'autorisee_par_id' => $request->user()->id,
        ]);

        return back()->with('status', 'Demande de dérogation refusée.');
    }
}
