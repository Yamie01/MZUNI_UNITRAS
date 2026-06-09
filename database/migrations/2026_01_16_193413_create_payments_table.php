<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_owner_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Payment Identification
            $table->string('transaction_id')->unique()->comment('Internal transaction ID');
            $table->string('gateway_transaction_id')->nullable()->comment('Payment gateway transaction ID');
            $table->string('reference_number')->nullable()->comment('Mobile money reference');
            
            // Payment Details
            $table->decimal('amount', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('fee_amount', 12, 2)->default(0)->comment('Gateway/processing fee');
            $table->decimal('net_amount', 12, 2)->comment('Amount after fees');
            $table->string('currency')->default('MWK');
            
            // Payment Method
            $table->enum('payment_method', [
                'mobile_money', 
                'card', 
                'cash', 
                'bank_transfer',
                'wallet',
                'voucher'
            ]);
            
            // For mobile money
            $table->string('mobile_money_number')->nullable();
            $table->enum('mobile_money_provider', ['airtel_money', 'tnm_mpamba', 'none'])->nullable();
            
            // For card payments
            $table->string('card_last_four')->nullable();
            $table->string('card_brand')->nullable();
            
            // Status
            $table->enum('status', [
                'initiated', 
                'pending', 
                'processing', 
                'completed', 
                'failed', 
                'refunded',
                'partially_refunded',
                'disputed',
                'cancelled'
            ])->default('initiated');
            
            $table->text('failure_reason')->nullable();
            
            // Payment Gateway Information
            $table->string('payment_gateway')->nullable()->comment('Stripe, Flutterwave, etc.');
            $table->json('gateway_response')->nullable()->comment('Full response from payment gateway');
            $table->json('gateway_metadata')->nullable()->comment('Additional gateway data');
            
            // Refund Information
            $table->boolean('is_refunded')->default(false);
            $table->decimal('refund_amount', 12, 2)->default(0);
            $table->text('refund_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('refund_transaction_id')->nullable();
            
            // Timing
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->comment('For pending payments');
            
            // Additional Information
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->text('receipt_url')->nullable();
            $table->boolean('receipt_sent')->default(false);
            
            $table->timestamps();
            
            // Indexes for optimized queries
            $table->index('transaction_id');
            $table->index('booking_id');
            $table->index('user_id');
            $table->index('vehicle_owner_id');
            $table->index('payment_method');
            $table->index('status');
            $table->index(['status', 'created_at']);
            $table->index('gateway_transaction_id');
            $table->index('reference_number');
            $table->index('completed_at');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['vehicle_owner_id']);
        });
        Schema::dropIfExists('payments');
    }
}