<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'booking_id',
        'user_id',
        'amount',
        'platform_fee',
        'recipient_phone',
        'provider',
        'reference',
        'status',
        'response',
        'processed_at',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'processed_at' => 'datetime',
        'response' => 'array',
    ];

    /**
     * Get the booking associated with this payout.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user (vehicle owner) associated with this payout.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include failed payouts.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if the payout is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the payout is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the payout has failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark the payout as completed.
     */
    public function markAsCompleted(): self
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark the payout as failed.
     */
    public function markAsFailed(string $error = null): self
    {
        $this->update([
            'status' => 'failed',
            'response' => array_merge($this->response ?? [], ['error' => $error]),
        ]);

        return $this;
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'MWK ' . number_format($this->amount, 2);
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'bg-success',
            'pending'   => 'bg-warning',
            'failed'    => 'bg-danger',
            'processing'=> 'bg-info',
            default     => 'bg-secondary',
        };
    }

    /**
     * Get human-readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Completed',
            'pending'   => 'Pending',
            'failed'    => 'Failed',
            'processing'=> 'Processing',
            default     => ucfirst($this->status),
        };
    }
}