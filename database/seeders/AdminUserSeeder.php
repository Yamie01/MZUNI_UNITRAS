<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@mzuni.ac.mw'], // Check if admin already exists
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin@123'),
                'user_type' => 'admin',
                'phone' => '+265888123456',
                'status' => 'active',
            ]
        );

        $this->command->info('Admin user seeded successfully!');
        $this->command->info('Email: admin@mzuni.ac.mw');
        $this->command->info('Password: Admin@123');
    }
}