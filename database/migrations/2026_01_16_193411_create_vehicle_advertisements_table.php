<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_advertisements', function (Blueprint $table) {
            $table->id();

            /* =====================
             * RELATIONSHIPS
             * ===================== */
            $table->foreignId('vehicle_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /* =====================
             * BASIC INFO
             * ===================== */
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');

            $table->enum('ad_type', [
                'ride_share', 'taxi', 'bus', 'bike_share', 'rental'
            ]);

            $table->enum('trip_type', [
                'one_way', 'round_trip', 'multi_city', 'daily'
            ])->default('one_way');

            /* =====================
             * LOCATION INFO
             * ===================== */
            $table->string('from_location');
            $table->decimal('from_latitude', 10, 8)->nullable();
            $table->decimal('from_longitude', 11, 8)->nullable();

            $table->string('to_location');
            $table->decimal('to_latitude', 10, 8)->nullable();
            $table->decimal('to_longitude', 11, 8)->nullable();

            /* =====================
             * TIMING
             * ===================== */
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time')->nullable();

            $table->dateTime('return_departure_time')->nullable();
            $table->dateTime('return_arrival_time')->nullable();

            /* =====================
             * PRICING & CAPACITY
             * ===================== */
            $table->decimal('price', 10, 2);
            $table->decimal('price_per_extra_km', 8, 2)->nullable();

            $table->unsignedInteger('total_seats');
            $table->unsignedInteger('available_seats');
            $table->unsignedInteger('minimum_seats')->default(1);
            $table->unsignedInteger('maximum_seats')->default(1);

            /* =====================
             * ROUTES
             * ===================== */
            $table->json('route_points')->nullable();
            $table->json('pickup_points')->nullable();
            $table->json('dropoff_points')->nullable();

            /* =====================
             * STATUS & VISIBILITY
             * ===================== */
            $table->enum('status', [
                'draft', 'pending', 'approved', 'rejected',
                'active', 'completed', 'cancelled', 'expired'
            ])->default('draft');

            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('booking_count')->default(0);

            /* =====================
             * RECURRING TRIPS
             * ===================== */
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_days')->nullable();
            $table->date('recurring_start_date')->nullable();
            $table->date('recurring_end_date')->nullable();

            /* =====================
             * EXTRA INFO
             * ===================== */
            $table->text('terms_conditions')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->json('images')->nullable();
            $table->json('amenities')->nullable();

            $table->timestamps();

            /* =====================
             * INDEXES
             * ===================== */
            $table->index(['from_location', 'to_location']);
            $table->index(['ad_type', 'trip_type', 'status']);
            $table->index('departure_time');
            $table->index('price');
            $table->index('available_seats');

            $table->fullText(
    ['title', 'description', 'from_location', 'to_location'],
    'va_fulltext'
);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_advertisements');
    }
};
