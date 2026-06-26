<?php

namespace App\Traits;

use App\Models\BikeRental;

trait HandlesLateFees
{
    /**
     * Check if user has unpaid late fees.
     */
    public function hasUnpaidLateFee(): bool
    {
        return BikeRental::where('user_id', $this->id)
            ->where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->exists();
    }

    /**
     * Get total unpaid late fees.
     */
    public function getUnpaidLateFeeTotal(): float
    {
        return BikeRental::where('user_id', $this->id)
            ->where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->sum('late_fee');
    }

    /**
     * Get all rentals with unpaid late fees.
     */
    public function getUnpaidLateFeeRentals()
    {
        return BikeRental::where('user_id', $this->id)
            ->where('late_fee', '>', 0)
            ->where('late_fee_paid', false)
            ->get();
    }

    /**
     * Check if user can rent a bike.
     */
    public function canRentBike(): bool
    {
        return !$this->hasUnpaidLateFee();
    }

    /**
     * Get late fee warning message.
     */
    public function getLateFeeWarning(): ?string
    {
        if ($this->hasUnpaidLateFee()) {
            return 'You have an unpaid late fee of MWK ' . number_format($this->getUnpaidLateFeeTotal(), 2) . '. Please pay it to continue renting.';
        }
        return null;
    }
}