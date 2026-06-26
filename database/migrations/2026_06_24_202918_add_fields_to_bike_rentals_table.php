<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('bike_rentals', 'university_id')) {
                $table->string('university_id')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'dropoff_location')) {
                $table->string('dropoff_location')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'total_minutes')) {
                $table->integer('total_minutes')->default(0);
            }
            if (!Schema::hasColumn('bike_rentals', 'rate_per_minute')) {
                $table->decimal('rate_per_minute', 10, 2)->default(2.00);
            }
        });
    }

    public function down()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn([
                'university_id',
                'phone_number',
                'dropoff_location',
                'total_minutes',
                'rate_per_minute'
            ]);
        });
    }
};