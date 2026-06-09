<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\VehicleLocation;
use App\Events\VehicleLocationUpdated;

class SimulateVehicleLocation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-vehicle-location';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
            $vehicle = Vehicle::find($this->argument('vehicleId'));
            $lat = -11.45 + (rand(-100, 100) / 10000);
            $lng = 34.02 + (rand(-100, 100) / 10000);

            $location = VehicleLocation::create([
                'trackable_type' => 'vehicle',
                'trackable_id' => $vehicle->id,
                'latitude' => $lat,
                'longitude' => $lng,
                'recorded_at' => now(),
            ]);

            broadcast(new VehicleLocationUpdated('vehicle', $vehicle->id, $location));
                }
}
