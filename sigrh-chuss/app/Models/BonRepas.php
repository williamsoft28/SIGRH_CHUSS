<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'declaration_jour_id',
    'code_unique',
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
}
