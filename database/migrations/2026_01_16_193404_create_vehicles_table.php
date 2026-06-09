<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->enum('vehicle_type', ['bike', 'car', 'taxi', 'bus', 'minibus', 'coaster']);
            $table->string('registration_number')->unique();
            $table->string('model');
            $table->string('make')->nullable();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->integer('capacity')->comment('Number of seats/passengers');
            $table->json('features')->nullable()->comment('JSON array of features e.g., ["AC", "WiFi", "TV"]');
            $table->decimal('price_per_km', 8, 2)->nullable()->comment('For taxi/bike sharing');
            $table->decimal('price_per_day', 8, 2)->nullable()->comment('For daily rental');
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->enum('fuel_type', ['petrol', 'diesel', 'electric', 'hybrid'])->nullable();
            $table->decimal('fuel_efficiency', 5, 2)->nullable()->comment('km per liter');
            $table->enum('status', ['available', 'booked', 'maintenance', 'inactive', 'pending_approval'])->default('pending_approval');
            $table->boolean('is_approved')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->text('documents')->nullable()->comment('JSON of document paths');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index('owner_id');
            $table->index('vehicle_type');
            $table->index('status');
            $table->index('is_approved');
            $table->index(['current_latitude', 'current_longitude']);
            $table->index('registration_number');
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });
        Schema::dropIfExists('vehicles');
    }
}