<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code_regime', 'libelle', 'type_regime'])]
class RegimeSpecial extends Model
{
    /**
     * @return HasMany<Beneficiaire, $this>
     */
    public function beneficiaires(): HasMany
    {
        return $this->hasMany(Beneficiaire::class);
    }
}
