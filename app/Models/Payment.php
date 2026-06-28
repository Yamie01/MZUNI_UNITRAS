<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'bike_rental_id',
        'user_id',
        'transaction_id',
        'amount',
        'net_amount',
        'payment_method',
        'mobile_money_number',
        'status',
        'payment_date',
        'gateway_response',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'gateway_response' => 'array',
    ];

    // ============================================================
    // RELATIONSHIPS
    // ============================================================

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the booking associated with the payment.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the bike rental associated with the payment.
     */
    public function bikeRental()
    {
        return $this->belongsTo(BikeRental::class, 'bike_rental_id');
    }

    /**
     * Get the parent payable model (polymorphic).
     */
    public function payable()
    {
        return $this->morphTo();
    }

    // ============================================================
    // SCOPES
    // ============================================================

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payments.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include refunded payments.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    // ============================================================
    // ACCESSORS
    // ============================================================

    /**
     * Get the formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'MWK ' . number_format($this->amount, 2);
    }

    /**
     * Get the payment status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'badge bg-warning text-dark',
            'completed' => 'badge bg-success',
            'failed' => 'badge bg-danger',
            'refunded' => 'badge bg-secondary',
        ];

        return $badges[$this->status] ?? 'badge bg-secondary';
    }

    /**
     * Get the payment status text.
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    // ============================================================
    // METHODS
    // ============================================================

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payment is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'payment_date' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => 'failed',
        ]);
    }

    /**
     * Mark payment as refunded.
     */
    public function markAsRefunded(): bool
    {
        return $this->update([
            'status' => 'refunded',
        ]);
    }
}