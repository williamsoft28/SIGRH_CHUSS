<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SusAlerteController extends Controller
{
    /**
     * Liste des alertes pour le service du SUS connecté.
     */
    public function index(Request $request)
    {
        // On récupère le service du SUS
        $sus = auth()->user()->sus;
        if (!$sus || !$sus->service_id) {
            abort(403, "Vous n'êtes assigné à aucun service.");
        }

        $alertes = Alerte::where('service_id', $sus->service_id)
            ->with('beneficiaire')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('sus.alertes.index', compact('alertes'));
    }

    /**
     * Voir les détails d'une alerte et la marquer comme lue.
     */
    public function show(Alerte $alerte)
    {
        $sus = auth()->user()->sus;
        abort_if($alerte->service_id !== $sus->service_id, 403);

        if (!$alerte->lue) {
            $alerte->update(['lue' => true]);
        }

        return view('sus.alertes.show', compact('alerte'));
    }

    /**
     * Télécharger le PDF de l'alerte.
     */
    public function pdf(Alerte $alerte)
    {
        $sus = auth()->user()->sus;
        abort_if($alerte->service_id !== $sus->service_id, 403);

        $alerte->load(['service', 'beneficiaire']);
        $pdf = Pdf::loadView('alertes.pdf', compact('alerte'));
        return $pdf->download('Alerte_CHUSS_' . $alerte->id . '.pdf');
    }
}
