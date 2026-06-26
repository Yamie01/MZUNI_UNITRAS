<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            
            // === RELATIONSHIPS ===
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The vehicle owner
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            
            // === MZUNI STAFF INFORMATION ===
            $table->string('staff_id')->nullable();
            $table->string('staff_name')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable(); // Lecturer, Admin, etc.
            
            // === REVENUE SPLIT (80/20) ===
            $table->decimal('total_amount', 15, 2); // Total paid by passenger
            $table->decimal('owner_share', 15, 2); // 80% to staff owner
            $table->decimal('platform_share', 15, 2); // 20% to platform
            $table->decimal('amount', 15, 2); // Owner share (kept for compatibility)
            
            // === PAYOUT METHOD: MOBILE MONEY ===
            $table->enum('payout_method', ['mobile_money', 'bank'])->default('mobile_money');
            $table->string('mobile_money_provider')->nullable(); // airtel_money, tnm_mpamba
            $table->string('mobile_money_number')->nullable();
            $table->boolean('mobile_money_verified')->default(false);
            
            // === PAYOUT METHOD: BANK TRANSFER ===
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->boolean('bank_account_verified')->default(false);
            
            // === PAYOUT TRACKING ===
            $table->string('reference')->unique(); // System reference
            $table->string('payout_reference')->nullable()->unique(); // PayChangu reference
            $table->string('provider_reference')->nullable(); // Provider transaction ID
            
            // === STATUS TRACKING ===
            $table->enum('status', [
                'pending',           // Created, waiting to be processed
                'pending_details',   // Owner needs to update payout details
                'processing',        // Being processed by PayChangu
                'completed',         // Successfully paid
                'failed',            // Failed
                'reversed',          // Reversed/refunded
                'cancelled'          // Cancelled by admin
            ])->default('pending');
            
            // === RESPONSE & ERROR TRACKING ===
            $table->json('request_payload')->nullable(); // What we sent to PayChangu
            $table->json('response_payload')->nullable(); // What PayChangu returned
            $table->text('error_message')->nullable();
            $table->text('admin_notes')->nullable(); // For admin comments
            
            // === TIMESTAMPS ===
            $table->timestamp('initiated_at')->nullable(); // When payout was initiated
            $table->timestamp('processed_at')->nullable(); // When processing started
            $table->timestamp('completed_at')->nullable(); // When completed
            $table->timestamp('failed_at')->nullable(); // When failed
            
            // === METADATA ===
            $table->json('metadata')->nullable(); // Additional data
            $table->json('audit_trail')->nullable(); // Audit log of status changes
            
            // === SYSTEM FIELDS ===
            $table->timestamps();
            $table->softDeletes(); // Soft delete for safety
            
            // === INDEXES ===
            $table->index(['booking_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['staff_id', 'status']);
            $table->index(['payout_method', 'status']);
            $table->index('payout_reference');
            $table->index('reference');
            $table->index('created_at');
            $table->index(['status', 'created_at']);
            
            // === COMPOSITE INDEXES ===
            $table->index(['user_id', 'payout_method', 'status']);
            $table->index(['staff_id', 'department']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};