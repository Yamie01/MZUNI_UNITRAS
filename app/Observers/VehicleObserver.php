<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Services\VehicleVettingService;

class VehicleObserver
{
    protected $vettingService;

    public function __construct(VehicleVettingService $vettingService)
    {
        $this->vettingService = $vettingService;
    }

    /**
     * Handle the Vehicle "created" event.
     */
    public function created(Vehicle $vehicle): void
    {
        // Automatically vet the vehicle after creation
        $this->vettingService->vet($vehicle);
    }

    /**
     * Handle the Vehicle "updated" event.
     */
    public function updated(Vehicle $vehicle): void
    {
        // If certain fields changed, re-vet
        if ($vehicle->wasChanged([
            'license_plate',
            'insurance_expiry_date',
            'roadworthiness_expiry_date',
            'owner_id'
        ])) {
            // Reset vetting status and re-vet
            $vehicle->vetting_status = 'pending';
            $vehicle->vetted_at = null;
            $vehicle->save();
            $this->vettingService->vet($vehicle);
        }
    }

    /**
     * Handle the Vehicle "deleted" event.
     */
    public function deleted(Vehicle $vehicle): void
    {
        // Clean up vetting logs if needed
    }
}