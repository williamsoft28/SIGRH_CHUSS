<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneService extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'zone_id',
        'date_service',
        'statut',
        'heure_service',
        'observation'
    ];
    
    protected $casts = [
        'date_service' => 'date',
    ];
    
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
