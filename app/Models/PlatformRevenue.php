<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformRevenue extends Model
{
    protected $fillable = [
        'date',
        'total_revenue',
        'rides_revenue',
        'rentals_revenue',
        'subscriptions_revenue',
        'breakdown',
    ];

    protected $casts = [
        'total_revenue' => 'decimal:2',
        'rides_revenue' => 'decimal:2',
        'rentals_revenue' => 'decimal:2',
        'subscriptions_revenue' => 'decimal:2',
        'breakdown' => 'array',
        'date' => 'date',
    ];

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'MWK ' . number_format($this->total_revenue, 2);
    }
}