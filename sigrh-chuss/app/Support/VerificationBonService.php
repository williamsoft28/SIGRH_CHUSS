<?php

namespace App\Support;

use App\Models\BonRepas;
use App\Models\Consommation;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * Cœur du contrôle au réfectoire : à partir du code_unique scanné et de
 * l'heure du scan, détermine si le repas peut être servi.
 */
class VerificationBonService
{
    /**
     * @return array{autorise: bool, motif: ?string, beneficiaire: ?string, type_repas: ?string}
     */
    public function verifier(string $codeUnique, ?CarbonInterface $maintenant = null): array
    {
        $maintenant = $maintenant ?? now();

        $bon = BonRepas::where('code_unique', trim($codeUnique))
            ->with(['declarationJour.beneficiaire', 'droits'])
            ->first();

        if (! $bon) {
            return $this->refus('Bon invalide.');
        }

        $typeRepas = $this->determinerTypeRepas($maintenant);

        if (! $typeRepas) {
            return $this->refus('Aucun repas en cours à cette heure.');
        }

        $dateJour = $maintenant->toDateString();

        if ($bon->droits->isNotEmpty()) {
            // Calendrier hebdomadaire (nouveau mécanisme) : chaque jour/repas doit
            // correspondre à une case explicitement cochée par le SUS.
            $droitPrevu = $bon->droits->contains(
                fn ($droit) => $droit->date->toDateString() === $dateJour && $droit->type_repas === $typeRepas
            );

            if (! $droitPrevu) {
                return $this->refus('Repas non prévu ce jour.');
            }
        } elseif ($dateJour < $bon->date_debut->toDateString() || $dateJour > $bon->date_fin->toDateString()) {
            // Bon généré via l'ancien mécanisme (période + liste de repas uniforme).
            return $this->refus('Jour non autorisé pour ce bon.');
        }

        $dejaConsomme = Consommation::where('bon_repas_id', $bon->id)
            ->where('type_repas', $typeRepas)
            ->whereDate('date_repas', $dateJour)
            ->exists();

        if ($dejaConsomme) {
            return $this->refus('Repas déjà consommé pour ce jour.');
        }

        Consommation::create([
            'bon_repas_id' => $bon->id,
            'type_repas' => $typeRepas,
            'date_repas' => $dateJour,
            'date_heure_scan' => $maintenant,
            'statut' => 'consomme',
        ]);

        return [
            'autorise' => true,
            'motif' => null,
            'beneficiaire' => $bon->declarationJour->beneficiaire->nom,
            'type_repas' => str_replace('_', ' ', $typeRepas),
        ];
    }

    /**
     * Détermine le repas en cours à partir des plages horaires configurées
     * (config/repas.php). Retourne null si l'heure ne correspond à aucun repas.
     */
    private function determinerTypeRepas(CarbonInterface $maintenant): ?string
    {
        foreach (config('repas.plages', []) as $type => $plage) {
            $debut = Carbon::parse($maintenant->toDateString().' '.$plage['debut']);
            $fin = Carbon::parse($maintenant->toDateString().' '.$plage['fin']);

            if ($maintenant->between($debut, $fin)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * @return array{autorise: bool, motif: ?string, beneficiaire: ?string, type_repas: ?string}
     */
    private function refus(string $motif): array
    {
        return [
            'autorise' => false,
            'motif' => $motif,
            'beneficiaire' => null,
            'type_repas' => null,
        ];
    }
}
