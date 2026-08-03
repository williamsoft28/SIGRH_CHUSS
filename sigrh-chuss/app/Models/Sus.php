<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['service_id', 'nom', 'login'])]
class Sus extends Model
{
    protected $table = 'sus';

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return HasMany<DeclarationJour, $this>
     */
    public function declarationJours(): HasMany
    {
        return $this->hasMany(DeclarationJour::class);
    }
}
