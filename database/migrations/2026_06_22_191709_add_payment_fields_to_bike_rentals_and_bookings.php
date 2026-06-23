<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add columns to bike_rentals table
        Schema::table('bike_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('bike_rentals', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('status');
            }
            if (!Schema::hasColumn('bike_rentals', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('is_paid');
            }
            if (!Schema::hasColumn('bike_rentals', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }
        });

        // Add columns to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('is_paid');
            }
            if (!Schema::hasColumn('bookings', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }
        });
    }

    public function down()
    {
        // Remove columns from bike_rentals
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'payment_method', 'paid_at']);
        });

        // Remove columns from bookings
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'payment_method', 'paid_at']);
        });
    }
};