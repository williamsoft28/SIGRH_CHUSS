<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'declaration_jour_id',
    'code_unique',
    'code_court',
    'type_periode',
    'date_debut',
    'date_fin',
    'canal_envoi',
    'date_emission',
])]
class BonRepas extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_emission' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<DeclarationJour, $this>
     */
    public function declarationJour(): BelongsTo
    {
        return $this->belongsTo(DeclarationJour::class);
    }

    /**
     * @return HasMany<Consommation, $this>
     */
    public function consommations(): HasMany
    {
        return $this->hasMany(Consommation::class);
    }

    /**
     * Droits attendus (jour + repas) du calendrier hebdomadaire, s'il y en a.
     *
     * @return HasMany<DroitRepas, $this>
     */
    public function droits(): HasMany
    {
        return $this->hasMany(DroitRepas::class);
    }

    /**
     * Génère un code_unique aléatoire et non devinable, garanti unique en base.
     */
    public static function genererCodeUnique(): string
    {
        do {
            $code = Str::random(48);
        } while (self::where('code_unique', $code)->exists());

        return $code;
    }

    /**
     * Génère un code court humainement lisible (ex: MI12AG)
     * - Préfixe: Initiales du service
     * - Chiffre: Aléatoire (100 à 999)
     * - Suffixe: Basé sur les repas demandés
     * 
     * @param \App\Models\Service $service
     * @param array $typesRepas Liste des types de repas (ex: ['dejeuner', 'diner'])
     */
    public static function genererCodeCourt(\App\Models\Service $service, array $typesRepas): string
    {
        // 1. Initiales du service (ex: "Médecine Interne" -> "MI")
        $mots = preg_split('/[\s\-]+/', trim($service->nom));
        $initiales = '';
        foreach ($mots as $mot) {
            if (strlen($mot) > 0) {
                // Pour extraire le premier caractère correctement même avec des accents
                $initiales .= mb_strtoupper(mb_substr($mot, 0, 1));
            }
        }
        if (empty($initiales)) {
            $initiales = 'X';
        }

        // 2. Détermination du suffixe selon les repas
        $suffixe = '';
        if (in_array('dejeuner', $typesRepas) || in_array('apres_midi', $typesRepas)) {
            $suffixe .= 'A'; // Après-midi
        }
        if (in_array('diner', $typesRepas) || in_array('garde', $typesRepas)) {
            $suffixe .= 'G'; // Garde
        }
        if (in_array('petit_dejeuner', $typesRepas)) {
            $suffixe .= 'M'; // Matin (si nécessaire plus tard)
        }
        if (empty($suffixe)) {
            $suffixe = 'X';
        }

        // 3. Boucle pour garantir l'unicité du chiffre
        do {
            $chiffre = rand(100, 999);
            $codeCourt = "{$initiales}{$chiffre}{$suffixe}";
        } while (self::where('code_court', $codeCourt)->exists());

        return $codeCourt;
    }
}
