<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('trip_started_at')->nullable()->after('status');
            $table->timestamp('trip_completed_at')->nullable()->after('trip_started_at');
            $table->enum('trip_status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled')->after('trip_completed_at');
            $table->decimal('system_commission', 10, 2)->default(0)->after('total_price');
            $table->decimal('owner_earnings', 10, 2)->default(0)->after('system_commission');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['trip_started_at', 'trip_completed_at', 'trip_status', 'system_commission', 'owner_earnings']);
        });
    }
};