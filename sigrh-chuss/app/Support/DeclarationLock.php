<?php

namespace App\Support;

use App\Models\Derogation;
use App\Models\Service;
use Carbon\Carbon;

/**
 * Centralise la règle de verrouillage de la saisie des déclarations journalières :
 * ouverte jusqu'à l'heure limite (09h00), puis fermée sauf dérogation autorisée
 * pour le service et la date concernés.
 */
class DeclarationLock
{
    public const HEURE_LIMITE = '09:00:00';

    public static function limite(Carbon $date): Carbon
    {
        return Carbon::parse($date->toDateString().' '.self::HEURE_LIMITE);
    }

    public static function avantHeureLimite(Carbon $date): bool
    {
        return now()->lt(self::limite($date));
    }

    public static function derogationAutorisee(Service $service, Carbon $date): bool
    {
        return Derogation::where('service_id', $service->id)
            ->whereDate('date', $date->toDateString())
            ->where('statut', 'autorisee')
            ->exists();
    }

    /**
     * La saisie est ouverte avant l'heure limite, ou à tout moment si une
     * dérogation a été autorisée pour ce service et cette date.
     */
    public static function estOuverte(Service $service, Carbon $date): bool
    {
        return self::avantHeureLimite($date) || self::derogationAutorisee($service, $date);
    }
}
