<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevenueColumnsToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'platform_fee')) {
                $table->decimal('platform_fee', 10, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'owner_earnings')) {
                $table->decimal('owner_earnings', 10, 2)->default(0)->after('platform_fee');
            }
            // Note: is_paid and payment_date already exist, so we skip them
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['platform_fee', 'owner_earnings']);
        });
    }
}