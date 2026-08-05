<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_revenues', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('rides_revenue', 15, 2)->default(0);
            $table->decimal('rentals_revenue', 15, 2)->default(0);
            $table->decimal('subscriptions_revenue', 15, 2)->default(0);
            $table->json('breakdown')->nullable();
            $table->timestamps();
            
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_revenues');
    }
};