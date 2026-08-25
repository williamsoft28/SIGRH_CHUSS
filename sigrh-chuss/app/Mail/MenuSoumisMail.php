<?php

namespace App\Mail;

use App\Models\Menu;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MenuSoumisMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Menu $menu) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nouveau menu soumis — SIGRH CHUSS');
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.menus.soumis', with: [
            'menu' => $this->menu,
            'lien' => route('prestataire.menus.show', $this->menu),
        ]);
    }
}
