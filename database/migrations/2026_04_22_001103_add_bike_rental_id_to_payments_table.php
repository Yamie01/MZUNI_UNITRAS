<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Add bike_rental_id column (nullable, for bike rental payments)
            if (!Schema::hasColumn('payments', 'bike_rental_id')) {
                $table->foreignId('bike_rental_id')->nullable()->after('booking_id')
                    ->constrained('bike_rentals')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'bike_rental_id')) {
                $table->dropForeign(['bike_rental_id']);
                $table->dropColumn('bike_rental_id');
            }
        });
    }
};