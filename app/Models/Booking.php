<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_reference',
        'user_id',
        'vehicle_advertisement_id',
        'vehicle_id',
        'number_of_seats',
        'price_per_seat',
        'subtotal',
        'total_price',
        'pickup_point',
        'dropoff_point',
        'special_requests',
        'status',
        'is_paid',
        'payment_date',
        'payment_method',
        'booking_time',
        'tax_amount',
        'discount_amount',
        'extra_distance_charge',
        'extra_time_charge',
        'preferred_pickup_time',
        'actual_pickup_time',
        'actual_dropoff_time',
        'estimated_duration',
        'actual_duration',
        'estimated_distance',
        'actual_distance',
        'driver_id',

        'platform_fee',
        'owner_earnings',
        'is_paid',
        'payment_date',
];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'extra_distance_charge' => 'decimal:2',
        'extra_time_charge' => 'decimal:2',
        'price_per_seat' => 'decimal:2',
        'number_of_seats' => 'integer',
        'booking_time' => 'datetime',
        'payment_date' => 'datetime',
        'preferred_pickup_time' => 'datetime',
        'actual_pickup_time' => 'datetime',
        'actual_dropoff_time' => 'datetime',
        'estimated_duration' => 'integer',
        'actual_duration' => 'integer',
        'estimated_distance' => 'decimal:2',
        'actual_distance' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [];

    /**
     * Default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'pending',
        'is_paid' => false,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user that made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle advertisement associated with the booking.
     */
    public function advertisement(): BelongsTo
    {
        return $this->belongsTo(VehicleAdvertisement::class, 'vehicle_advertisement_id');
    }

    /**
     * Get the vehicle associated with the booking.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the payment associated with the booking.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get the review associated with the booking.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Get the driver assigned to this booking.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include pending bookings.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include completed bookings.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include paid bookings.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope a query to only include unpaid bookings.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'MWK ' . number_format($this->total_price, 2);
    }

    /**
     * Get the booking status with badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'badge bg-warning text-dark',
            'confirmed' => 'badge bg-info',
            'in_progress' => 'badge bg-primary',
            'completed' => 'badge bg-success',
            'cancelled' => 'badge bg-danger',
        ];

        return $badges[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Get the booking status text.
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    /**
     * Calculate the total price before saving.
     */
    public function calculateTotalPrice(): float
    {
        $total = $this->subtotal;
        $total += $this->tax_amount ?? 0;
        $total += $this->extra_distance_charge ?? 0;
        $total += $this->extra_time_charge ?? 0;
        $total -= $this->discount_amount ?? 0;
        
        return max(0, $total);
    }

    /*
    |--------------------------------------------------------------------------
    | Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if booking can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Check if booking can be confirmed.
     */
    public function canBeConfirmed(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if booking can be completed.
     */
    public function canBeCompleted(): bool
    {
        return $this->status === 'confirmed' || $this->status === 'in_progress';
    }

    /**
     * Generate unique booking reference.
     */
    public static function generateBookingReference(): string
    {
        $prefix = 'BKG';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
            
            if (empty($booking->subtotal) && $booking->number_of_seats && $booking->price_per_seat) {
                $booking->subtotal = $booking->number_of_seats * $booking->price_per_seat;
            }
            
            if (empty($booking->total_price)) {
                $booking->total_price = $booking->calculateTotalPrice();
            }
        });

        static::updating(function ($booking) {
            if ($booking->isDirty('number_of_seats') || $booking->isDirty('price_per_seat')) {
                $booking->subtotal = $booking->number_of_seats * $booking->price_per_seat;
                $booking->total_price = $booking->calculateTotalPrice();
            }
        });
    }
}