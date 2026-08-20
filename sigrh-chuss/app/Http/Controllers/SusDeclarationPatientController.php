<?php

namespace App\Http\Controllers;

use App\Models\DeclarationPatient;
use App\Models\RegimeSpecial;
use App\Support\DeclarationLock;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SusDeclarationPatientController extends Controller
{
    /**
     * Liste des déclarations de patients pour le service du SUS.
     */
    public function index(Request $request): View
    {
        $service = $request->user()->service;

        $declarations = DeclarationPatient::query()
            ->where('service_id', $service?->id)
            ->with('regimeSpecial')
            ->orderByDesc('date_repas')
            ->paginate(20);

        return view('declarations_patients.sus.index', compact('declarations'));
    }

    /**
     * Formulaire pour déclarer le nombre de plats par régime pour une date.
     */
    public function create(Request $request): View
    {
        $service = $request->user()->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $date = $request->date('date') ?? today();

        $regimes = RegimeSpecial::orderBy('libelle')->get();

        $declarationsExistantes = DeclarationPatient::where('service_id', $service->id)
            ->whereDate('date_repas', $date->toDateString())
            ->get()
            ->keyBy('regime_special_id');

        $ouverte = DeclarationLock::estOuverte($service, $date);

        return view('declarations_patients.sus.creer', compact('regimes', 'date', 'ouverte', 'declarationsExistantes'));
    }

    /**
     * Enregistre les déclarations de patients.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $service = $user->service;
        abort_unless($service, 403, "Votre compte n'est rattaché à aucun service.");

        $sus = $user->sus;
        abort_unless($sus, 403, "Votre compte SUS n'est pas configuré.");

        $date = Carbon::parse($request->input('date', today()->toDateString()));

        if (! DeclarationLock::estOuverte($service, $date)) {
            return back()->withErrors([
                'date' => "La saisie est verrouillée pour cette date (après 09h00). Faites une demande de dérogation.",
            ])->withInput();
        }

        $regimesInput = $request->input('regimes', []);
        $maladesInput = $request->input('malades', []);

        $erreurs = [];
        $enregistrees = 0;

        foreach ($regimesInput as $regimeId => $nombre) {
            $nombrePlats = (int) $nombre;
            $nombreMalades = (int) ($maladesInput[$regimeId] ?? 0);

            if ($nombrePlats < 0 || $nombreMalades < 0) {
                $erreurs[] = "Les valeurs pour le régime ID $regimeId sont invalides.";
                continue;
            }

            if ($nombrePlats > 0 || $nombreMalades > 0) {
                DeclarationPatient::updateOrCreate(
                    [
                        'service_id' => $service->id,
                        'date_repas' => $date->toDateString(),
                        'regime_special_id' => $regimeId,
                    ],
                    [
                        'sus_id' => $sus->id,
                        'nombre_plats' => $nombrePlats,
                        'nombre_malades' => $nombreMalades,
                    ]
                );
                $enregistrees++;
            } else {
                // Si les deux nombres sont 0, on peut supprimer l'enregistrement s'il existait
                DeclarationPatient::where('service_id', $service->id)
                    ->whereDate('date_repas', $date->toDateString())
                    ->where('regime_special_id', $regimeId)
                    ->delete();
            }
        }

        if (!empty($erreurs)) {
            return back()->withErrors($erreurs)->withInput();
        }

        return redirect()
            ->route('beneficiaires.declarations-patients.index')
            ->with('status', 'Déclarations des patients enregistrées avec succès.');
    }
}
