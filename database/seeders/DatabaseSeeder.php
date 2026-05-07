<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create an Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@bitespot.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        // 2. Create a generic User
        $user = User::firstOrCreate(
            ['email' => 'user@bitespot.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );

        // 3. Create a Vendor User
        $vendorOwner = User::firstOrCreate(
            ['email' => 'vendor@bitespot.com'],
            [
                'name' => 'Vendor Owner',
                'password' => Hash::make('password'),
                'role' => 'vendor'
            ]
        );

        // Generate additional random users
        $users = User::factory(10)->create();

        // Ensure categories exist (they are seeded in the migration, but just in case we fetch them)
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return; // Safety guard
        }

        // 4. Generate Vendors
        // Create 3 specific vendors for our vendor owner
        for ($i = 1; $i <= 3; $i++) {
            Vendor::factory()->create([
                'user_id' => $vendorOwner->id,
                'category_id' => $categories->random()->id,
                'status' => 'approved',
                'city' => 'Tacloban City',
            ]);
        }

        // Create 15 random vendors assigned to random users
        foreach (range(1, 15) as $i) {
            Vendor::factory()->create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'status' => array_rand(['approved' => 1, 'pending' => 1]),
            ]);
        }

        $this->call([
            VendorPhotoSeeder::class,
            TaclobanVendorSeeder::class,
            ]);
    }
}
