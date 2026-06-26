<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Relationships
        'booking_id',
        'user_id',
        'transaction_id',
        
        // MZUNI Staff Information
        'staff_id',
        'staff_name',
        'department',
        'designation',
        
        // Revenue Split
        'total_amount',
        'owner_share',
        'platform_share',
        'amount',
        
        // Mobile Money
        'payout_method',
        'mobile_money_provider',
        'mobile_money_number',
        'mobile_money_verified',
        
        // Bank
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'bank_account_verified',
        
        // Tracking
        'reference',
        'payout_reference',
        'provider_reference',
        'status',
        
        // Responses
        'request_payload',
        'response_payload',
        'error_message',
        'admin_notes',
        
        // Timestamps
        'initiated_at',
        'processed_at',
        'completed_at',
        'failed_at',
        
        // Metadata
        'metadata',
        'audit_trail',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'owner_share' => 'decimal:2',
        'platform_share' => 'decimal:2',
        'amount' => 'decimal:2',
        'mobile_money_verified' => 'boolean',
        'bank_account_verified' => 'boolean',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'metadata' => 'array',
        'audit_trail' => 'array',
        'initiated_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the booking associated with this payout.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the user (vehicle owner) associated with this payout.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transaction associated with this payout.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the payout history records.
     */
    public function history()
    {
        return $this->hasMany(PayoutHistory::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing payouts.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed payouts.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed payouts.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope a query to only include payouts by staff ID.
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    /**
     * Scope a query to only include payouts by method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payout_method', $method);
    }

    /**
     * Scope a query to only include today's payouts.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope a query to only include this month's payouts.
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Check Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if payout is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payout is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payout is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if payout is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if payout requires owner details.
     */
    public function isPendingDetails(): bool
    {
        return $this->status === 'pending_details';
    }

    /**
     * Check if payout can be processed.
     */
    public function canBeProcessed(): bool
    {
        return in_array($this->status, ['pending', 'pending_details']);
    }

    /**
     * Check if payout can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'pending_details', 'processing']);
    }

    /*
    |--------------------------------------------------------------------------
    | Status Update Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Mark payout as processing.
     */
    public function markAsProcessing($response = null): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'processing',
            'response_payload' => $response,
            'initiated_at' => now(),
            'processed_at' => now(),
        ]);

        $this->recordHistory($oldStatus, 'processing', 'Payout initiated');

        return $this;
    }

    /**
     * Mark payout as completed.
     */
    public function markAsCompleted($response = null): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'completed',
            'response_payload' => $response,
            'completed_at' => now(),
        ]);

        $this->recordHistory($oldStatus, 'completed', 'Payout completed successfully');

        return $this;
    }

    /**
     * Mark payout as failed.
     */
    public function markAsFailed($errorMessage = null, $response = null): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'response_payload' => $response,
            'failed_at' => now(),
        ]);

        $this->recordHistory($oldStatus, 'failed', 'Payout failed: ' . $errorMessage);

        return $this;
    }

    /**
     * Mark payout as pending details.
     */
    public function markAsPendingDetails(): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'pending_details',
        ]);

        $this->recordHistory($oldStatus, 'pending_details', 'Payout requires owner details');

        return $this;
    }

    /**
     * Mark payout as reversed.
     */
    public function markAsReversed($response = null): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'reversed',
            'response_payload' => $response,
        ]);

        $this->recordHistory($oldStatus, 'reversed', 'Payout reversed');

        return $this;
    }

    /**
     * Mark payout as cancelled.
     */
    public function markAsCancelled($reason = null): self
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => 'cancelled',
            'admin_notes' => $reason,
        ]);

        $this->recordHistory($oldStatus, 'cancelled', 'Payout cancelled: ' . $reason);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Record payout history.
     */
    protected function recordHistory($oldStatus, $newStatus, $notes = null): void
    {
        PayoutHistory::create([
            'payout_id' => $this->id,
            'user_id' => auth()->id(),
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'action' => 'status_change',
            'notes' => $notes,
            'metadata' => [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_at' => now()->toDateTimeString()
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update audit trail
        $auditTrail = $this->audit_trail ?? [];
        $auditTrail[] = [
            'action' => 'status_change',
            'from' => $oldStatus,
            'to' => $newStatus,
            'notes' => $notes,
            'timestamp' => now()->toDateTimeString(),
            'user_id' => auth()->id()
        ];
        $this->update(['audit_trail' => $auditTrail]);
    }

    /**
     * Check if payout is eligible for retry.
     */
    public function isEligibleForRetry(): bool
    {
        if (!$this->isFailed()) {
            return false;
        }

        $failedCount = $this->history()
            ->where('new_status', 'failed')
            ->count();

        if ($failedCount >= 3) {
            return false;
        }

        if ($this->failed_at && $this->failed_at->diffInHours(now()) > 24) {
            return false;
        }

        return true;
    }

    /**
     * Get retry count.
     */
    public function getRetryCount(): int
    {
        return $this->history()
            ->where('new_status', 'failed')
            ->count();
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'MWK ' . number_format($this->amount, 2);
    }

    /**
     * Get formatted owner share.
     */
    public function getFormattedOwnerShareAttribute(): string
    {
        return 'MWK ' . number_format($this->owner_share, 2);
    }

    /**
     * Get formatted platform share.
     */
    public function getFormattedPlatformShareAttribute(): string
    {
        return 'MWK ' . number_format($this->platform_share, 2);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'MWK ' . number_format($this->total_amount, 2);
    }

    /**
     * Get payout method display.
     */
    public function getPayoutMethodDisplayAttribute(): string
    {
        if ($this->payout_method === 'mobile_money') {
            return $this->mobile_money_provider . ': ' . $this->mobile_money_number;
        }
        return $this->bank_name . ' - ' . $this->bank_account_number;
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'warning',
            'pending_details' => 'info',
            'processing' => 'primary',
            'completed' => 'success',
            'failed' => 'danger',
            'reversed' => 'secondary',
            'cancelled' => 'dark'
        ];
        
        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Pending',
            'pending_details' => 'Pending Details',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'reversed' => 'Reversed',
            'cancelled' => 'Cancelled'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}