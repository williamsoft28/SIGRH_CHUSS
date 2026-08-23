<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['sus_id', 'service_id', 'date_repas', 'regime_special_id', 'nombre_plats', 'nombre_malades'])]
class DeclarationPatient extends Model
{
    /**
     * @return BelongsTo<Sus, $this>
     */
    public function sus(): BelongsTo
    {
        return $this->belongsTo(Sus::class);
    }

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
}
