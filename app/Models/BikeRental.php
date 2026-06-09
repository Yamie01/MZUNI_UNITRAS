<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BikeRental extends Model
{
    protected $fillable = [
        'rental_code', 'bike_id', 'user_id', 'start_time', 'end_time',
        'duration_type', 'duration', 'rate_per_unit', 'subtotal',
        'deposit_paid', 'total_amount', 'status', 'pickup_location',
        'pickup_latitude', 'pickup_longitude', 'dropoff_location',
        'dropoff_latitude', 'dropoff_longitude', 'notes',
        'damage_report', 'damage_charge', 'refund_amount', 'actual_return_time',
        'is_paid', 'payment_date'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'actual_return_time' => 'datetime',
        'payment_date' => 'datetime',
        'rate_per_unit' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'damage_charge' => 'decimal:2',
        'refund_amount' => 'decimal:2'
    ];

    public function bike()
    {
        return $this->belongsTo(Bike::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->morphOne(Payment::class, 'payable');
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function calculateTotal()
    {
        if ($this->duration_type === 'hourly') {
            return $this->rate_per_unit * $this->duration;
        }
        return $this->rate_per_unit * $this->duration;
    }

    public function generateRentalCode()
    {
        return 'BIKE-' . strtoupper(uniqid());
    }

    public function transaction()
{
    return $this->hasOne(Transaction::class, 'transaction_id', 'id')
                ->where('transaction_type', 'bike_rental');
}
}