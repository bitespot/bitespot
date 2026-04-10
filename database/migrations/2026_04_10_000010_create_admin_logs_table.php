<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');

            // e.g. 'approved_vendor', 'rejected_vendor', 'removed_review', 'banned_user'
            $table->string('action', 100);

            // Polymorphic-style target (avoids requiring all target tables up front)
            $table->string('target_type', 100)->nullable(); // 'vendor', 'review', 'user'
            $table->unsignedBigInteger('target_id')->nullable();

            $table->text('notes')->nullable(); // e.g. rejection reason, ban reason

            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_logs');
    }
};
