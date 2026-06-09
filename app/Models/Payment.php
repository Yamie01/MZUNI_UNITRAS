<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'booking_id',
    'bike_rental_id',  // ADD THIS
    'user_id',
    'transaction_id',
    'amount',
    'payment_method',
    'mobile_money_number',
    'status',
    'payment_date',
    'gateway_response',
];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'payment_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }
}