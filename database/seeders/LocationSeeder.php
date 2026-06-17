<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $locations = [
            'Main Campus (Gate)',
            'City Campus',
            'Library',
            'Hostel A',
            'Hostel B',
            'Sports Complex',
            'ICT Centre',
            'School of Education',
            'School of Science',
            'Staff Quarters',
            'Luwinga',
            'Chibavi',
            'Town (Mzuzu)',
            'Mzimba Road',
            'Katoto',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(['name' => $name]);
        }

        $this->command->info('Locations seeded successfully.');
    }
}