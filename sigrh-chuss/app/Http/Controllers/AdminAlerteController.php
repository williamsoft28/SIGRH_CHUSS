<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Models\Service;
use App\Models\Beneficiaire;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminAlerteController extends Controller
{
    /**
     * Affiche le formulaire de création d'alerte.
     */
    public function create(Request $request)
    {
        $services = Service::orderBy('nom')->get();
        $beneficiaires = [];
        
        // Si un service est sélectionné, charger ses bénéficiaires pour le dropdown
        if ($request->has('service_id') && $request->service_id != '') {
            $beneficiaires = Beneficiaire::where('service_id', $request->service_id)->orderBy('nom')->get();
        }

        return view('admin.alertes.create', compact('services', 'beneficiaires'));
    }

    /**
     * Enregistre l'alerte.
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'beneficiaire_id' => ['nullable', 'exists:beneficiaires,id'],
            'titre' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        $alerte = new Alerte([
            'service_id' => $request->service_id,
            'beneficiaire_id' => $request->beneficiaire_id,
            'titre' => $request->titre,
            'message' => $request->message,
            'lue' => false,
        ]);
        $alerte->save();

        return redirect()->route('hotellerie.alertes.create')->with('status', 'L\'alerte a été envoyée au service avec succès.');
    }

    /**
     * Génère le PDF de l'alerte.
     */
    public function pdf(Alerte $alerte)
    {
        $alerte->load(['service', 'beneficiaire']);
        $pdf = Pdf::loadView('alertes.pdf', compact('alerte'));
        return $pdf->stream('Alerte_CHUSS_' . $alerte->id . '.pdf');
    }
}
