<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'booking_time')) {
                $table->timestamp('booking_time')->nullable()->after('special_requests');
            }
            if (!Schema::hasColumn('bookings', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('status');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['booking_time', 'is_paid']);
        });
    }
};