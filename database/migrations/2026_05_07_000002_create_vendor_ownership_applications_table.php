<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_ownership_applications', function (Blueprint $table) {
            $table->id();

            // User claiming ownership
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Existing vendor being claimed
            $table->foreignId('vendor_id')
                  ->constrained('vendors')
                  ->onDelete('cascade');

            // Supporting documents (JSON: paths to uploaded files)
            $table->json('documents')->nullable();    // ['doc1.pdf', 'doc2.jpg']

            // Application details
            $table->text('reason')->nullable();        // Why they're claiming ownership
            $table->enum('status', ['pending', 'approved', 'rejected', 'withdrawn'])
                  ->default('pending');
            $table->text('admin_notes')->nullable();   // Admin rejection/approval notes

            // Admin action tracking
            $table->foreignId('reviewed_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['user_id', 'vendor_id']);
            $table->unique(['user_id', 'vendor_id']); // One application per user per vendor
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_ownership_applications');
    }
};
