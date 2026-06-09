<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['weekly', 'monthly']);
            $table->enum('status', ['active', 'expired', 'cancelled', 'pending'])->default('pending');
            $table->decimal('price', 10, 2);
            $table->dateTime('start_date');  // Changed from timestamp to dateTime
            $table->dateTime('end_date');    // Changed from timestamp to dateTime
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
};