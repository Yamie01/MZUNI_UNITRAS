<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name',
        'lat',
        'lng',
    ];

    // Relationships
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'from_location_id');
    }

    public function advertisements()
    {
        return $this->hasMany(VehicleAdvertisement::class, 'from_location_id');
    }
}