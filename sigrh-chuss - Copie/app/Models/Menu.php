<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'numero_semaine',
    'annee',
    'date_debut',
    'date_fin',
    'statut',
    'date_soumission',
    'date_validation',
    'nb_modifications',
])]
class Menu extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'date_soumission' => 'datetime',
            'date_validation' => 'datetime',
        ];
    }

    /**
     * @return HasMany<MenuJour, $this>
     */
    public function menuJours(): HasMany
    {
        return $this->hasMany(MenuJour::class);
    }

    /**
     * @return HasMany<Observation, $this>
     */
    public function observations(): HasMany
    {
        return $this->hasMany(Observation::class);
    }
}
