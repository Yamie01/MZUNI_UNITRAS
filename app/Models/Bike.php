<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bike extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'bike_code',
        'brand',
        'model',
        'type',
        'color',
        'year',
        'price_per_hour',
        'price_per_day',
        'deposit_amount',
        'status',
        'description',
        'features',
        'images',
        'qr_code',
        'current_latitude',
        'current_longitude',
        'last_maintenance_date',
        'total_rentals',
        'total_revenue',
        'is_active',
        'location_id',
        'current_renter_id',
        'rate_per_minute',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
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
        'is_active' => 'boolean',
        'year' => 'integer',
        'total_rentals' => 'integer',
        'rate_per_minute' => 'decimal:2',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the rentals for this bike.
     */
    public function rentals()
    {
        return $this->hasMany(BikeRental::class);
    }

    /**
     * Get the active rental for this bike.
     */
    public function activeRental()
    {
        return $this->hasOne(BikeRental::class)->where('status', 'active')->latest();
    }

    /**
     * Get active rentals (multiple, though usually one).
     */
    public function activeRentals()
    {
        return $this->hasMany(BikeRental::class)->where('status', 'active');
    }

    /**
     * Get the current renter of this bike.
     */
    public function currentRenter()
    {
        return $this->belongsTo(User::class, 'current_renter_id');
    }

    /**
     * Get the location of this bike.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the latest location tracking record.
     */
    public function latestLocation()
    {
        return $this->morphOne(VehicleLocation::class, 'trackable')->latest('recorded_at');
    }

    /**
     * Get all location tracking records.
     */
    public function locations()
    {
        return $this->morphMany(VehicleLocation::class, 'trackable');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to only include available bikes.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')
            ->where('is_active', true);
    }

    /**
     * Scope a query to only include active/rented bikes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->orWhere('status', 'rented');
    }

    /**
     * Scope a query to only include bikes needing maintenance.
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get the current status (checks for active rental).
     */
    public function getCurrentStatusAttribute()
    {
        if ($this->activeRentals()->exists()) {
            return 'rented';
        }
        return $this->status;
    }

    /**
     * Get formatted price per hour.
     */
    public function getFormattedPricePerHourAttribute()
    {
        return 'MWK ' . number_format($this->price_per_hour, 2);
    }

    /**
     * Get formatted price per day.
     */
    public function getFormattedPricePerDayAttribute()
    {
        return 'MWK ' . number_format($this->price_per_day, 2);
    }

    /**
     * Get full bike name.
     */
    public function getFullNameAttribute()
    {
        return $this->brand . ' ' . $this->model;
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'available' => 'bg-success',
            'active' => 'bg-warning',
            'rented' => 'bg-warning',
            'maintenance' => 'bg-danger',
            'inactive' => 'bg-secondary',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'available' => 'Available',
            'active' => 'Rented',
            'rented' => 'Rented',
            'maintenance' => 'Maintenance',
            'inactive' => 'Inactive',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Check if bike is available.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->is_active;
    }

    /**
     * Check if bike is currently rented.
     */
    public function isRented(): bool
    {
        return $this->status === 'active' || $this->status === 'rented';
    }

    /**
     * Mark bike as active/rented.
     */
    public function markAsActive($userId = null)
{
    $this->update([
        'status' => 'rented',  // ← Change from 'active' to 'rented'
        'current_renter_id' => $userId,
    ]);
}

    /**
     * Mark bike as available.
     */
    public function markAsAvailable()
    {
        $this->update([
            'status' => 'available',
            'current_renter_id' => null,
        ]);
    }

    /**
     * Increment total rentals count.
     */
    public function incrementRentals()
    {
        $this->increment('total_rentals');
    }

    /**
     * Add revenue to total.
     */
    public function addRevenue($amount)
    {
        $this->increment('total_revenue', $amount);
    }

    /**
     * Get active rental for this bike.
     */
    public function getActiveRental()
    {
        return $this->activeRental()->first();
    }
}