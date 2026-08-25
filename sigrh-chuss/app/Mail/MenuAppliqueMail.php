<?php

namespace App\Mail;

use App\Models\Menu;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class MenuAppliqueMail extends Mailable
{
    use Queueable;

    public function __construct(public Menu $menu) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau menu validé — SIGRH CHUSS',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.menus.applique',
            with: [
                'menu' => $this->menu,
                'lien' => route('menus.telecharger', $this->menu),
            ],
        );
    }
}
