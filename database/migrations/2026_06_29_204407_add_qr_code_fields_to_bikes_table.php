<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bikes', function (Blueprint $table) {
            if (!Schema::hasColumn('bikes', 'qr_code')) {
                $table->string('qr_code')->nullable()->unique()->after('is_active');
            }
            if (!Schema::hasColumn('bikes', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('qr_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bikes', function (Blueprint $table) {
            if (Schema::hasColumn('bikes', 'qr_code')) {
                $table->dropColumn('qr_code');
            }
            if (Schema::hasColumn('bikes', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });
    }
};