<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('university_id')->unique()->nullable()
                  ->comment('Student or Staff ID');

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();          // ✅ Only one phone column, removed `->after('email')`
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            $table->enum('user_type', [
                'student',
                'staff',
                'vehicle_owner',
                'admin'
            ])->default('student');

            // ✅ Removed duplicate phone column
            $table->string('avatar')->nullable();
            $table->string('department')->nullable();
            $table->string('driving_license')->nullable();
            $table->date('license_expiry')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'suspended'
            ])->default('active');

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_type');
            $table->index('status');
        });

        // Seed admin user
        DB::table('users')->insert([
            'name' => 'System Admin',
            'email' => 'admin@mzuni.ac.mw',
            'password' => bcrypt('admin123'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};