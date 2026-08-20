<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminAnneeController extends Controller
{
    public function index()
    {
        $annees = \App\Models\Annee::all();
        return view('super_admin.annees.index', compact('annees'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|unique:annees,nom',
        ]);
        
        \App\Models\Annee::create($data);
        return back()->with('status', 'Année créée avec succès.');
    }

    public function archiver(Request $request, \App\Models\Annee $annee)
    {
        $annee->update(['est_archivee' => true]);
        return back()->with('status', 'Année archivée avec succès.');
    }
}
