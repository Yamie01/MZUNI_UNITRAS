<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleLocation extends Model
{
    protected $fillable = ['trackable_type', 'trackable_id', 'latitude', 'longitude', 'speed', 'heading', 'recorded_at'];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function trackable()
    {
        return $this->morphTo();
    }
}