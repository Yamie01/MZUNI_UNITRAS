<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeRental extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'rental_code',
    'bike_id',
    'user_id',
    'registration_number',
    'phone_number',
    'pickup_location',
    'dropoff_location',
    'start_time',
    'end_time',
    'duration',
    'duration_type',
    'rate_per_unit',
    'subtotal',      // ← ADD THIS
    'total_amount',  // ← ADD THIS
    'total_minutes',
    'rate_per_minute',
    'status',
    'is_paid',
    'payment_date',
    'payment_method',
];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'actual_return_time' => 'datetime',
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'total_minutes' => 'integer',
        'rate_per_minute' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'damage_charge' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'dropoff_latitude' => 'decimal:8',
        'dropoff_longitude' => 'decimal:8',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the bike for this rental.
     */
    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }

    /**
     * Get the user for this rental.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment for this rental.
     */
    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    /**
     * Get the transaction for this rental.
     */
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'transaction_id', 'id')
            ->where('transaction_type', 'bike_rental');
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to only include active rentals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed rentals.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include paid rentals.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope a query to only include unpaid rentals.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get elapsed minutes.
     */
    public function getElapsedMinutesAttribute()
    {
        if (!$this->start_time || $this->status !== 'active') {
            return $this->total_minutes ?? 0;
        }
        return ceil($this->start_time->diffInMinutes(now()));
    }

    /**
     * Get elapsed time formatted.
     */
    public function getElapsedTimeAttribute()
    {
        $minutes = $this->elapsed_minutes;
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'm';
        }
        return $mins . 'm';
    }

    /**
     * Get current cost based on elapsed time.
     */
    public function getCurrentCostAttribute()
    {
        if ($this->status !== 'active' || !$this->start_time) {
            return $this->total_amount ?? 0;
        }
        return $this->elapsed_minutes * $this->rate_per_minute;
    }

    /**
     * Get formatted current cost.
     */
    public function getFormattedCurrentCostAttribute()
    {
        return 'MWK ' . number_format($this->current_cost, 2);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute()
    {
        return 'MWK ' . number_format($this->total_amount, 2);
    }

    /**
     * Get formatted rate per minute.
     */
    public function getFormattedRateAttribute()
    {
        return 'MWK ' . number_format($this->rate_per_minute, 2) . '/min';
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-warning text-dark',
            'active' => 'bg-success',
            'completed' => 'bg-info',
            'cancelled' => 'bg-danger',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pending',
            'active' => 'Active',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Calculate current cost based on elapsed time.
     */
    public function calculateCurrentCost()
    {
        if ($this->status !== 'active' || !$this->start_time) {
            return $this->total_amount ?? 0;
        }

        $minutes = ceil($this->start_time->diffInMinutes(now()));
        return $minutes * $this->rate_per_minute;
    }

    /**
     * Calculate total cost when returning bike.
     */
    public function calculateTotalCost()
    {
        if (!$this->start_time) {
            return 0;
        }

        $endTime = $this->end_time ?? now();
        $minutes = ceil($this->start_time->diffInMinutes($endTime));
        return $minutes * $this->rate_per_minute;
    }

    /**
     * Check if rental is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if rental is completed.
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * Check if rental is pending.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if rental is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if rental is paid.
     */
    public function isPaid()
    {
        return $this->is_paid;
    }

    /**
     * Mark rental as active.
     */
    public function markAsActive()
    {
        $this->update([
            'status' => 'active',
            'start_time' => now(),
        ]);
    }

    /**
     * Mark rental as completed.
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => 'completed',
            'end_time' => now(),
            'total_minutes' => ceil($this->start_time->diffInMinutes(now())),
            'total_amount' => $this->total_minutes * $this->rate_per_minute,
        ]);
    }

    /**
     * Mark rental as paid.
     */
    public function markAsPaid($method = 'paychangu')
    {
        $this->update([
            'is_paid' => true,
            'payment_date' => now(),
            'payment_method' => $method,
        ]);
    }

    /**
     * Mark rental as cancelled.
     */
    public function markAsCancelled()
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Generate unique rental code.
     */
    public static function generateRentalCode()
    {
        return 'BIKE-' . strtoupper(uniqid());
    }

    /**
     * Calculate refund amount (deposit minus damages).
     */
    public function calculateRefund()
    {
        return ($this->deposit_paid ?? 0) - ($this->damage_charge ?? 0);
    }

    /**
     * Check if refund is due.
     */
    public function hasRefundDue()
    {
        return $this->isCompleted() && 
               $this->deposit_paid > 0 && 
               $this->calculateRefund() > 0;
    }
}