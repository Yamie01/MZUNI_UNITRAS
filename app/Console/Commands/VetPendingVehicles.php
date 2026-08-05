<?php

namespace App\Console\Commands;

use App\Services\VehicleVettingService;
use Illuminate\Console\Command;

class VetPendingVehicles extends Command
{
    protected $signature = 'vehicles:vet-pending';
    protected $description = 'Automatically vet all pending vehicles';

    public function handle(VehicleVettingService $vettingService)
    {
        $results = $vettingService->vetAllPending();
        $this->info('Vetting complete: ' . count($results) . ' vehicles processed.');
        return 0;
    }
}