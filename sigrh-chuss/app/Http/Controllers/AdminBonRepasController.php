<?php

namespace App\Http\Controllers;

use App\Models\BonRepas;
use App\Support\EnvoiBonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminBonRepasController extends Controller
{
    public function __construct(private readonly EnvoiBonService $envoiBonService) {}

    /**
     * Affiche le bon de repas et son QR code à l'écran.
     */
    public function show(BonRepas $bon): View
    {
        $bon->load('declarationJour.beneficiaire.service');

        $qr = QrCode::size(260)->generate($bon->code_unique);

        $aNumeroWhatsapp = (bool) $this->envoiBonService->numeroWhatsappInternational($bon);

        return view('bons.show', [
            'bon' => $bon,
            'qr' => $qr,
            'aNumeroWhatsapp' => $aNumeroWhatsapp,
            'routeTelecharger' => 'admin.bons.telecharger',
            'routeEnvoyerEmail' => 'admin.bons.envoyer-email',
            'routeEnvoyerWhatsapp' => 'admin.bons.envoyer-whatsapp',
            'retourUrl' => route('admin.declarations.index'),
            'retourLabel' => 'Retour à la validation des déclarations',
        ]);
    }

    /**
     * Télécharge le QR code du bon au format SVG.
     */
    public function telecharger(BonRepas $bon): Response
    {
        $qr = QrCode::size(400)->generate($bon->code_unique);

        return response($qr, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="bon-'.$bon->code_unique.'.svg"',
        ]);
    }

    /**
     * Envoie le bon (QR joint) par email au bénéficiaire.
     */
    public function envoyerEmail(BonRepas $bon): RedirectResponse
    {
        return back()->with('status', $this->envoiBonService->envoyerEmail($bon));
    }

    /**
     * Marque le bon comme envoyé par WhatsApp et redirige vers le lien
     * click-to-chat pré-rempli (envoi effectif fait manuellement par l'admin).
     */
    public function envoyerWhatsapp(BonRepas $bon): RedirectResponse
    {
        $lien = $this->envoiBonService->marquerEtLienWhatsapp($bon);

        if (! $lien) {
            return back()->with('status', 'Aucun numéro WhatsApp renseigné pour ce bénéficiaire.');
        }

        return redirect()->away($lien);
    }
}
