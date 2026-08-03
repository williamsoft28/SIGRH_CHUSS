<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'bon_repas_id',
    'type_repas',
    'date_repas',
    'date_heure_scan',
    'statut',
])]
class Consommation extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_repas' => 'date',
            'date_heure_scan' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<BonRepas, $this>
     */
    public function bonRepas(): BelongsTo
    {
        return $this->belongsTo(BonRepas::class);
    }
}
