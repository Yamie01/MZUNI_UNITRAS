<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('bookings', 'system_commission')) {
                $table->decimal('system_commission', 10, 2)->default(0)->after('total_price');
            }
            if (!Schema::hasColumn('bookings', 'owner_earnings')) {
                $table->decimal('owner_earnings', 10, 2)->default(0)->after('system_commission');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['system_commission', 'owner_earnings']);
        });
    }
};