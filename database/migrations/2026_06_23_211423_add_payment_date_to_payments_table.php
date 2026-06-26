<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add payment_date column if it doesn't exist
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->timestamp('payment_date')->nullable();
            }

            // Also ensure other columns exist
            if (!Schema::hasColumn('payments', 'net_amount')) {
                $table->decimal('net_amount', 10, 2)->default(0);
            }

            // Increase payment_method length if needed
            $table->string('payment_method', 100)->change();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_date');
        });
    }
};