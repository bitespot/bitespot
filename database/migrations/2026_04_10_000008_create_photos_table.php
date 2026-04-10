<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');

            $table->string('url', 500);           // storage path or CDN URL
            $table->string('caption', 255)->nullable();
            $table->boolean('is_primary')->default(false); // one primary photo per vendor

            $table->timestamps();
            $table->index('vendor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
