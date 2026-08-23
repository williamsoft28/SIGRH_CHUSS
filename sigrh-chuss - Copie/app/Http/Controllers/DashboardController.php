<?php

namespace App\Http\Controllers;

use App\Models\DeclarationJour;
use App\Models\DeclarationPatient;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $date = today();

        // 1. Personnel (Bénéficiaires du jour)
        $personnelDeclarations = DeclarationJour::query()
            ->where(function ($query) use ($date) {
                // Quotidien matches the exact date_repas
                $query->where(function ($q) use ($date) {
                    $q->where('type_periode', 'quotidien')
                      ->whereDate('date_repas', $date->toDateString());
                })
                // Hebdomadaire (or mensuel) matches if there is a bon de repas with a droit_repas for this date
                ->orWhere(function ($q) use ($date) {
                    $q->whereIn('type_periode', ['hebdomadaire', 'mensuel'])
                      ->whereHas('bonRepas.droits', function ($q2) use ($date) {
                          $q2->whereDate('date', $date->toDateString());
                      });
                })
                // Hebdomadaire/Mensuel NOT validated yet (en_saisie)
                ->orWhere(function ($q) use ($date) {
                    $q->whereIn('type_periode', ['hebdomadaire', 'mensuel'])
                      ->whereDoesntHave('bonRepas')
                      ->whereDate('date_debut', '<=', $date->toDateString())
                      ->whereDate('date_fin', '>=', $date->toDateString());
                });
            })
            ->with('beneficiaire.regimeSpecial')
            ->get();

        $personnelDuJourCount = $personnelDeclarations->count();
        $personnelParRegime = $personnelDeclarations->groupBy(function($decl) {
            return $decl->beneficiaire->regimeSpecial ? $decl->beneficiaire->regimeSpecial->libelle : 'Standard (Sans régime)';
        })->map->count();

        // 2. Malades (Patients du jour)
        $maladesDeclarations = DeclarationPatient::query()
            ->whereDate('date_repas', $date->toDateString())
            ->with('regimeSpecial')
            ->get();

        $maladesDuJourCount = $maladesDeclarations->sum('nombre_malades');
        $maladesParRegime = $maladesDeclarations->groupBy(function($decl) {
            return $decl->regimeSpecial ? $decl->regimeSpecial->libelle : 'Standard (Sans régime)';
        })->map(function($group) {
            return $group->sum('nombre_malades');
        });

        return view('dashboard', compact('personnelDuJourCount', 'maladesDuJourCount', 'personnelParRegime', 'maladesParRegime'));
    }
}
