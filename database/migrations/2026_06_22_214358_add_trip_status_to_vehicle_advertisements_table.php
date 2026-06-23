<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('vehicle_advertisements', function (Blueprint $table) {
        $table->enum('trip_status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
        $table->timestamp('trip_started_at')->nullable();
        $table->timestamp('trip_completed_at')->nullable();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            //
        });
    }
};
