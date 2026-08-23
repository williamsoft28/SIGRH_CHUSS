<?php

namespace App\Support;

use App\Mail\BonRepasMail;
use App\Models\BonRepas;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Logique d'envoi d'un bon de repas (email et WhatsApp), partagée entre les
 * écrans SUS et administrateur.
 */
class EnvoiBonService
{
    /**
     * Envoie le bon (QR joint) par email au bénéficiaire. Met à jour canal_envoi
     * en cas de succès. Retourne un message à afficher à l'utilisateur.
     */
    public function envoyerEmail(BonRepas $bon): string
    {
        $bon->loadMissing('declarationJour.beneficiaire');
        $beneficiaire = $bon->declarationJour->beneficiaire;

        if (empty($beneficiaire->email)) {
            return "Aucun email renseigné pour {$beneficiaire->nom} — impossible d'envoyer.";
        }

        try {
            Mail::to($beneficiaire->email)->send(new BonRepasMail($bon));
            $bon->update(['canal_envoi' => 'email']);

            return "Bon envoyé par email à {$beneficiaire->email}.";
        } catch (\Throwable $e) {
            Log::error("Échec de l'envoi du bon {$bon->code_unique} par email : {$e->getMessage()}");

            return "L'envoi par email a échoué. Réessaie ou transmets le bon autrement.";
        }
    }

    /**
     * Marque le bon comme envoyé par WhatsApp et retourne le lien click-to-chat
     * pré-rempli (envoi effectif fait manuellement via WhatsApp).
     */
    public function marquerEtLienWhatsapp(BonRepas $bon): ?string
    {
        $bon->loadMissing('declarationJour.beneficiaire');

        $numero = $this->numeroWhatsappInternational($bon);

        if (! $numero) {
            return null;
        }

        $bon->update(['canal_envoi' => 'whatsapp']);

        return 'https://wa.me/'.$numero.'?text='.rawurlencode($this->messageWhatsapp($bon));
    }

    public function numeroWhatsappInternational(BonRepas $bon): ?string
    {
        $bon->loadMissing('declarationJour.beneficiaire');

        $numero = $bon->declarationJour->beneficiaire->numero_whatsapp ?? null;

        if (! $numero) {
            return null;
        }

        $numero = preg_replace('/\D+/', '', $numero);

        return $numero ?: null;
    }

    private function messageWhatsapp(BonRepas $bon): string
    {
        $beneficiaire = $bon->declarationJour->beneficiaire;
        $repas = collect($bon->declarationJour->repas ?? [])
            ->map(fn ($r) => str_replace('_', ' ', $r))
            ->implode(', ');

        $periode = $bon->date_debut->format('d/m/Y');
        if (! $bon->date_debut->equalTo($bon->date_fin)) {
            $periode .= ' au '.$bon->date_fin->format('d/m/Y');
        }

        return "Bonjour {$beneficiaire->nom}, votre bon de repas ({$repas}) est prêt pour la période du {$periode}. "
            .'Cliquez sur ce lien pour voir votre QR code à présenter au réfectoire : '
            .route('bons.public', $bon->code_unique);
    }
}
