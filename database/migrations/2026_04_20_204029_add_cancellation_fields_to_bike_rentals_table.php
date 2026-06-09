<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('bike_rentals', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('bike_rentals', 'cancellation_reason')) {
                $table->string('cancellation_reason')->nullable()->after('cancelled_at');
            }
        });
    }

    public function down()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn(['cancelled_at', 'cancellation_reason']);
        });
    }
};