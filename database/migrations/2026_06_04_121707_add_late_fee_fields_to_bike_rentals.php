<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            if (!Schema::hasColumn('bike_rentals', 'late_fee')) {
                $table->decimal('late_fee', 10, 2)->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('bike_rentals', 'extra_hours')) {
                $table->decimal('extra_hours', 5, 2)->default(0)->after('late_fee');
            }
            if (!Schema::hasColumn('bike_rentals', 'calculated_at')) {
                $table->timestamp('calculated_at')->nullable()->after('extra_hours');
            }
        });
    }

    public function down()
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn(['late_fee', 'extra_hours', 'calculated_at']);
        });
    }
};