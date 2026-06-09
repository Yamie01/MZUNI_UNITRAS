<?php

namespace App\Console\Commands;

use App\Models\BikeRental;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateLateFees extends Command
{
    protected $signature = 'rentals:calculate-late-fees';
    protected $description = 'Calculate late fees for overdue bike rentals';

    public function handle()
    {
        $activeRentals = BikeRental::where('status', 'active')->get();
        $updated = 0;
        
        foreach ($activeRentals as $rental) {
            // Calculate expected end time
            $startTime = $rental->created_at;
            $durationHours = $rental->duration_type === 'hour' 
                ? $rental->duration 
                : $rental->duration * 24;
            $endTime = $startTime->copy()->addHours($durationHours);
            
            if (now() > $endTime) {
                // Calculate overtime hours
                $overtimeHours = ceil(now()->diffInHours($endTime));
                
                // Calculate late fee (e.g., 50% of hourly rate per extra hour)
                $hourlyRate = $rental->total_amount / $durationHours;
                $lateFee = $overtimeHours * ($hourlyRate * 0.5);
                
                $rental->update([
                    'late_fee' => $lateFee,
                    'extra_hours' => $overtimeHours,
                    'calculated_at' => now(),
                ]);
                $updated++;
                
                Log::info("Late fee calculated for rental #{$rental->id}: MWK {$lateFee} for {$overtimeHours} extra hours");
            }
        }
        
        $this->info("Calculated late fees for {$updated} rentals.");
    }
}