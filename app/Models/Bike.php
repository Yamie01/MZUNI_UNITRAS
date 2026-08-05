<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Str;
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)

class Bike extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
<<<<<<< HEAD
=======
        // Basic Info
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        'bike_code',
        'brand',
        'model',
        'type',
        'color',
        'year',
<<<<<<< HEAD
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
=======
        'description',
        'features',
        'images',
        
        // Pricing
        'price_per_hour',
        'price_per_day',
        'deposit_amount',
        'rate_per_minute',
        
        // Status & Location
        'status',
        'is_active',
        'location_id',
        'current_latitude',
        'current_longitude',
        
        // QR Code
        'qr_code',
        'qr_code_path',
        
        // Rental Tracking
        'current_renter_id',
        'total_rentals',
        'total_revenue',
        
        // Maintenance
        'last_maintenance_date',
        'registration_number',
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
=======
        'rate_per_minute' => 'decimal:2',
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'last_maintenance_date' => 'date',
        'is_active' => 'boolean',
        'year' => 'integer',
        'total_rentals' => 'integer',
<<<<<<< HEAD
        'rate_per_minute' => 'decimal:2',
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
<<<<<<< HEAD
     * Get the rentals for this bike.
=======
     * Get the location of this bike.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get all rentals for this bike.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
     * Get active rentals (multiple, though usually one).
=======
     * Get all active rentals for this bike.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
     * Get the location of this bike.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
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
=======
     * Scope to only include available bikes.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_active', true);
    }

    /**
     * Scope to only include rented bikes.
     */
    public function scopeRented($query)
    {
        return $query->where('status', 'rented');
    }

    /**
     * Scope to only include bikes needing maintenance.
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->where('status', 'maintenance');
    }

<<<<<<< HEAD
    // ============================================================
    // ACCESSORS
=======
    /**
     * Scope to only include active bikes.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['available', 'rented'])->where('is_active', true);
    }

    // ============================================================
    // ACCESSORS & MUTATORS
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
        return $this->brand . ' ' . $this->model;
=======
        return trim($this->brand . ' ' . $this->model);
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
<<<<<<< HEAD
            'available' => 'bg-success',
            'active' => 'bg-warning',
            'rented' => 'bg-warning',
            'maintenance' => 'bg-danger',
            'inactive' => 'bg-secondary',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
=======
            'available' => 'success',
            'rented' => 'warning',
            'maintenance' => 'danger',
            'inactive' => 'secondary',
        ];

        return $badges[$this->status] ?? 'secondary';
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'available' => 'Available',
<<<<<<< HEAD
            'active' => 'Rented',
=======
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
            'rented' => 'Rented',
            'maintenance' => 'Maintenance',
            'inactive' => 'Inactive',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

<<<<<<< HEAD
    // ============================================================
    // METHODS
=======
    /**
     * Get the QR code URL.
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    /**
     * Get the QR code activation URL.
     */
    public function getQrActivationUrlAttribute()
    {
        if ($this->qr_code) {
            return route('bike.activate', ['qr' => $this->qr_code]);
        }
        return null;
    }

    // ============================================================
    // HELPER METHODS
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
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
=======
        return $this->status === 'rented';
    }

    /**
     * Check if bike is in maintenance.
     */
    public function isInMaintenance(): bool
    {
        return $this->status === 'maintenance';
    }

    /**
     * Check if bike is active.
     */
    public function isActive(): bool
    {
        return $this->is_active && in_array($this->status, ['available', 'rented']);
    }

    /**
     * Mark bike as rented.
     */
    public function markAsRented($userId): void
    {
        $this->update([
            'status' => 'rented',
            'current_renter_id' => $userId,
        ]);
    }
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)

    /**
     * Mark bike as available.
     */
<<<<<<< HEAD
    public function markAsAvailable()
=======
    public function markAsAvailable(): void
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    {
        $this->update([
            'status' => 'available',
            'current_renter_id' => null,
        ]);
    }

    /**
<<<<<<< HEAD
     * Increment total rentals count.
     */
    public function incrementRentals()
=======
     * Mark bike as in maintenance.
     */
    public function markAsMaintenance(): void
    {
        $this->update([
            'status' => 'maintenance',
            'current_renter_id' => null,
        ]);
    }

    /**
     * Increment total rentals count.
     */
    public function incrementRentals(): void
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
    {
        $this->increment('total_rentals');
    }

    /**
     * Add revenue to total.
     */
