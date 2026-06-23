<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Mzuni Main Campus (Gate)',
            'Dunduzu Tourism Campus',
            'Library',
            'Nora Hostel A',
            'Nora Hostel B',
            'Thengere Sports Ground',
            'ICT Centre',
            'School of Education',
            'School of Science',
            'Staff Quarters',
            'Luwinga Market',
            'Airport',
            'Area 1B',
            'Mzuzu Central Hospital',
            'Town (Mzuzu)',
            'Mzimba Road',
            'Katoto',
        ];

        foreach ($locations as $name) {
            Location::create(['name' => $name]);
        }
    }
}