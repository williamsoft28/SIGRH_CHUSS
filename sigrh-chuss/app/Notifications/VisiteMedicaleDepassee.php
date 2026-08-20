<?php

namespace App\Notifications;

use App\Models\VisiteMedicale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisiteMedicaleDepassee extends Notification
{
    use Queueable;

    public $visite;

    public function __construct(VisiteMedicale $visite)
    {
        $this->visite = $visite;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // On stocke en base de données pour affichage dans l'app
    }

    public function toArray(object $notifiable): array
    {
        return [
            'visite_id' => $this->visite->id,
            'agent_id' => $this->visite->user_id,
            'agent_nom' => $this->visite->user->prenom . ' ' . $this->visite->user->nom,
            'date_programmee' => $this->visite->date_programmee->format('d/m/Y'),
            'message' => "La visite médicale programmée le {$this->visite->date_programmee->format('d/m/Y')} pour {$this->visite->user->prenom} {$this->visite->user->nom} est dépassée et n'a pas été réalisée.",
        ];
    }
}
