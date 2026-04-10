<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Laravel Breeze scaffolds a basic `users` table.
 * This migration EXTENDS it with BiteSpot-specific columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add after 'email_verified_at' if it exists, otherwise just append
            $table->string('avatar')->nullable()->after('email');
            $table->string('location', 255)->nullable()->after('avatar');
            // role column used by RBAC middleware (user | vendor | admin)
            $table->enum('role', ['user', 'vendor', 'admin'])->default('user')->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'location', 'role']);
        });
    }
};
