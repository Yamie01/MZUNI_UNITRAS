<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'reference',
        'transaction_type',
        'transaction_id',
        'amount',
        'platform_fee',
        'owner_earnings',
        'status',
        'payment_details',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'payment_details' => 'array',
    ];
}