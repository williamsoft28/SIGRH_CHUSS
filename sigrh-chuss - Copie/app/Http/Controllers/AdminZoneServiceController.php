<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use App\Models\ZoneService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminZoneServiceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $isLocked = Carbon::parse($date)->startOfDay()->addHours(24)->isPast();
        
        $zones = Zone::orderBy('nom')->get();
        
        // Eager load ou récupération des services pour cette date
        $services = ZoneService::where('date_service', $date)
            ->get()
            ->keyBy('zone_id');
            
        return view('admin.controle_service.index', compact('zones', 'services', 'date', 'isLocked'));
    }

    public function valider(Request $request, Zone $zone)
    {
        $date = $request->input('date', now()->toDateString());
        
        if (Carbon::parse($date)->startOfDay()->addHours(24)->isPast()) {
            return redirect()->back()->with('error', 'Le délai de 24h est dépassé. Modification interdite.');
        }
        
        ZoneService::updateOrCreate(
            ['zone_id' => $zone->id, 'date_service' => $date],
            ['statut' => 'servi', 'heure_service' => now()->toTimeString(), 'observation' => null]
        );
        
        return redirect()->back()->with('success', 'Service validé.');
    }

    public function signaler(Request $request, Zone $zone)
    {
        $date = $request->input('date', now()->toDateString());
        
        if (Carbon::parse($date)->startOfDay()->addHours(24)->isPast()) {
            return redirect()->back()->with('error', 'Le délai de 24h est dépassé. Modification interdite.');
        }
        
        $request->validate([
            'observation' => 'required|string|max:1000'
        ]);
        
        ZoneService::updateOrCreate(
            ['zone_id' => $zone->id, 'date_service' => $date],
            ['statut' => 'non_servi', 'heure_service' => null, 'observation' => $request->observation]
        );
        
        return redirect()->back()->with('success', 'Signalement enregistré.');
    }
}
