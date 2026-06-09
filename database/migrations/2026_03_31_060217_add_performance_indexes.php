<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if an index exists on a table.
     */
    protected function indexExists($tableName, $indexName)
    {
        $result = DB::select("SHOW INDEX FROM {$tableName} WHERE Key_name = '{$indexName}'");
        return count($result) > 0;
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_user_type_index')) {
                $table->index('user_type');
            }
            if (!$this->indexExists('users', 'users_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('users', 'users_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('users', 'users_user_type_status_index')) {
                $table->index(['user_type', 'status']);
            }
        });

        // Vehicles table indexes
        Schema::table('vehicles', function (Blueprint $table) {
            if (!$this->indexExists('vehicles', 'vehicles_owner_id_index')) {
                $table->index('owner_id');
            }
            if (!$this->indexExists('vehicles', 'vehicles_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('vehicles', 'vehicles_is_approved_index')) {
                $table->index('is_approved');
            }
            if (!$this->indexExists('vehicles', 'vehicles_owner_id_is_approved_index')) {
                $table->index(['owner_id', 'is_approved']);
            }
        });

        // Vehicle advertisements table indexes
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            if (!$this->indexExists('vehicle_advertisements', 'vehicle_advertisements_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('vehicle_advertisements', 'vehicle_advertisements_departure_time_index')) {
                $table->index('departure_time');
            }
            if (!$this->indexExists('vehicle_advertisements', 'vehicle_advertisements_ad_type_index')) {
                $table->index('ad_type');
            }
            if (!$this->indexExists('vehicle_advertisements', 'vehicle_advertisements_status_departure_time_index')) {
                $table->index(['status', 'departure_time']);
            }
            if (!$this->indexExists('vehicle_advertisements', 'vehicle_advertisements_from_location_to_location_index')) {
                $table->index(['from_location', 'to_location']);
            }
        });

        // Bookings table indexes
        Schema::table('bookings', function (Blueprint $table) {
            if (!$this->indexExists('bookings', 'bookings_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('bookings', 'bookings_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('bookings', 'bookings_created_at_index')) {
                $table->index('created_at');
            }
            if (!$this->indexExists('bookings', 'bookings_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
            if (!$this->indexExists('bookings', 'bookings_vehicle_advertisement_id_status_index')) {
                $table->index(['vehicle_advertisement_id', 'status']);
            }
        });

        // Bikes table indexes
        Schema::table('bikes', function (Blueprint $table) {
            if (!$this->indexExists('bikes', 'bikes_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('bikes', 'bikes_type_index')) {
                $table->index('type');
            }
            if (!$this->indexExists('bikes', 'bikes_is_active_index')) {
                $table->index('is_active');
            }
            if (!$this->indexExists('bikes', 'bikes_status_is_active_index')) {
                $table->index(['status', 'is_active']);
            }
        });

        // Bike rentals table indexes
        Schema::table('bike_rentals', function (Blueprint $table) {
            if (!$this->indexExists('bike_rentals', 'bike_rentals_user_id_index')) {
                $table->index('user_id');
            }
            if (!$this->indexExists('bike_rentals', 'bike_rentals_bike_id_index')) {
                $table->index('bike_id');
            }
            if (!$this->indexExists('bike_rentals', 'bike_rentals_status_index')) {
                $table->index('status');
            }
            if (!$this->indexExists('bike_rentals', 'bike_rentals_start_time_index')) {
                $table->index('start_time');
            }
            if (!$this->indexExists('bike_rentals', 'bike_rentals_user_id_status_index')) {
                $table->index(['user_id', 'status']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Remove indexes (optional - you can skip if not needed)
        // This is commented out to avoid errors when rolling back
        // You can implement if needed
    }
};