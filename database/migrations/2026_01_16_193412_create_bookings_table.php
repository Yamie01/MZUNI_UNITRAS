<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique()->comment('Format: MZU-BK-YYYYMMDD-XXXXX');
            
            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_advertisement_id')->constrained('vehicle_advertisements')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('set null')->comment('Assigned driver');
            
            // Booking Details
            $table->integer('number_of_seats');
            $table->decimal('price_per_seat', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->decimal('extra_distance_charge', 10, 2)->default(0)->comment('For extra km beyond route');
            $table->decimal('extra_time_charge', 10, 2)->default(0)->comment('For waiting time');
            
            // Location Details
            $table->string('pickup_point');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->string('dropoff_point');
            $table->decimal('dropoff_latitude', 10, 8)->nullable();
            $table->decimal('dropoff_longitude', 11, 8)->nullable();
            
            // Timing Details
            $table->timestamp('preferred_pickup_time')->nullable();
            $table->timestamp('actual_pickup_time')->nullable();
            $table->timestamp('actual_dropoff_time')->nullable();
            $table->integer('estimated_duration')->nullable()->comment('In minutes');
            $table->integer('actual_duration')->nullable()->comment('In minutes');
            $table->decimal('estimated_distance', 8, 2)->nullable()->comment('In kilometers');
            $table->decimal('actual_distance', 8, 2)->nullable()->comment('In kilometers');
            
            // Status and Tracking
            $table->enum('status', [
                'pending', 
                'confirmed', 
                'awaiting_payment',
                'paid', 
                'driver_assigned',
                'in_progress', 
                'completed', 
                'cancelled',
                'refunded',
                'no_show',
                'disputed'
            ])->default('pending');
            
            $table->enum('cancelled_by', ['user', 'driver', 'owner', 'system'])->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Special Requests and Notes
            $table->text('special_requests')->nullable();
            $table->text('driver_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Payment Information
            $table->string('payment_method')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('payment_date')->nullable();
            
            // Ratings
            $table->integer('user_rating')->nullable()->comment('User rating for driver/vehicle (1-5)');
            $table->integer('driver_rating')->nullable()->comment('Driver rating for user (1-5)');
            $table->text('user_review')->nullable();
            $table->text('driver_review')->nullable();
            
            // Tracking
            $table->string('ip_address')->nullable();
            $table->json('location_history')->nullable()->comment('JSON array of locations during trip');
            $table->json('metadata')->nullable()->comment('Additional booking data');
            
            $table->timestamps();
            
            // Indexes for optimized queries
            $table->index('booking_reference');
            $table->index('user_id');
            $table->index('vehicle_advertisement_id');
            $table->index('vehicle_id');
            $table->index('driver_id');
            $table->index('status');
            $table->index('is_paid');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            $table->index('preferred_pickup_time');
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['vehicle_advertisement_id']);
            $table->dropForeign(['vehicle_id']);
            $table->dropForeign(['driver_id']);
        });
        Schema::dropIfExists('bookings');
    }
}