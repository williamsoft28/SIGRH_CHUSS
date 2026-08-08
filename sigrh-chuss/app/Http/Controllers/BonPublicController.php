<?php

namespace App\Http\Controllers;

use App\Models\BonRepas;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BonPublicController extends Controller
{
    /**
     * Page publique (sans authentification) d'un bon de repas, accessible via
     * son code_unique — un lien à usage de partage par email/WhatsApp, pas
     * une ressource devinable. Affiche le QR en grand, prêt à scanner dès
     * l'ouverture du lien (limite technique de WhatsApp : un lien wa.me ne
     * peut pré-remplir que du texte, jamais une image).
     */
    public function show(string $codeUnique): View
    {
        $bon = BonRepas::where('code_unique', $codeUnique)
            ->with('declarationJour.beneficiaire.service')
            ->firstOrFail();

        $qr = QrCode::size(280)->generate($bon->code_unique);

        return view('bons.public', compact('bon', 'qr'));
    }

    /**
     * Télécharge le QR code du bon (accessible sans authentification, pour
     * que le bénéficiaire puisse l'enregistrer sur son téléphone).
     */
    public function telecharger(string $codeUnique): Response
    {
        $bon = BonRepas::where('code_unique', $codeUnique)->firstOrFail();

        $qr = QrCode::size(400)->generate($bon->code_unique);

        return response((string) $qr, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="bon-repas-'.$bon->code_unique.'.svg"',
        ]);
    }
}
