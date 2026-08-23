<?php

namespace App\Http\Controllers;

use App\Support\VerificationBonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ControleurScanController extends Controller
{
    /**
     * Écran de scan des bons au réfectoire.
     */
    public function index(): View
    {
        return view('controleur.scanner');
    }

    /**
     * Vérifie un code scanné et enregistre la consommation si autorisée.
     */
    public function verifier(Request $request, VerificationBonService $service): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:255'],
        ]);

        return response()->json($service->verifier($data['code']));
    }
}
