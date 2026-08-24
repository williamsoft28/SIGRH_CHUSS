<?php

namespace App\Http\Controllers;

use App\Models\BonRepas;
use App\Models\DeclarationJour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDeclarationController extends Controller
{
    /**
     * Déclarations en attente de validation (en saisie ou verrouillées) pour une date donnée.
     */
    public function index(Request $request): View
    {
        $date = $request->date('date') ?? today();

        $declarations = DeclarationJour::query()
            ->whereDate('date_repas', $date->toDateString())
            ->whereIn('statut', ['en_saisie', 'verrouillee'])
            ->with(['beneficiaire.service', 'sus'])
            ->orderBy('statut')
            ->get();

        return view('admin.declarations.index', compact('declarations', 'date'));
    }

    /**
     * Valide une déclaration et génère le bon de repas (avec QR code) correspondant.
     */
    public function valider(Request $request, DeclarationJour $declaration): RedirectResponse
    {
        abort_if($declaration->statut === 'validee', 409, 'Cette déclaration est déjà validée.');

        $data = $request->validate([
            'canal_envoi' => ['required', 'in:whatsapp,email,tiers'],
        ]);

        $bon = DB::transaction(function () use ($declaration, $data) {
            $declaration->update(['statut' => 'validee']);

            return $declaration->bonRepas()->create([
                'code_unique' => BonRepas::genererCodeUnique(),
                'code_court' => BonRepas::genererCodeCourt($declaration->beneficiaire->service, $declaration->repas ?? []),
                'type_periode' => $declaration->type_periode,
                'date_debut' => $declaration->date_debut,
                'date_fin' => $declaration->date_fin,
                'canal_envoi' => $data['canal_envoi'],
                'date_emission' => now(),
            ]);
        });

        return redirect()
            ->route('admin.bons.show', $bon)
            ->with('status', 'Déclaration validée et bon de repas généré.');
    }
}
