<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            // When the user first visited / marked this bitespot as discovered
            $table->timestamp('discovered_at')->useCurrent();

            $table->timestamps();

            // A user can only discover a vendor once
            $table->unique(['user_id', 'vendor_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discoveries');
    }
};
