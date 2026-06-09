<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    protected $fillable = [
        'subscription_id', 'booking_id', 'usage_date'
    ];
    
    protected $casts = [
        'usage_date' => 'date',
    ];
    
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}