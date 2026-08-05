<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    /**
<<<<<<< HEAD
     * Mass assignable fields
     */
    protected $fillable = [
        'owner_id',
        'vehicle_type',
        'registration_number',
=======
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Owner & Basic Info
        'owner_id',
        'name',
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        'model',
        'make',
        'year',
        'color',
<<<<<<< HEAD
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
=======
        'license_plate',
        'registration_number',
        'seating_capacity',
        'capacity',
        'vehicle_type',
        'description',
        'image',
        
        // Pricing
        'price_per_km',
        'price_per_day',
        'price_per_seat',
        
        // Insurance & Roadworthiness
        'insurance_number',
        'insurance_expiry',
        'insurance_expiry_date',
        'insurance_certificate',
        'roadworthiness_certificate',
        'roadworthiness_expiry_date',
        
        // Vehicle Details
        'fuel_type',
        'fuel_efficiency',
        'features',
        'documents',
        
        // Status
        'status',
        'is_available',
        'is_approved',
        'is_active',
        
        // Vetting
        'vetting_status',
        'vetting_score',
        'vetting_checks',
        'vetting_performed_by',
        'vetted_at',
        'rejection_reason',
        
        // Location
        'current_latitude',
        'current_longitude',
        'from_location_id',
        'to_location_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    protected $casts = [
        'features' => 'array',
        'documents' => 'array',
<<<<<<< HEAD
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
=======
        'vetting_checks' => 'array',
        'price_per_km' => 'decimal:2',
        'price_per_day' => 'decimal:2',
        'price_per_seat' => 'decimal:2',
        'fuel_efficiency' => 'decimal:2',
        'is_available' => 'boolean',
        'is_approved' => 'boolean',
        'is_active' => 'boolean',
        'insurance_expiry' => 'date',
        'insurance_expiry_date' => 'date',
        'roadworthiness_expiry_date' => 'date',
        'vetted_at' => 'datetime',
        'year' => 'integer',
        'seating_capacity' => 'integer',
        'capacity' => 'integer',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the owner of the vehicle.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
<<<<<<< HEAD
     * Vehicle Advertisements
=======
     * Get the user who performed the vetting.
     */
    public function vettingPerformedBy()
    {
        return $this->belongsTo(User::class, 'vetting_performed_by');
    }

    /**
     * Get the vehicle's advertisements.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function advertisements()
    {
        return $this->hasMany(VehicleAdvertisement::class);
    }

    /**
<<<<<<< HEAD
     * Vehicle Bookings
=======
     * Get the vehicle's bookings.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
<<<<<<< HEAD
     * Vehicle Reviews
=======
     * Get the vehicle's reviews.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
<<<<<<< HEAD
     * Get active advertisement
=======
     * Get the vehicle's location history.
     */
    public function locations()
    {
        return $this->morphMany(VehicleLocation::class, 'trackable');
    }

    /**
     * Get the vehicle's latest location.
     */
    public function latestLocation()
    {
        return $this->morphOne(VehicleLocation::class, 'trackable')->latest('recorded_at');
    }

    /**
     * Get the departure location.
     */
    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    /**
     * Get the destination location.
     */
    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include vetted vehicles.
     */
    public function scopeVetted($query)
    {
        return $query->where('vetting_status', 'approved');
    }

    /**
     * Scope a query to only include vehicles pending vetting.
     */
    public function scopePendingVetting($query)
    {
        return $query->where('vetting_status', 'pending');
    }

    /**
     * Scope a query to only include vehicles needing manual review.
     */
    public function scopeManualReview($query)
    {
        return $query->where('vetting_status', 'manual_review');
    }

    /**
     * Scope a query to only include rejected vehicles.
     */
    public function scopeRejected($query)
    {
        return $query->where('vetting_status', 'rejected');
    }

    /**
     * Scope a query to only include available vehicles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
                     ->where('is_approved', true)
                     ->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the active advertisement for this vehicle.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function getActiveAdvertisementAttribute()
    {
        return $this->advertisements()
            ->where('status', 'approved')
            ->where('departure_time', '>', now())
            ->first();
    }

<<<<<<< HEAD
    // In app/Models/Vehicle.php
    public function locations()
    {
        return $this->morphMany(VehicleLocation::class, 'trackable');
    }

    public function latestLocation()
    {
        return $this->morphOne(VehicleLocation::class, 'trackable')->latest('recorded_at');
    }

    public function fromLocation()
{
    return $this->belongsTo(Location::class, 'from_location_id');
}

public function toLocation()
{
    return $this->belongsTo(Location::class, 'to_location_id');
}
=======
    /**
     * Get the vehicle's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return trim($this->make . ' ' . $this->model);
    }

    /**
     * Get the vehicle's full details.
     */
    public function getFullDetailsAttribute(): string
    {
        return $this->display_name . ' (' . $this->license_plate . ')';
    }

    /*
    |--------------------------------------------------------------------------
    | VETTING METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if vehicle is vetted (approved).
     */
    public function isVetted(): bool
    {
        return $this->vetting_status === 'approved';
    }

    /**
     * Check if vehicle is pending vetting.
     */
    public function isPending(): bool
    {
        return $this->vetting_status === 'pending';
    }

    /**
     * Check if vehicle needs manual review.
     */
    public function needsManualReview(): bool
    {
        return $this->vetting_status === 'manual_review';
    }

    /**
     * Check if vehicle is rejected.
     */
    public function isRejected(): bool
    {
        return $this->vetting_status === 'rejected';
    }

    /**
     * Check if vehicle is available.
     */
    public function isAvailable(): bool
    {
        return $this->is_available && $this->is_approved && $this->is_active;
    }

    /**
     * Check if vehicle is eligible to be listed.
     */
    public function isEligibleForListing(): bool
    {
        return $this->isVetted() && $this->is_available;
    }

    /**
     * Perform automatic vetting on the vehicle.
     */
    public function performVetting(): void
    {
        $checks = [];
        $score = 0;
        $passed = true;

        // Check 1: Validate license plate format
        $licenseValid = $this->validateLicensePlate();
        $checks[] = [
            'name' => 'License Plate Format',
            'passed' => $licenseValid,
            'message' => $licenseValid ? 'Valid format' : 'Invalid format (expected: MZ-123-456 or MZ-ABC-123)'
        ];
        if ($licenseValid) $score += 20;

        // Check 2: Check if vehicle already exists for another owner
        $exists = self::where('license_plate', $this->license_plate)
            ->where('id', '!=', $this->id)
            ->exists();
        $checks[] = [
            'name' => 'Unique Vehicle',
            'passed' => !$exists,
            'message' => $exists ? 'Vehicle already registered by another owner' : 'Vehicle is unique'
        ];
        if (!$exists) $score += 20;

        // Check 3: Owner is a valid staff member
        $isStaff = $this->owner && $this->owner->isStaff();
        $checks[] = [
            'name' => 'Owner is University Staff',
            'passed' => $isStaff,
            'message' => $isStaff ? 'Owner is verified staff' : 'Owner is not a verified staff member'
        ];
        if ($isStaff) $score += 20;

        // Check 4: Insurance validity
        $insuranceValid = $this->insurance_expiry_date && $this->insurance_expiry_date->isFuture();
        $checks[] = [
            'name' => 'Insurance Validity',
            'passed' => $insuranceValid,
            'message' => $insuranceValid ? 'Insurance is valid' : 'Insurance expired or not provided'
        ];
        if ($insuranceValid) $score += 20;

        // Check 5: Roadworthiness validity
        $roadworthyValid = $this->roadworthiness_expiry_date && $this->roadworthiness_expiry_date->isFuture();
        $checks[] = [
            'name' => 'Roadworthiness Validity',
            'passed' => $roadworthyValid,
            'message' => $roadworthyValid ? 'Roadworthiness is valid' : 'Roadworthiness expired or not provided'
        ];
        if ($roadworthyValid) $score += 20;

        // Store the checks
        $this->vetting_checks = $checks;
        $this->vetting_score = $score;

        // Determine status based on score
        if ($score >= 80 && $passed) {
            $this->vetting_status = 'approved';
            $this->is_approved = true;
        } elseif ($score >= 40) {
            $this->vetting_status = 'manual_review';
            $this->is_approved = false;
        } else {
            $this->vetting_status = 'rejected';
            $this->is_approved = false;
        }

        $this->vetted_at = now();
        $this->save();
    }

    /**
     * Validate license plate format (Malawi standard).
     */
    protected function validateLicensePlate(): bool
    {
        return preg_match('/^[A-Z]{2,3}-\d{3,4}$|^[A-Z]{2,3}-\d{3,4}-[A-Z]{1,2}$/', $this->license_plate) === 1;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get the vetting status badge class.
     */
    public function getVettingBadgeClass(): string
    {
        return match ($this->vetting_status) {
            'approved' => 'success',
            'pending' => 'warning',
            'manual_review' => 'info',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get the vetting status label.
     */
    public function getVettingStatusLabel(): string
    {
        return match ($this->vetting_status) {
            'approved' => 'Approved',
            'pending' => 'Pending',
            'manual_review' => 'Manual Review',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
    }

    /**
     * Get the vehicle status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }
        if (!$this->is_approved) {
            return 'warning';
        }
        if (!$this->is_available) {
            return 'danger';
        }
        return 'success';
    }

    /**
     * Get the vehicle status label.
     */
    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        if (!$this->is_approved) {
            return 'Pending Approval';
        }
        if (!$this->is_available) {
            return 'Unavailable';
        }
        return 'Available';
    }
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
}