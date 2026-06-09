<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('status');
            }
            if (!Schema::hasColumn('bookings', 'payment_date')) {
                $table->timestamp('payment_date')->nullable()->after('is_paid');
            }
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_date');
            }
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_paid', 'payment_date', 'payment_method']);
        });
    }
};