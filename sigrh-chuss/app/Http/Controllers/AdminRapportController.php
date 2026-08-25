<?php

namespace App\Http\Controllers;

use App\Models\Consommation;
use App\Models\DeclarationPatient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class AdminRapportController extends Controller
{
    /**
     * Affiche le formulaire de génération de rapports.
     */
    public function index()
    {
        return view('admin.rapports.index');
    }

    /**
     * Génère le rapport PDF selon la période et le type sélectionnés.
     */
    public function generer(Request $request)
    {
        $request->validate([
            'date_debut' => ['required', 'date'],
            'date_fin'   => ['required', 'date', 'after_or_equal:date_debut'],
        ]);

        $dateDebut = Carbon::parse($request->date_debut)->startOfDay();
        $dateFin   = Carbon::parse($request->date_fin)->endOfDay();

        // 1. Statistiques du Personnel (Consommations réelles validées)
        $consommationsPersonnel = Consommation::with(['bonRepas.beneficiaire.service'])
            ->where('statut', 'consomme')
            ->whereBetween('date_repas', [$dateDebut, $dateFin])
            ->get();

        $statsPersonnel = $consommationsPersonnel->groupBy('type_repas')->map(function ($items) {
            return $items->count();
        });

        // 2. Statistiques des Malades (Déclarations validées par les SUS)
        $declarationsMalades = DeclarationPatient::with(['service', 'regimeSpecial'])
            ->whereBetween('date_repas', [$dateDebut, $dateFin])
            ->get();

        $statsMalades = [
            'total' => $declarationsMalades->sum('nombre_plats'),
            'details' => $declarationsMalades->groupBy('regimeSpecial.libelle')->map(function ($items) {
                return $items->sum('nombre_plats');
            })
        ];

        // Compilation des données pour la vue PDF
        $data = [
            'date_debut' => $dateDebut,
            'date_fin'   => $dateFin,
            'statsPersonnel' => $statsPersonnel,
            'statsMalades' => $statsMalades,
            'totalGeneral' => $statsPersonnel->sum() + $statsMalades['total'],
        ];

        $pdf = Pdf::loadView('admin.rapports.pdf', $data);

        $nomFichier = 'Rapport_CHUSS_' . $dateDebut->format('dmY') . '_au_' . $dateFin->format('dmY') . '.pdf';

        return $pdf->download($nomFichier);
    }
}
