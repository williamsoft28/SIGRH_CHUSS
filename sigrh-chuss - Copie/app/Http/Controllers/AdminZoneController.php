<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\Http\Request;

class AdminZoneController extends Controller
{
    public function index()
    {
        $zones = Zone::orderBy('nom')->get();
        return view('admin.zones.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:zones,nom',
            'emplacement' => 'nullable|string|max:255',
        ]);

        Zone::create($request->only(['nom', 'emplacement']));

        return redirect()->route('admin.zones.index')->with('success', 'Zone créée avec succès.');
    }

    public function update(Request $request, Zone $zone)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:zones,nom,' . $zone->id,
            'emplacement' => 'nullable|string|max:255',
        ]);

        $zone->update($request->only(['nom', 'emplacement']));

        return redirect()->route('admin.zones.index')->with('success', 'Zone mise à jour avec succès.');
    }

    public function destroy(Zone $zone)
    {
        $zone->delete();
        return redirect()->route('admin.zones.index')->with('success', 'Zone supprimée avec succès.');
    }
}
