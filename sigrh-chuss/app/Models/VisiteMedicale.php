<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisiteMedicale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_programmee',
        'statut',
        'resultat',
        'date_realisation'
    ];

    protected $casts = [
        'date_programmee' => 'date',
        'date_realisation' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
