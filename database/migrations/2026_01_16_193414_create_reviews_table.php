<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade')->comment('User giving the review');
            $table->foreignId('reviewee_id')->constrained('users')->onDelete('cascade')->comment('User receiving the review');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('cascade');
            
            // Review Details
            $table->enum('type', ['driver', 'vehicle', 'service', 'passenger', 'owner'])->default('service');
            $table->integer('rating')->unsigned()->between(1, 5);
            
            // Detailed Ratings (for different aspects)
            $table->integer('punctuality_rating')->unsigned()->between(1, 5)->nullable();
            $table->integer('cleanliness_rating')->unsigned()->between(1, 5)->nullable();
            $table->integer('comfort_rating')->unsigned()->between(1, 5)->nullable();
            $table->integer('safety_rating')->unsigned()->between(1, 5)->nullable();
            $table->integer('communication_rating')->unsigned()->between(1, 5)->nullable();
            $table->integer('value_rating')->unsigned()->between(1, 5)->nullable();
            
            // Review Content
            $table->string('title')->nullable();
            $table->text('comment');
            $table->text('positive_points')->nullable();
            $table->text('negative_points')->nullable();
            $table->text('suggestions')->nullable();
            
            // Verification and Status
            $table->boolean('is_verified')->default(false)->comment('Verified booking');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(true)->comment('Admin can hide inappropriate reviews');
            $table->enum('status', ['pending', 'published', 'hidden', 'flagged'])->default('published');
            
            // Responses
            $table->text('owner_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            // Helpfulness
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            // Reporting
            $table->integer('report_count')->default(0);
            $table->text('report_reason')->nullable();
            
            // Media
            $table->json('images')->nullable()->comment('JSON array of image paths');
            $table->json('videos')->nullable()->comment('JSON array of video paths');
            
            // Analytics
            $table->integer('view_count')->default(0);
            $table->json('reactions')->nullable()->comment('JSON of various reactions');
            
            // Metadata
            $table->string('ip_address')->nullable();
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for optimized queries
            $table->index('booking_id');
            $table->index('reviewer_id');
            $table->index('reviewee_id');
            $table->index('vehicle_id');
            $table->index('type');
            $table->index('rating');
            $table->index('is_verified');
            $table->index('is_approved');
            $table->index('status');
            $table->index('created_at');
            $table->unique(['booking_id', 'reviewer_id', 'type']);
            $table->fulltext(['title', 'comment', 'positive_points', 'negative_points']);
        });
    }

    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropForeign(['reviewee_id']);
            $table->dropForeign(['vehicle_id']);
        });
        Schema::dropIfExists('reviews');
    }
}