<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffPayoutDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Staff Information
        'user_id',
        'staff_id',
        'department',
        'designation',
        'office_location',
        
        // Payout Preferences
        'preferred_payout_method',
        'minimum_payout_threshold',
        
        // Mobile Money
        'mobile_money_provider',
        'mobile_money_number',
        'mobile_money_verified',
        'mobile_money_verified_at',
        
        // Bank
        'bank_name',
        'bank_branch',
        'bank_account_number',
        'bank_account_name',
        'bank_account_verified',
        'bank_account_verified_at',
        
        // Verification
        'verified_by',
        'verified_at',
        'verification_notes',
        
        // Status
        'is_active',
        'last_payout_at',
        
        // Metadata
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mobile_money_verified' => 'boolean',
        'bank_account_verified' => 'boolean',
        'is_active' => 'boolean',
        'minimum_payout_threshold' => 'decimal:2',
        'mobile_money_verified_at' => 'datetime',
        'bank_account_verified_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_payout_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the user (staff member) associated with these payout details.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who verified these details.
     */
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Scope a query to only include active staff payout details.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include verified staff payout details.
     */
    public function scopeVerified($query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope a query to only include unverified staff payout details.
     */
    public function scopeUnverified($query)
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope a query to only include staff with mobile money.
     */
    public function scopeWithMobileMoney($query)
    {
        return $query->whereNotNull('mobile_money_number')
                     ->whereNotNull('mobile_money_provider');
    }

    /**
     * Scope a query to only include staff with bank account.
     */
    public function scopeWithBankAccount($query)
    {
        return $query->whereNotNull('bank_account_number')
                     ->whereNotNull('bank_name');
    }

    /**
     * Scope a query to only include staff by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Check if staff has mobile money details.
     */
    public function hasMobileMoney(): bool
    {
        return !empty($this->mobile_money_number) && !empty($this->mobile_money_provider);
    }

    /**
     * Check if staff has bank account details.
     */
    public function hasBankAccount(): bool
    {
        return !empty($this->bank_account_number) && !empty($this->bank_name);
    }

    /**
     * Check if staff has any payout method configured.
     */
    public function hasPayoutMethod(): bool
    {
        return $this->hasMobileMoney() || $this->hasBankAccount();
    }

    /**
     * Check if staff's payout details are verified.
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Check if staff's preferred method is mobile money.
     */
    public function prefersMobileMoney(): bool
    {
        return $this->preferred_payout_method === 'mobile_money';
    }

    /**
     * Check if staff's preferred method is bank.
     */
    public function prefersBank(): bool
    {
        return $this->preferred_payout_method === 'bank';
    }

    /**
     * Get the active payout method (fallback if preferred not available).
     */
    public function getActivePayoutMethod(): ?string
    {
        if ($this->prefersMobileMoney() && $this->hasMobileMoney()) {
            return 'mobile_money';
        }

        if ($this->prefersBank() && $this->hasBankAccount()) {
            return 'bank';
        }

        // Fallback to available method
        if ($this->hasMobileMoney()) {
            return 'mobile_money';
        }

        if ($this->hasBankAccount()) {
            return 'bank';
        }

        return null;
    }

    /**
     * Get payout details for PayChangu.
     */
    public function getPayoutPayload(): ?array
    {
        $method = $this->getActivePayoutMethod();
        
        if (!$method) {
            return null;
        }

        $payload = [
            'method' => $method,
            'account_name' => $this->user->name ?? $this->bank_account_name,
        ];

        if ($method === 'mobile_money') {
            $payload['mobile_provider'] = strtolower($this->mobile_money_provider);
            $payload['mobile_number'] = $this->mobile_money_number;
        } else {
            $payload['bank_name'] = $this->bank_name;
            $payload['account_number'] = $this->bank_account_number;
            $payload['account_name'] = $this->bank_account_name ?? $this->user->name;
        }

        return $payload;
    }

    /**
     * Get formatted display of payout details.
     */
    public function getDisplayDetails(): string
    {
        if ($this->preferred_payout_method === 'mobile_money' && $this->hasMobileMoney()) {
            return $this->mobile_money_provider . ': ' . $this->mobile_money_number;
        }

        if ($this->hasBankAccount()) {
            return $this->bank_name . ' - ' . $this->bank_account_number;
        }

        return 'No payout method configured';
    }

    /**
     * Verify the staff payout details.
     */
    public function verify($adminId, $notes = null): self
    {
        $this->update([
            'verified_by' => $adminId,
            'verified_at' => now(),
            'verification_notes' => $notes,
        ]);

        return $this;
    }

    /**
     * Mark mobile money as verified.
     */
    public function verifyMobileMoney(): self
    {
        $this->update([
            'mobile_money_verified' => true,
            'mobile_money_verified_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark bank account as verified.
     */
    public function verifyBankAccount(): self
    {
        $this->update([
            'bank_account_verified' => true,
            'bank_account_verified_at' => now(),
        ]);

        return $this;
    }

    /**
     * Update last payout timestamp.
     */
    public function updateLastPayout(): self
    {
        $this->update([
            'last_payout_at' => now(),
        ]);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Get full staff information.
     */
    public function getStaffFullNameAttribute(): string
    {
        return $this->user->name ?? $this->staff_id;
    }

    /**
     * Get staff department and designation.
     */
    public function getStaffRoleAttribute(): string
    {
        $parts = [];
        
        if ($this->designation) {
            $parts[] = $this->designation;
        }
        
        if ($this->department) {
            $parts[] = $this->department;
        }
        
        return implode(' - ', $parts);
    }

    /**
     * Get verification status.
     */
    public function getVerificationStatusAttribute(): string
    {
        if ($this->isVerified()) {
            return 'Verified';
        }
        return 'Unverified';
    }

    /**
     * Get verification status badge.
     */
    public function getVerificationBadgeAttribute(): string
    {
        if ($this->isVerified()) {
            return 'success';
        }
        return 'warning';
    }
}