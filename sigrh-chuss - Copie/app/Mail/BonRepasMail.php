<?php

namespace App\Mail;

use App\Models\BonRepas;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BonRepasMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public BonRepas $bon) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre bon de repas — SIGRH CHUSS',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $beneficiaire = $this->bon->declarationJour->beneficiaire;

        return new Content(
            markdown: 'emails.bons.envoi',
            with: [
                'nom' => $beneficiaire->nom,
                'service' => $beneficiaire->service?->nom ?? '—',
                'repas' => collect($this->bon->declarationJour->repas ?? [])
                    ->map(fn ($r) => str_replace('_', ' ', $r))
                    ->implode(', '),
                'dateDebut' => $this->bon->date_debut->format('d/m/Y'),
                'dateFin' => $this->bon->date_fin->format('d/m/Y'),
                'codeUnique' => $this->bon->code_unique,
                'lienPublic' => route('bons.public', $this->bon->code_unique),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // QrCode::generate() renvoie un Illuminate\Support\HtmlString (pratique pour
        // l'affichage Blade en {!! !!}) ; Symfony Mime exige une chaîne brute pour
        // le contenu d'une pièce jointe, d'où le cast explicite.
        $svg = (string) QrCode::size(300)->generate($this->bon->code_unique);

        return [
            Attachment::fromData(fn () => $svg, 'bon-repas-qr.svg')
                ->withMime('image/svg+xml'),
        ];
    }
}
