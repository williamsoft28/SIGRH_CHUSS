<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Droit attendu : pour un bon donné, autorise un type de repas précis à une
 * date précise (calendrier hebdomadaire coché par le SUS). Utilisé par le
 * contrôle au scan pour valider finement, jour par jour et repas par repas.
 */
#[Fillable(['bon_repas_id', 'date', 'type_repas'])]
class DroitRepas extends Model
{
    protected $table = 'droit_repas';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
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
