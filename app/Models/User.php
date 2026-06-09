<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'user_type',
    'phone',
    'status',
    'university_id',
    'department',
    'driving_license',
    'license_expiry',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Vehicles owned by the user
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'owner_id');
    }

    /**
     * Vehicle advertisements
     */
    public function advertisements()
    {
        return $this->hasMany(VehicleAdvertisement::class, 'owner_id');
    }

    /**
     * User bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Payments made by the user
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Reviews given by the user
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * Reviews received by the driver
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | User Role Helpers
    |--------------------------------------------------------------------------
    */

    public function isVehicleOwner()
    {
        return $this->user_type === 'vehicle_owner';
    }

    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    public function isStudent()
    {
        return $this->user_type === 'student';
    }

    public function isStaff()
    {
        return $this->user_type === 'staff';
    }

    public function bikeRentals()
    {
    return $this->hasMany(BikeRental::class);
    }
}