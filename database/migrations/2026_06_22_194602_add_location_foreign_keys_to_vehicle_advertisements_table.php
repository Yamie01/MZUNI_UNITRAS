<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_advertisements', 'from_location_id')) {
                $table->foreignId('from_location_id')
                      ->nullable()
                      ->constrained('locations')
                      ->onDelete('set null');
            }
            if (!Schema::hasColumn('vehicle_advertisements', 'to_location_id')) {
                $table->foreignId('to_location_id')
                      ->nullable()
                      ->constrained('locations')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id']);
        });
    }
};