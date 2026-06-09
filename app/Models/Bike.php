<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bike extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bike_code', 'brand', 'model', 'type', 'color', 'year',
        'price_per_hour', 'price_per_day', 'deposit_amount', 'status',
        'description', 'features', 'images', 'qr_code',
        'current_latitude', 'current_longitude', 'last_maintenance_date',
        'total_rentals', 'total_revenue', 'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'images' => 'array',
        'price_per_hour' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'last_maintenance_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function rentals()
    {
        return $this->hasMany(BikeRental::class);
    }

    public function activeRentals()
    {
        return $this->hasMany(BikeRental::class)->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    public function getCurrentStatusAttribute()
    {
        if ($this->activeRentals()->exists()) {
            return 'rented';
        }
        return $this->status;
    }

    public function activeRental()
    {
        return $this->hasOne(BikeRental::class)->where('status', 'active')->latest();
    }

    
    public function latestLocation()
    {
        return $this->morphOne(VehicleLocation::class, 'trackable')->latest('recorded_at');
    }

    public function locations()
    {
        return $this->morphMany(VehicleLocation::class, 'trackable');
    }
}