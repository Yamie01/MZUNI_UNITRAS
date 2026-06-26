<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('bike_rentals', 'registration_number')) {
                $table->string('registration_number')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'total_minutes')) {
                $table->integer('total_minutes')->default(0);
            }
            if (!Schema::hasColumn('bike_rentals', 'rate_per_minute')) {
                $table->decimal('rate_per_minute', 10, 2)->default(2.00);
            }
            if (!Schema::hasColumn('bike_rentals', 'total_amount')) {
                $table->decimal('total_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('bike_rentals', 'is_paid')) {
                $table->boolean('is_paid')->default(false);
            }
            if (!Schema::hasColumn('bike_rentals', 'payment_date')) {
                $table->timestamp('payment_date')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'pickup_location')) {
                $table->string('pickup_location')->nullable();
            }
            if (!Schema::hasColumn('bike_rentals', 'dropoff_location')) {
                $table->string('dropoff_location')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn([
                'registration_number',
                'phone_number',
                'total_minutes',
                'rate_per_minute',
                'total_amount',
                'is_paid',
                'payment_date',
                'payment_method',
                'pickup_location',
                'dropoff_location'
            ]);
        });
    }
};