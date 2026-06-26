<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_payout_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // MZUNI Staff Information
            $table->string('staff_id')->unique();
            $table->string('department');
            $table->string('designation')->nullable();
            $table->string('office_location')->nullable();
            $table->string('staff_email')->nullable();
            $table->string('staff_phone')->nullable();
            
            // Payout Preferences
            $table->enum('preferred_payout_method', ['mobile_money', 'bank'])->default('mobile_money');
            $table->decimal('minimum_payout_threshold', 15, 2)->default(1000); // Minimum amount to request payout
            
            // Mobile Money Details
            $table->string('mobile_money_provider')->nullable();
            $table->string('mobile_money_number')->nullable();
            $table->boolean('mobile_money_verified')->default(false);
            $table->timestamp('mobile_money_verified_at')->nullable();
            
            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->boolean('bank_account_verified')->default(false);
            $table->timestamp('bank_account_verified_at')->nullable();
            
            // Verification
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamp('next_payout_eligible_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('staff_id');
            $table->index('mobile_money_number');
            $table->index('bank_account_number');
            $table->index(['is_active', 'preferred_payout_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_payout_details');
    }
};