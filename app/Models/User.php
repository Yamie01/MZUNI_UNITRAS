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
        'staff_id',
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
    // CREDENTIAL VALIDATION HELPERS
    // ============================================================

    /**
     * Validate user credentials for bike activation.
     * 
     * @param string $registrationNumber The entered registration number
     * @param string $phoneNumber The entered phone number
     * @return array Returns an array of errors
     */
    public function validateCredentials($registrationNumber, $phoneNumber): array
    {
        $errors = [];

        // Check registration number (university_id, driving_license, or staff_id)
        if (!$this->hasValidRegistrationNumber($registrationNumber)) {
            $errors['registration_number'] = 'Invalid Registration/Staff ID. Please use the ID you registered with.';
        }

        // Check phone number
        if (!$this->hasValidPhoneNumber($phoneNumber)) {
            $errors['phone_number'] = 'Invalid Phone Number. Please use the phone number you registered with.';
        }

        return $errors;
    }

    /**
     * Check if user has valid registration number.
     * 
     * @param string $registrationNumber The entered registration number
     * @return bool
     */
    public function hasValidRegistrationNumber($registrationNumber): bool
    {
        // Check against all possible ID fields
        if ($this->university_id && $this->university_id === $registrationNumber) {
            return true;
        }
        if ($this->driving_license && $this->driving_license === $registrationNumber) {
            return true;
        }
        if ($this->staff_id && $this->staff_id === $registrationNumber) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user has valid phone number.
     * 
     * @param string $phoneNumber The entered phone number
     * @return bool
     */
    public function hasValidPhoneNumber($phoneNumber): bool
    {
        // Remove all non-numeric characters
        $userPhone = preg_replace('/[^0-9]/', '', $this->phone);
        $enteredPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Remove leading 0 or 265 for comparison
        $userPhone = ltrim($userPhone, '0');
        $userPhone = ltrim($userPhone, '265');
        $enteredPhone = ltrim($enteredPhone, '0');
        $enteredPhone = ltrim($enteredPhone, '265');
        
        return $userPhone === $enteredPhone;
    }

    /**
     * Get the registration number type (student, staff, or driver).
     * 
     * @return string|null
     */
    public function getRegistrationType(): ?string
    {
        if ($this->university_id) {
            return 'Student';
        }
        if ($this->staff_id) {
            return 'Staff';
        }
        if ($this->driving_license) {
            return 'Driver';
        }
        return null;
    }

    /**
     * Get the actual registration number based on user type.
     * 
     * @return string|null
     */
    public function getRegistrationNumber(): ?string
    {
        if ($this->university_id) {
            return $this->university_id;
        }
        if ($this->staff_id) {
            return $this->staff_id;
        }
        if ($this->driving_license) {
            return $this->driving_license;
        }
        return null;
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