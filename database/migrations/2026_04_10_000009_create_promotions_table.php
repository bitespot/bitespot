<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->decimal('discount', 5, 2)->nullable(); // percentage e.g. 20.00
            $table->timestamp('valid_until')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->index('vendor_id');
            $table->index('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
