<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();

            // Owner — references the users table (vendor owner's account)
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Category — nullable so vendor can register before picking a category
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories')
                  ->onDelete('set null');

            // Business identity
            $table->string('business_name', 255);
            $table->string('slug', 255)->unique();          // URL-friendly name /place/{slug}
            $table->text('description')->nullable();
            $table->string('owner_name', 255)->nullable();

            // Contact
            $table->string('phone', 30)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('email', 255)->nullable();

            // Location
            $table->string('address', 500);
            $table->string('district', 100)->nullable();   // e.g. "Downtown Tacloban"
            $table->string('city', 100)->default('Tacloban City');
            $table->string('province', 100)->default('Leyte');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Operating hours stored as JSON: {"mon": "08:00-22:00", "tue": "08:00-22:00", ...}
            $table->json('hours')->nullable();

            // Visuals
            $table->string('cover_photo')->nullable();      // banner image path/URL
            $table->string('profile_photo')->nullable();    // thumbnail/avatar

            // Business details
            $table->enum('price_tier', ['$', '$$', '$$$'])->default('$');

            // Subscription tier (FR-V06 / Monetization)
            $table->enum('tier', ['free', 'basic', 'premium'])->default('free');

            // Approval workflow (FR-A01)
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();

            // Denormalized counters (updated by observers for fast reads)
            $table->unsignedInteger('view_count')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('review_count')->default(0);

            $table->timestamps();
            $table->softDeletes(); // allows admin to soft-delete without losing data

            // Indexes for search/explore queries
            $table->index('status');
            $table->index('city');
            $table->index('price_tier');
            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
