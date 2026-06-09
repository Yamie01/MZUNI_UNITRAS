<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'owner_id',
        'vehicle_type',
        'registration_number',
        'model',
        'make',
        'year',
        'color',
        'capacity',
        'features',
        'price_per_km',
        'price_per_day',
        'insurance_number',
        'insurance_expiry',
        'fuel_type',
        'fuel_efficiency',
        'status',
        'is_approved',
        'rejection_reason',
        'documents',
        'current_latitude',
        'current_longitude',
        'is_active',
        'description'
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'features' => 'array',
        'documents' => 'array',
        'price_per_km' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'fuel_efficiency' => 'decimal:2',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'insurance_expiry' => 'date',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8'
    ];

    /**
     * Vehicle Owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Vehicle Advertisements
     */
    public function advertisements()
    {
        return $this->hasMany(VehicleAdvertisement::class);
    }

    /**
     * Vehicle Bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Vehicle Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get active advertisement
     */
    public function getActiveAdvertisementAttribute()
    {
        return $this->advertisements()
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->first();
    }

    // In app/Models/Vehicle.php
    public function locations()
    {
        return $this->morphMany(VehicleLocation::class, 'trackable');
    }

    public function latestLocation()
    {
        return $this->morphOne(VehicleLocation::class, 'trackable')->latest('recorded_at');
    }
}