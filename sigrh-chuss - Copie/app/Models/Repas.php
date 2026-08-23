<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['menu_jour_id', 'type_repas', 'plat_id', 'sauce_id', 'viande_id', 'dessert_id'])]
class Repas extends Model
{
    protected $table = 'repas';

    /**
     * @return BelongsTo<MenuJour, $this>
     */
    public function menuJour(): BelongsTo
    {
        return $this->belongsTo(MenuJour::class);
    }

    /**
     * @return BelongsTo<Plat, $this>
     */
    public function plat(): BelongsTo
    {
        return $this->belongsTo(Plat::class);
    }

    /**
     * @return BelongsTo<Sauce, $this>
     */
    public function sauce(): BelongsTo
    {
        return $this->belongsTo(Sauce::class);
    }

    /**
     * @return BelongsTo<Viande, $this>
     */
    public function viande(): BelongsTo
    {
        return $this->belongsTo(Viande::class);
    }

    /**
     * Le dessert est un plat (table `plats`, type = dessert), référencé via dessert_id.
     *
     * @return BelongsTo<Plat, $this>
     */
    public function dessert(): BelongsTo
    {
        return $this->belongsTo(Plat::class, 'dessert_id');
    }
}
