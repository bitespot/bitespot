<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('photo')->nullable();

            // e.g. "Mains", "Drinks", "Desserts" — free-text per FR-V03
            $table->string('category', 100)->nullable();

            $table->boolean('is_available')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0); // drag-to-reorder

            $table->timestamps();

            $table->index('vendor_id');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
