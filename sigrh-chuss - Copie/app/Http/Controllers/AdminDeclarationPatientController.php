<?php

namespace App\Http\Controllers;

use App\Models\DeclarationPatient;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDeclarationPatientController extends Controller
{
    /**
     * Liste des déclarations de patients de tous les services.
     */
    public function index(Request $request): View
    {
        $date = $request->date('date') ?? today();

        $declarations = DeclarationPatient::query()
            ->whereDate('date_repas', $date->toDateString())
            ->with(['service', 'regimeSpecial', 'sus'])
            ->orderBy('service_id')
            ->get();

        // Agrouper par service pour faciliter l'affichage
        $declarationsParService = $declarations->groupBy(fn ($d) => $d->service->nom);

        return view('declarations_patients.admin.index', compact('declarationsParService', 'date', 'declarations'));
    }
}
