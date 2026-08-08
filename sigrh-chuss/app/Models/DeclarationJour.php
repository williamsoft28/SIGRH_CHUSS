<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'sus_id',
    'beneficiaire_id',
    'date_repas',
    'heure_limite',
    'type_periode',
    'date_debut',
    'date_fin',
    'repas',
    'statut',
    'deroge',
])]
class DeclarationJour extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_repas' => 'date',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'repas' => 'array',
            'deroge' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Sus, $this>
     */
    public function sus(): BelongsTo
    {
        return $this->belongsTo(Sus::class);
    }

    /**
     * @return BelongsTo<Beneficiaire, $this>
     */
    public function beneficiaire(): BelongsTo
    {
        return $this->belongsTo(Beneficiaire::class);
    }

    /**
     * @return HasOne<BonRepas, $this>
     */
    public function bonRepas(): HasOne
    {
        return $this->hasOne(BonRepas::class);
    }
}
