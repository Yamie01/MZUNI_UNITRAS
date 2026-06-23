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
    Schema::table('bike_rentals', function (Blueprint $table) {
        if (!Schema::hasColumn('bike_rentals', 'duration')) {
            $table->integer('duration')->nullable()->after('total_amount');
        }
        if (!Schema::hasColumn('bike_rentals', 'duration_type')) {
            $table->enum('duration_type', ['hourly', 'daily'])->default('hourly')->after('duration');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            //
        });
    }
};
