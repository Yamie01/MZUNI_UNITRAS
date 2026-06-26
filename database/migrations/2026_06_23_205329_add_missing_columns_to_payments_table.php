<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('payments', 'payment_date')) {
                $table->timestamp('payment_date')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'bike_rental_id')) {
                $table->foreignId('bike_rental_id')->nullable()->constrained('bike_rentals')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('payments', 'gateway_response')) {
                $table->json('gateway_response')->nullable();
            }
            
            if (!Schema::hasColumn('payments', 'mobile_money_number')) {
                $table->string('mobile_money_number')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'payment_date',
                'payment_method',
                'bike_rental_id',
                'gateway_response',
                'mobile_money_number'
            ]);
        });
    }
};