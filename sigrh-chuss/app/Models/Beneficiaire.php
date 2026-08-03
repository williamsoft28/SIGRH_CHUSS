<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'service_id',
    'regime_special_id',
    'nom',
    'categorie',
    'numero_whatsapp',
    'type',
])]
class Beneficiaire extends Model
{
    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return BelongsTo<RegimeSpecial, $this>
     */
    public function regimeSpecial(): BelongsTo
    {
        return $this->belongsTo(RegimeSpecial::class);
    }

    /**
     * @return HasMany<DeclarationJour, $this>
     */
    public function declarationJours(): HasMany
    {
        return $this->hasMany(DeclarationJour::class);
    }
}
