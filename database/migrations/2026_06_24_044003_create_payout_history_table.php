<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payout_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payout_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained(); // Who made the change
            
            // Status change tracking
            $table->enum('old_status', [
                'pending', 'pending_details', 'processing', 
                'completed', 'failed', 'reversed', 'cancelled'
            ]);
            $table->enum('new_status', [
                'pending', 'pending_details', 'processing', 
                'completed', 'failed', 'reversed', 'cancelled'
            ]);
            
            // Change details
            $table->string('action')->nullable(); // initiated, processed, completed, etc.
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('payout_id');
            $table->index(['old_status', 'new_status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_history');
    }
};