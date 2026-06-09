<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bikes', function (Blueprint $table) {
            $table->id();
            $table->string('bike_code')->unique()->comment('Unique identifier for the bike');
            $table->string('brand');
            $table->string('model');
            $table->enum('type', ['mountain', 'road', 'hybrid', 'electric', 'city'])->default('city');
            $table->string('color')->nullable();
            $table->integer('year')->nullable();
            $table->decimal('price_per_hour', 8, 2)->default(500);
            $table->decimal('price_per_day', 8, 2)->default(3000);
            $table->decimal('deposit_amount', 10, 2)->default(5000)->comment('Security deposit'); // FIXED: removed auto_increment
            $table->enum('status', ['available', 'rented', 'maintenance', 'out_of_service'])->default('available');
            $table->text('description')->nullable();
            $table->json('features')->nullable()->comment('JSON array of features');
            $table->json('images')->nullable();
            $table->string('qr_code')->nullable()->comment('QR code for bike access');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->timestamp('last_maintenance_date')->nullable();
            $table->integer('total_rentals')->default(0);
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('status');
            $table->index('bike_code');
            $table->index(['current_latitude', 'current_longitude']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bikes');
    }
};