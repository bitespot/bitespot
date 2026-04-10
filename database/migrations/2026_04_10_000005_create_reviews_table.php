<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            $table->unsignedTinyInteger('rating'); // 1–5, validated at app layer
            $table->text('body')->nullable();

            // Soft-delete allows admins to remove reviews (FR-A02) without losing audit trail
            $table->timestamps();
            $table->softDeletes();

            // One review per user per vendor (enforced at app layer + unique index)
            $table->unique(['user_id', 'vendor_id']);
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
