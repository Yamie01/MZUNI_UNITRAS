<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAdvertisement extends Model
{
    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'vehicle_id',
        'owner_id',
        'title',
        'slug',
        'description',
        'ad_type',
        'trip_type',
        'from_location',
        'from_latitude',
        'from_longitude',
        'to_location',
        'to_latitude',
        'to_longitude',
        'departure_time',
        'arrival_time',
        'return_departure_time',
        'return_arrival_time',
        'price',
        'price_per_extra_km',
        'total_seats',
        'available_seats',
        'minimum_seats',
        'maximum_seats',
        'route_points',
        'pickup_points',
        'dropoff_points',
        'status',
        'is_featured',
        'view_count',
        'booking_count',
        'is_recurring',
        'recurring_days',
        'recurring_start_date',
        'recurring_end_date',
        'terms_conditions',
        'cancellation_policy',
        'images',
        'amenities'
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
        'return_departure_time' => 'datetime',
        'return_arrival_time' => 'datetime',
        'price' => 'decimal:2',
        'price_per_extra_km' => 'decimal:2',
        'route_points' => 'array',
        'pickup_points' => 'array',
        'dropoff_points' => 'array',
        'recurring_days' => 'array',
        'images' => 'array',
        'amenities' => 'array',
        'is_featured' => 'boolean',
        'is_recurring' => 'boolean'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Advertisement belongs to a vehicle
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Advertisement owner
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Bookings for this advertisement
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Active advertisements
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
                     ->where('departure_time', '>', now())
                     ->where('available_seats', '>', 0);
    }

    /**
     * Ride sharing ads
     */
    public function scopeRideShare($query)
    {
        return $query->where('ad_type', 'ride_share');
    }

    /**
     * Taxi ads
     */
    public function scopeTaxi($query)
    {
        return $query->where('ad_type', 'taxi');
    }

    /**
     * Bus ads
     */
    public function scopeBus($query)
    {
        return $query->where('ad_type', 'bus');
    }

    /**
     * Bike sharing ads
     */
    public function scopeBikeShare($query)
    {
        return $query->where('ad_type', 'bike_share');
    }
}