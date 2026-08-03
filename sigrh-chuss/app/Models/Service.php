<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code_service', 'nom', 'type_service'])]
class Service extends Model
{
    /**
     * @return HasMany<Sus, $this>
     */
    public function suses(): HasMany
    {
        return $this->hasMany(Sus::class);
    }

    /**
     * @return HasMany<Beneficiaire, $this>
     */
    public function beneficiaires(): HasMany
    {
        return $this->hasMany(Beneficiaire::class);
    }
}
