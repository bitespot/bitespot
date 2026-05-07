<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Sold Out', 'Hidden'])
                  ->default('Active')
                  ->after('is_available');
        });

        // Backfill: any existing rows with is_available=false become 'Sold Out'
        DB::table('menu_items')
            ->where('is_available', false)
            ->update(['status' => 'Sold Out']);
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