<<<<<<< HEAD
    public function addRevenue($amount)
=======
    public function addRevenue($amount): void
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
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
<<<<<<< HEAD
=======

    /**
     * Update bike location.
     */
    public function updateLocation($latitude, $longitude): void
    {
        $this->update([
            'current_latitude' => $latitude,
            'current_longitude' => $longitude,
        ]);

        // Create location tracking record
        $this->locations()->create([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'recorded_at' => now(),
        ]);
    }

    // ============================================================
    // QR CODE METHODS
    // ============================================================

    /**
     * Generate QR code for bike activation.
     */
    /**
 * Generate QR code for bike activation
 */
public function generateQRCode()
{
    // Create the QR code directory if it doesn't exist
    $path = storage_path('app/public/qrcodes/bikes');
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }

    // Generate unique QR code identifier
    $qrIdentifier = 'BIKE-' . $this->id . '-' . Str::random(10);
    $this->qr_code = $qrIdentifier;
    $this->save();

    // QR code data URL
    $qrData = route('bike.activate', ['qr' => $qrIdentifier]);
    $fileName = 'bike-qr-' . $this->id . '.png';
    $filePath = $path . '/' . $fileName;

    // Generate QR code using GD backend (not Imagick)
    try {
        // Use SimpleSoftwareIO with GD backend
        $qrCode = \QrCode::format('png')
            ->size(400)
            ->errorCorrection('H')
            ->generate($qrData);
        
        // Save the QR code to file
        file_put_contents($filePath, $qrCode);
        
        $this->qr_code_path = 'qrcodes/bikes/' . $fileName;
        $this->save();
        
        return $this;
    } catch (\Exception $e) {
        // If QR generation fails, use fallback method
        Log::warning('QR Code generation failed, using fallback', [
            'bike_id' => $this->id,
            'error' => $e->getMessage()
        ]);
        return $this->generateFallbackQR($qrData, $filePath);
    }
}

/**
 * Generate fallback QR code using API
 */
private function generateFallbackQR($data, $filePath)
{
    // Use free QR code API as fallback
    $url = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=' . urlencode($data);
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $imageData) {
        file_put_contents($filePath, $imageData);
        $this->qr_code_path = 'qrcodes/bikes/' . basename($filePath);
        $this->save();
        return $this;
    }

    // If API fails, create simple SVG
    return $this->createSimpleSVG($data, $filePath);
}

/**
 * Create a simple SVG fallback
 */
private function createSimpleSVG($data, $filePath)
{
    $svg = '<svg width="400" height="400" xmlns="http://www.w3.org/2000/svg">
        <rect width="400" height="400" fill="#ffffff"/>
        <rect x="40" y="40" width="320" height="320" rx="10" fill="#00693E"/>
        <text x="200" y="120" text-anchor="middle" font-family="Arial" font-size="28" fill="#ffffff" font-weight="bold">MZUNI UNITRAS</text>
        <text x="200" y="170" text-anchor="middle" font-family="Arial" font-size="16" fill="#FFB300">🚲 QR CODE</text>
        <text x="200" y="220" text-anchor="middle" font-family="Arial" font-size="12" fill="#e0e0e0">Scan to Activate Bike</text>
        <rect x="120" y="250" width="160" height="40" rx="8" fill="#FFB300"/>
        <text x="200" y="276" text-anchor="middle" font-family="Arial" font-size="14" fill="#1a1a1a" font-weight="bold">SCAN ME</text>
        <text x="200" y="340" text-anchor="middle" font-family="Arial" font-size="10" fill="#cccccc">' . substr($data, -30) . '</text>
        <text x="200" y="370" text-anchor="middle" font-family="Arial" font-size="10" fill="#888888">Powered by MZUNI UNITRAS</text>
    </svg>';

    file_put_contents($filePath, $svg);
    $this->qr_code_path = 'qrcodes/bikes/' . basename($filePath);
    $this->save();
    
    return $this;
}
    /**
     * Regenerate QR code.
     */
    public function regenerateQRCode()
    {
        return $this->generateQRCode();
    }

    /**
     * Delete QR code image.
     */
    public function deleteQRCode(): void
    {
        if ($this->qr_code_path) {
            $filePath = storage_path('app/public/' . $this->qr_code_path);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        $this->update([
            'qr_code' => null,
            'qr_code_path' => null,
        ]);
    }
>>>>>>> b686eb4 (All updated features - Mzuni UNITRAS system)
}