<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayoutHistory extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payout_id',
        'user_id',
        'old_status',
        'new_status',
        'action',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ip_address',
        'user_agent',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the payout associated with this history record.
     */
    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include history for a specific payout.
     */
    public function scopeForPayout($query, $payoutId)
    {
        return $query->where('payout_id', $payoutId);
    }

    /**
     * Scope a query to only include history for a specific status change.
     */
    public function scopeStatusChange($query, $oldStatus, $newStatus)
    {
        return $query->where('old_status', $oldStatus)
                     ->where('new_status', $newStatus);
    }

    /**
     * Scope a query to only include history by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include history with a specific action.
     */
    public function scopeWithAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include today's history.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if this is a status change record.
     */
    public function isStatusChange(): bool
    {
        return $this->old_status !== $this->new_status;
    }

    /**
     * Check if this is a completion record.
     */
    public function isCompletion(): bool
    {
        return $this->new_status === 'completed';
    }

    /**
     * Check if this is a failure record.
     */
    public function isFailure(): bool
    {
        return $this->new_status === 'failed';
    }

    /**
     * Get formatted old status.
     */
    public function getOldStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->old_status);
    }

    /**
     * Get formatted new status.
     */
    public function getNewStatusLabelAttribute(): string
    {
        return $this->getStatusLabel($this->new_status);
    }

    /**
     * Get status label.
     */
    protected function getStatusLabel($status): string
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
        
        return $labels[$status] ?? $status;
    }

    /**
     * Get formatted action.
     */
    public function getActionLabelAttribute(): string
    {
        $actions = [
            'status_change' => 'Status Changed',
            'initiated' => 'Initiated',
            'processed' => 'Processed',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'retry' => 'Retry Attempted',
        ];
        
        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Get user name who performed the action.
     */
    public function getActorNameAttribute(): string
    {
        return $this->user->name ?? 'System';
    }

    /**
     * Get time since the action.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted timestamp.
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i:s');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get status change description.
     */
    public function getStatusChangeDescriptionAttribute(): string
    {
        if ($this->old_status === $this->new_status) {
            return 'Status remained ' . $this->getStatusLabel($this->new_status);
        }
        
        return 'Changed from ' . 
               $this->getStatusLabel($this->old_status) . 
               ' to ' . 
               $this->getStatusLabel($this->new_status);
    }
}