<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicle_advertisements', 'route')) {
                $table->string('route')->nullable()->after('to_location_id');
            }
            if (!Schema::hasColumn('vehicle_advertisements', 'image')) {
                $table->string('image')->nullable()->after('route');
            }
        });
    }

    public function down()
    {
        Schema::table('vehicle_advertisements', function (Blueprint $table) {
            $table->dropColumn(['route', 'image']);
        });
    }
};