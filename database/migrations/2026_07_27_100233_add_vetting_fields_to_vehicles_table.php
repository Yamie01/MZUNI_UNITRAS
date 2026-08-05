<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Vetting Status - Check if exists
            if (!Schema::hasColumn('vehicles', 'vetting_status')) {
                $table->enum('vetting_status', ['pending', 'approved', 'rejected', 'manual_review'])
                    ->default('pending')->after('is_approved');
            }

            // Vetting Score - Check if exists
            if (!Schema::hasColumn('vehicles', 'vetting_score')) {
                $table->integer('vetting_score')->default(0)->after('vetting_status');
            }

            // Vetting Checks - Check if exists
            if (!Schema::hasColumn('vehicles', 'vetting_checks')) {
                $table->json('vetting_checks')->nullable()->after('vetting_score');
            }

            // Vetting Performed By - Check if exists
            if (!Schema::hasColumn('vehicles', 'vetting_performed_by')) {
                $table->foreignId('vetting_performed_by')->nullable()->constrained('users')->after('vetting_checks');
            }

            // Vetted At - Check if exists
            if (!Schema::hasColumn('vehicles', 'vetted_at')) {
                $table->timestamp('vetted_at')->nullable()->after('vetting_performed_by');
            }

            // Rejection Reason - Check if exists
            if (!Schema::hasColumn('vehicles', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('vetted_at');
            }

            // Insurance Certificate - Check if exists
            if (!Schema::hasColumn('vehicles', 'insurance_certificate')) {
                $table->string('insurance_certificate')->nullable()->after('rejection_reason');
            }

            // Roadworthiness Certificate - Check if exists
            if (!Schema::hasColumn('vehicles', 'roadworthiness_certificate')) {
                $table->string('roadworthiness_certificate')->nullable()->after('insurance_certificate');
            }

            // Insurance Expiry Date - Check if exists
            if (!Schema::hasColumn('vehicles', 'insurance_expiry_date')) {
                $table->date('insurance_expiry_date')->nullable()->after('roadworthiness_certificate');
            }

            // Roadworthiness Expiry Date - Check if exists
            if (!Schema::hasColumn('vehicles', 'roadworthiness_expiry_date')) {
                $table->date('roadworthiness_expiry_date')->nullable()->after('insurance_expiry_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = [
                'vetting_status',
                'vetting_score',
                'vetting_checks',
                'vetting_performed_by',
                'vetted_at',
                'rejection_reason',
                'insurance_certificate',
                'roadworthiness_certificate',
                'insurance_expiry_date',
                'roadworthiness_expiry_date'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};