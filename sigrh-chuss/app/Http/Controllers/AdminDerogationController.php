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
            'service_id' => ['required'],
            'date' => ['required', 'date'],
            'heure_debut' => ['nullable', 'date_format:H:i'],
            'heure_fin' => ['nullable', 'date_format:H:i', 'after:heure_debut'],
        ]);

        if ($data['service_id'] === 'all') {
            $services = Service::all();
            foreach ($services as $service) {
                Derogation::updateOrCreate(
                    ['service_id' => $service->id, 'date' => $data['date']],
                    [
                        'heure_debut' => $data['heure_debut'],
                        'heure_fin' => $data['heure_fin'],
                        'statut' => 'autorisee',
                        'autorisee_par_id' => $request->user()->id,
                    ]
                );
            }
            return back()->with('status', 'Dérogation accordée pour tous les services.');
        }

        $request->validate(['service_id' => 'exists:services,id']);

        Derogation::updateOrCreate(
            ['service_id' => $data['service_id'], 'date' => $data['date']],
            [
                'heure_debut' => $data['heure_debut'],
                'heure_fin' => $data['heure_fin'],
                'statut' => 'autorisee',
                'autorisee_par_id' => $request->user()->id,
            ]
        );

        return back()->with('status', 'Dérogation accordée pour le service sélectionné.');
    }

    public function destroy(Derogation $derogation): RedirectResponse
    {
        $derogation->delete();
        return back()->with('status', 'Dérogation supprimée (rebloquée).');
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

    public function toutDebloquer(Request $request): RedirectResponse
    {
        $services = Service::all();
        $date = today()->toDateString();
        $userId = $request->user()->id;

        foreach ($services as $service) {
            Derogation::updateOrCreate(
                ['service_id' => $service->id, 'date' => $date],
                [
                    'statut' => 'autorisee',
                    'autorisee_par_id' => $userId,
                ]
            );
        }

        return back()->with('status', 'Toutes les exceptions ont été débloquées pour aujourd\'hui.');
    }

    public function toutRebloquer(Request $request): RedirectResponse
    {
        $date = today()->toDateString();

        Derogation::where('date', $date)->delete();

        return back()->with('status', 'Toutes les exceptions ont été rebloquées pour aujourd\'hui.');
    }
}
