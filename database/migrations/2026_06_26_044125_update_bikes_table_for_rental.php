<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bikes', function (Blueprint $table) {
            if (!Schema::hasColumn('bikes', 'status')) {
                $table->enum('status', ['available', 'active', 'rented', 'maintenance'])->default('available');
            }
            if (!Schema::hasColumn('bikes', 'current_renter_id')) {
                $table->foreignId('current_renter_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('bikes', 'rate_per_minute')) {
                $table->decimal('rate_per_minute', 10, 2)->default(2.00);
            }
        });
    }

    public function down()
    {
        Schema::table('bikes', function (Blueprint $table) {
            $table->dropColumn(['status', 'current_renter_id', 'rate_per_minute']);
        });
    }
};