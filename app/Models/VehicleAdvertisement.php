<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAdvertisement extends Model
{
    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        // Foreign keys
        'vehicle_id',
        'owner_id',
        'from_location_id',      // 🔥 NEW – references locations.id
        'to_location_id',        // 🔥 NEW – references locations.id

        // General info
        'title',
        'slug',
        'description',
        'ad_type',
        'trip_type',

        // Legacy text locations (kept for fallback; will be removed later)
        'from_location',
        'from_latitude',
        'from_longitude',
        'to_location',
        'to_latitude',
        'to_longitude',

        // Dates & times
        'departure_time',
        'arrival_time',
        'return_departure_time',
        'return_arrival_time',

        // Pricing & seats
        'price',
        'price_per_extra_km',
        'total_seats',
        'available_seats',
        'minimum_seats',
        'maximum_seats',

        // Route & stops
        'route_points',
        'pickup_points',
        'dropoff_points',

        // Status & flags
        'status',
        'is_featured',
        'is_recurring',
        'recurring_days',
        'recurring_start_date',
        'recurring_end_date',

        // Policies & extras
        'terms_conditions',
        'cancellation_policy',
        'images',
        'amenities',

        // Statistics
        'view_count',
        'booking_count',

        'trip_status',
        'trip_started_at',
        'trip_completed_at',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'departure_time'          => 'datetime',
        'arrival_time'            => 'datetime',
        'return_departure_time'   => 'datetime',
        'return_arrival_time'     => 'datetime',
        'price'                   => 'decimal:2',
        'price_per_extra_km'      => 'decimal:2',
        'route_points'            => 'array',
        'pickup_points'           => 'array',
        'dropoff_points'          => 'array',
        'recurring_days'          => 'array',
        'images'                  => 'array',
        'amenities'               => 'array',
        'is_featured'             => 'boolean',
        'is_recurring'            => 'boolean',
        'view_count'              => 'integer',
        'booking_count'           => 'integer',
        'route', // ← ADD THIS
        'image', // ← ADD THIS
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Vehicle that owns this advertisement.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Owner (user) of this advertisement.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Bookings made for this advertisement.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Location: pickup point (using new foreign key).
     */
    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    /**
     * Location: destination (using new foreign key).
     */
    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope to get only active (approved, future, with seats) advertisements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
                     ->where('departure_time', '>', now())
                     ->where('available_seats', '>', 0);
    }


    /**
     * Scope for ride‑sharing ads.
     */
    public function scopeRideShare($query)
    {
        return $query->where('ad_type', 'ride_share');
    }

    /**
     * Scope for taxi ads.
     */
    public function scopeTaxi($query)
    {
        return $query->where('ad_type', 'taxi');
    }

    /**
     * Scope for bus ads.
     */
    public function scopeBus($query)
    {
        return $query->where('ad_type', 'bus');
    }

    /**
     * Scope for bike‑sharing ads.
     */
    public function scopeBikeShare($query)
    {
        return $query->where('ad_type', 'bike_share');
    }

    /**
 * Scope a query to only include available rides.
 */
public function scopeAvailable($query)
{
    return $query->where('status', 'approved')
        ->where('departure_time', '>', now())
        ->where('available_seats', '>', 0)
        ->where(function($q) {
            $q->whereNull('trip_status')
              ->orWhere('trip_status', 'scheduled');
        });
}
    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators (optional)
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return 'MWK ' . number_format($this->price, 0);
    }
}