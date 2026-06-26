<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'from_location_id')) {
                $table->foreignId('from_location_id')->nullable()->constrained('locations')->onDelete('set null');
            }
            if (!Schema::hasColumn('vehicles', 'to_location_id')) {
                $table->foreignId('to_location_id')->nullable()->constrained('locations')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id']);
        });
    }
};