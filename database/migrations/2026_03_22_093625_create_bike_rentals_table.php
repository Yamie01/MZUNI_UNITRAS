<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bike_rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_code')->unique();
            $table->foreignId('bike_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('duration_type', ['hourly', 'daily'])->default('hourly');
            $table->integer('duration')->comment('Number of hours or days');
            $table->decimal('rate_per_unit', 8, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('deposit_paid', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled', 'overdue'])->default('pending');
            $table->string('pickup_location');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->string('dropoff_location')->nullable();
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->text('damage_report')->nullable();
            $table->decimal('damage_charge', 10, 2)->default(0);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->timestamp('actual_return_time')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('payment_date')->nullable();
            $table->timestamps();
            
            $table->index('rental_code');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['bike_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bike_rentals');
    }
};