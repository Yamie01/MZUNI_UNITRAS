<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\HandlesLateFees;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HandlesLateFees;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'status',
        'address',
        'profile_photo',
        'university_id',
        'department',
        'driving_license',
        'license_expiry',
        'is_active',
        'has_unpaid_late_fee',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'has_unpaid_late_fee' => 'boolean',
            'license_expiry' => 'datetime',
        ];
    }

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get vehicles owned by the user.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    /**
     * Get vehicle advertisements.
     */
    public function advertisements()
    {
        return $this->hasMany(VehicleAdvertisement::class, 'owner_id');
    }

    /**
     * Get user bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get payments made by the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get reviews given by the user.
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * Get reviews received by the driver.
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    /**
     * Get bike rentals.
     */
    public function bikeRentals()
    {
        return $this->hasMany(BikeRental::class);
    }

    /**
     * Get payouts for the user (as vehicle owner).
     */
    public function payouts()
    {
        return $this->hasMany(Payout::class, 'user_id');
    }

    /**
     * Get staff payout details.
     */
    public function staffPayoutDetails()
    {
        return $this->hasOne(StaffPayoutDetail::class);
    }

    // ============================================================
    // ROLE CHECK METHODS
    // ============================================================

    /**
     * Check if user is a vehicle owner.
     */
    public function isVehicleOwner(): bool
    {
        return $this->user_type === 'vehicle_owner';
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if user is a student.
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if user is a staff member.
     */
    public function isStaff(): bool
    {
        return $this->user_type === 'staff';
    }

    // ============================================================
    // PAYOUT HELPER METHODS
    // ============================================================

    /**
     * Check if user has payout details configured.
     */
    public function hasPayoutDetails(): bool
    {
        $details = $this->staffPayoutDetails;
        
        if (!$details) {
            return false;
        }

        if ($details->preferred_payout_method === 'mobile_money') {
            return !empty($details->mobile_money_number) && 
                   !empty($details->mobile_money_provider);
        }

        return !empty($details->bank_account_number) && 
               !empty($details->bank_name);
    }

    /**
     * Get user's payout method display.
     */
    public function getPayoutMethodDisplay(): ?string
    {
        $details = $this->staffPayoutDetails;
        
        if (!$details) {
            return null;
        }

        if ($details->preferred_payout_method === 'mobile_money') {
            return $details->mobile_money_provider . ': ' . 
                   $details->mobile_money_number;
        }

        return $details->bank_name . ' - ' . 
               $details->bank_account_number;
    }

    /**
     * Get user's active payout method.
     */
    public function getActivePayoutMethod(): ?string
    {
        $details = $this->staffPayoutDetails;
        
        if (!$details) {
            return null;
        }

        return $details->getActivePayoutMethod();
    }
}