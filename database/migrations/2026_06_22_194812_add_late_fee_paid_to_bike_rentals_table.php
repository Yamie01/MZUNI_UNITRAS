<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            // Add late_fee column if it doesn't exist
            if (!Schema::hasColumn('bike_rentals', 'late_fee')) {
                $table->decimal('late_fee', 10, 2)->default(0)->after('total_amount');
            }
            
            // Add late_fee_paid column if it doesn't exist
            if (!Schema::hasColumn('bike_rentals', 'late_fee_paid')) {
                $table->boolean('late_fee_paid')->default(false)->after('late_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bike_rentals', function (Blueprint $table) {
            $table->dropColumn(['late_fee', 'late_fee_paid']);
        });
    }
};