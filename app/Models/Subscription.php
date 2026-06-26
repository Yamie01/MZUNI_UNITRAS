<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'type', 'status', 'price', 'start_date', 'end_date', 'cancelled_at'
    ];
    
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'cancelled_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function usages()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }
    
    public function isActive()
    {
        return $this->status === 'active' && Carbon::now()->between($this->start_date, $this->end_date);
    }
    
    public function getRemainingDays()
    {
        if (!$this->isActive()) return 0;
        return Carbon::now()->diffInDays($this->end_date, false);
    }
    
    public function getDailyLimit()
    {
        return $this->type === 'weekly' ? 2 : 4;
    }
    
    public function getTodaysUsageCount()
    {
        return $this->usages()
            ->whereDate('usage_date', Carbon::today())
            ->count();
    }
    
    // ✅ ADD THIS MISSING METHOD
    public function getRemainingTodaysRides()
    {
        return $this->getDailyLimit() - $this->getTodaysUsageCount();
    }
    
    public function canBookRide()
    {
        if (!$this->isActive()) return false;
        return $this->getTodaysUsageCount() < $this->getDailyLimit();
    }
}