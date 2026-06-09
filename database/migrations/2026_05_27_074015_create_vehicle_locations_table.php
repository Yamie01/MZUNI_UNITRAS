<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_locations', function (Blueprint $table) {
    $table->id();
    $table->morphs('trackable');          // trackable_type, trackable_id
    $table->decimal('latitude', 10, 8);
    $table->decimal('longitude', 11, 8);
    $table->decimal('speed', 5, 2)->nullable();
    $table->decimal('heading', 5, 2)->nullable();
    $table->timestamp('recorded_at');
    $table->timestamps();

    $table->index(['trackable_type', 'trackable_id', 'recorded_at']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_locations');
    }
};
