<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Check if columns exist before adding
            if (!Schema::hasColumn('users', 'staff_id')) {
                $table->string('staff_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('staff_id');
            }
            if (!Schema::hasColumn('users', 'designation')) {
                $table->string('designation')->nullable()->after('department');
            }
            if (!Schema::hasColumn('users', 'mobile_money_provider')) {
                $table->string('mobile_money_provider')->nullable()->after('designation');
            }
            if (!Schema::hasColumn('users', 'mobile_money_number')) {
                $table->string('mobile_money_number')->nullable()->after('mobile_money_provider');
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('mobile_money_number');
            }
            if (!Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('users', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable()->after('bank_account_number');
            }
            if (!Schema::hasColumn('users', 'payout_method')) {
                $table->enum('payout_method', ['mobile_money', 'bank'])->default('mobile_money')->after('bank_account_name');
            }
            if (!Schema::hasColumn('users', 'available_balance')) {
                $table->decimal('available_balance', 15, 2)->default(0)->after('payout_method');
            }
            if (!Schema::hasColumn('users', 'pending_balance')) {
                $table->decimal('pending_balance', 15, 2)->default(0)->after('available_balance');
            }
            if (!Schema::hasColumn('users', 'lifetime_earnings')) {
                $table->decimal('lifetime_earnings', 15, 2)->default(0)->after('pending_balance');
            }
            if (!Schema::hasColumn('users', 'total_withdrawn')) {
                $table->decimal('total_withdrawn', 15, 2)->default(0)->after('lifetime_earnings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'staff_id', 'department', 'designation',
                'mobile_money_provider', 'mobile_money_number',
                'bank_name', 'bank_account_number', 'bank_account_name',
                'payout_method', 'available_balance', 'pending_balance',
                'lifetime_earnings', 'total_withdrawn'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};