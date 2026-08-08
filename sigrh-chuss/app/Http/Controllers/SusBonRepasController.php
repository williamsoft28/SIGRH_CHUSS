<?php

namespace App\Http\Controllers;

use App\Models\BonRepas;
use App\Support\EnvoiBonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SusBonRepasController extends Controller
{
    public function __construct(private readonly EnvoiBonService $envoiBonService) {}

    /**
     * Affiche le bon de repas et son QR code à l'écran, pour un bénéficiaire
     * du service du SUS connecté.
     */
    public function show(Request $request, BonRepas $bon): View
    {
        $this->authorizeAccess($request, $bon);

        $bon->load('declarationJour.beneficiaire.service');

        $qr = QrCode::size(260)->generate($bon->code_unique);

        $aNumeroWhatsapp = (bool) $this->envoiBonService->numeroWhatsappInternational($bon);

        return view('bons.show', [
            'bon' => $bon,
            'qr' => $qr,
            'aNumeroWhatsapp' => $aNumeroWhatsapp,
            'routeTelecharger' => 'beneficiaires.bons.telecharger',
            'routeEnvoyerEmail' => 'beneficiaires.bons.envoyer-email',
            'routeEnvoyerWhatsapp' => 'beneficiaires.bons.envoyer-whatsapp',
            'retourUrl' => route('beneficiaires.index'),
            'retourLabel' => 'Retour à mes bénéficiaires',
        ]);
    }

    public function telecharger(Request $request, BonRepas $bon): Response
    {
        $this->authorizeAccess($request, $bon);

        $qr = QrCode::size(400)->generate($bon->code_unique);

        return response($qr, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="bon-'.$bon->code_unique.'.svg"',
        ]);
    }

    public function envoyerEmail(Request $request, BonRepas $bon): RedirectResponse
    {
        $this->authorizeAccess($request, $bon);

        return back()->with('status', $this->envoiBonService->envoyerEmail($bon));
    }

    public function envoyerWhatsapp(Request $request, BonRepas $bon): RedirectResponse
    {
        $this->authorizeAccess($request, $bon);

        $lien = $this->envoiBonService->marquerEtLienWhatsapp($bon);

        if (! $lien) {
            return back()->with('status', 'Aucun numéro WhatsApp renseigné pour ce bénéficiaire.');
        }

        return redirect()->away($lien);
    }

    /**
     * Un SUS ne peut agir que sur les bons des bénéficiaires de son propre service.
     */
    private function authorizeAccess(Request $request, BonRepas $bon): void
    {
        $bon->loadMissing('declarationJour.beneficiaire');

        abort_unless(
            $bon->declarationJour->beneficiaire->service_id === $request->user()->service_id,
            403
        );
    }
}
