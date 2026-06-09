<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('total_earnings', 12, 2)->default(0)->after('status');
            $table->decimal('available_balance', 12, 2)->default(0)->after('total_earnings');
            $table->decimal('withdrawn_amount', 12, 2)->default(0)->after('available_balance');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_earnings', 'available_balance', 'withdrawn_amount']);
        });
    }
};