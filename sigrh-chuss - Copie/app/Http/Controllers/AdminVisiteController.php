<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VisiteMedicale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminVisiteController extends Controller
{
    public function index()
    {
        // Liste de TOUS les agents
        $agents = User::with(['visitesMedicales' => function($query) {
                $query->orderBy('date_programmee', 'desc');
            }])
            ->get();
            
        return view('admin.suivi_medical.index', compact('agents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_programmee' => 'required|date|after_or_equal:today',
        ]);
        
        $agent = User::findOrFail($request->user_id);
        
        VisiteMedicale::create([
            'user_id' => $agent->id,
            'date_programmee' => $request->date_programmee,
            'statut' => 'programmee',
        ]);
        
        return redirect()->route('admin.suivi_medical.index')->with('success', 'Visite médicale programmée avec succès.');
    }

    public function update(Request $request, VisiteMedicale $visite)
    {
        $request->validate([
            'resultat' => 'required|string|max:1000',
        ]);
        
        $visite->update([
            'statut' => 'realisee',
            'resultat' => $request->resultat,
            'date_realisation' => now(),
        ]);
        
        return redirect()->route('admin.suivi_medical.index')->with('success', 'Résultat de la visite enregistré.');
    }
}
