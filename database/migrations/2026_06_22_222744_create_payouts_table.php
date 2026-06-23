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
    $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    $table->decimal('amount', 10, 2);
    $table->decimal('platform_fee', 10, 2)->default(0);
    $table->string('recipient_phone');
    $table->string('provider')->default('airtel_money'); // airtel_money, tnm_mpamba
    $table->string('reference')->unique();
    $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
    $table->text('response')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();
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
