<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'service_id',
    'date',
    'heure_debut',
    'heure_fin',
    'statut',
    'motif',
    'demande_par_id',
    'autorisee_par_id',
])]
class Derogation extends Model
{
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
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function demandePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'demande_par_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function autoriseePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorisee_par_id');
    }
}
