<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 100)->nullable(); // e.g. heroicon name or emoji
            $table->timestamps();
        });

        // Seed the five platform categories defined in FR-U02
        \DB::table('categories')->insert([
            ['name' => 'Restaurants',  'slug' => 'restaurants',  'icon' => 'utensils',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Street Food',  'slug' => 'street-food',  'icon' => 'shopping-bag',  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cafés',        'slug' => 'cafes',        'icon' => 'coffee',        'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desserts',     'slug' => 'desserts',     'icon' => 'cake',          'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Drinks',       'slug' => 'drinks',       'icon' => 'glass-water',   'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
