<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;

class VehicleVettingService
{
    /**
     * Vet a vehicle automatically
     */
    public function vet(Vehicle $vehicle): array
    {
        // If already vetted, skip
        if ($vehicle->vetted_at) {
            return ['status' => 'already_vetted', 'vehicle' => $vehicle];
        }

        // Perform vetting
        $vehicle->performVetting();

        // Notify admin if manual review needed
        if ($vehicle->needsManualReview()) {
            $this->notifyAdminForReview($vehicle);
        }

        // Log the vetting
        Log::info('Vehicle vetted', [
            'vehicle_id' => $vehicle->id,
            'status' => $vehicle->vetting_status,
            'score' => $vehicle->vetting_score,
        ]);

        return [
            'status' => $vehicle->vetting_status,
            'vehicle' => $vehicle,
            'score' => $vehicle->vetting_score,
            'checks' => $vehicle->vetting_checks,
        ];
    }

    /**
     * Bulk vetting for all pending vehicles
     */
    public function vetAllPending(): array
    {
        $vehicles = Vehicle::pendingVetting()->get();
        $results = [];

        foreach ($vehicles as $vehicle) {
            $results[] = $this->vet($vehicle);
        }

        return $results;
    }

    /**
     * Re-vet a vehicle (e.g., after admin updates)
     */
    public function revet(Vehicle $vehicle): array
    {
        // Reset vetting
        $vehicle->vetting_status = 'pending';
        $vehicle->vetted_at = null;
        $vehicle->save();

        return $this->vet($vehicle);
    }

    /**
     * Notify admin for manual review
     */
    protected function notifyAdminForReview(Vehicle $vehicle): void
    {
        // Send notification to admins
        // You can implement email, SMS, or database notification
        // Example: event(new VehicleNeedsReview($vehicle));
        Log::warning('Vehicle needs manual review', [
            'vehicle_id' => $vehicle->id,
            'owner' => $vehicle->owner->email,
            'plate' => $vehicle->license_plate,
        ]);
    }

    /**
     * Auto-approve a vehicle (admin override)
     */
    public function approveManually(Vehicle $vehicle, $adminId, $notes = null): void
    {
        $vehicle->vetting_status = 'approved';
        $vehicle->is_approved = true;
        $vehicle->vetting_performed_by = $adminId;
        $vehicle->rejection_reason = null;
        $vehicle->save();

        Log::info('Vehicle manually approved', [
            'vehicle_id' => $vehicle->id,
            'admin_id' => $adminId,
        ]);
    }

    /**
     * Reject a vehicle (admin override)
     */
    public function rejectManually(Vehicle $vehicle, $adminId, $reason): void
    {
        $vehicle->vetting_status = 'rejected';
        $vehicle->is_approved = false;
        $vehicle->vetting_performed_by = $adminId;
        $vehicle->rejection_reason = $reason;
        $vehicle->save();

        Log::info('Vehicle manually rejected', [
            'vehicle_id' => $vehicle->id,
            'admin_id' => $adminId,
            'reason' => $reason,
        ]);
    }
}