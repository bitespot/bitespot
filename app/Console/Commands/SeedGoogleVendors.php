<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SeedGoogleVendors extends Command
{
    protected $signature = 'seed:google-vendors';
    protected $description = 'Seed sample restaurants in Tacloban (no photos, no API keys needed)';

    private $sampleVendors = [
        // Restaurants
        ['name' => 'Lantaw Native Restaurant', 'category' => 'restaurants', 'address' => 'Maharlika St, Tacloban City', 'price' => '$$', 'lat' => 11.2502, 'lng' => 125.0037, 'phone' => '(053) 323-2411', 'website' => 'lantaw.com'],
        ['name' => 'Cafe Mariposa', 'category' => 'restaurants', 'address' => 'Real St, Tacloban City', 'price' => '$$', 'lat' => 11.2486, 'lng' => 125.0042, 'phone' => '(053) 321-8900'],
        ['name' => 'Shrimp House', 'category' => 'restaurants', 'address' => 'San Juanico Bridge Access Rd', 'price' => '$$$', 'lat' => 11.2520, 'lng' => 125.0055, 'website' => 'shrimphouse.ph'],
        ['name' => 'Mabinays Dining', 'category' => 'restaurants', 'address' => 'Washington St, Tacloban City', 'price' => '$$', 'lat' => 11.2448, 'lng' => 125.0071, 'phone' => '(053) 322-1234'],
        ['name' => 'Abe Restaurant', 'category' => 'restaurants', 'address' => 'Magsaysay Blvd, Tacloban City', 'price' => '$', 'lat' => 11.2401, 'lng' => 125.0093, 'phone' => '(053) 325-5678'],
        ['name' => 'Casa da Jose', 'category' => 'restaurants', 'address' => 'Rizal Ave, Tacloban City', 'price' => '$$$', 'lat' => 11.2530, 'lng' => 125.0025],
        ['name' => 'Fiesta Filipino', 'category' => 'restaurants', 'address' => 'San Juanico Blvd, Tacloban City', 'price' => '$$', 'lat' => 11.2515, 'lng' => 125.0060],

        // Cafes
        ['name' => 'Starbucks Tacloban', 'category' => 'cafes', 'address' => 'SM City Tacloban', 'price' => '$$', 'lat' => 11.2540, 'lng' => 125.0100, 'phone' => '(053) 321-0001', 'website' => 'starbucks.ph'],
        ['name' => 'The Coffee Bean & Tea Leaf', 'category' => 'cafes', 'address' => 'Real St, Tacloban City', 'price' => '$$', 'lat' => 11.2490, 'lng' => 125.0048],
        ['name' => 'Kape Batuan', 'category' => 'cafes', 'address' => 'Magsaysay Ave, Tacloban City', 'price' => '$', 'lat' => 11.2455, 'lng' => 125.0080],
        ['name' => 'Brew & Bean', 'category' => 'cafes', 'address' => 'Juan Luna St, Tacloban City', 'price' => '$', 'lat' => 11.2410, 'lng' => 125.0065],
        ['name' => 'Cappuccino Bar', 'category' => 'cafes', 'address' => 'San Juanico Bridge, Tacloban City', 'price' => '$$', 'lat' => 11.2508, 'lng' => 125.0120],

        // Bakeries
        ['name' => 'Goldilocks Bakery', 'category' => 'desserts', 'address' => 'SM City Tacloban', 'price' => '$', 'lat' => 11.2545, 'lng' => 125.0105, 'phone' => '(053) 321-0200', 'website' => 'goldilocks.ph'],
        ['name' => 'Reyes Bakery', 'category' => 'desserts', 'address' => 'Magsaysay Blvd, Tacloban City', 'price' => '$', 'lat' => 11.2435, 'lng' => 125.0075],
        ['name' => 'Hometown Bakery', 'category' => 'desserts', 'address' => 'Real St, Tacloban City', 'price' => '$', 'lat' => 11.2480, 'lng' => 125.0040],
        ['name' => 'Savoy Cake House', 'category' => 'desserts', 'address' => 'Washington St, Tacloban City', 'price' => '$$', 'lat' => 11.2445, 'lng' => 125.0085],
        ['name' => 'La Panadera', 'category' => 'desserts', 'address' => 'Rizal Ave, Tacloban City', 'price' => '$', 'lat' => 11.2520, 'lng' => 125.0030],
    ];

    public function handle()
    {
        // Verify categories exist
        $requiredCategories = ['restaurants', 'cafes', 'desserts'];
        foreach ($requiredCategories as $slug) {
            if (!Category::where('slug', $slug)->exists()) {
                $this->error("Category '{$slug}' not found. Please seed categories first.");
                return 1;
            }
        }

        $this->info("🍽️ BiteSpot Seeder - Sample Data (No Photos, No API Keys)");
        $this->newLine();

        // Ensure a system user exists
        $userId = $this->ensureSystemUser();

        if (!$userId) {
            $this->error("No user found. Please create a user first.");
            return 1;
        }

        $this->seedSampleData($userId);

        $this->newLine();
        $this->info("✓ Seeding complete!");
        return 0;
    }

    private function ensureSystemUser()
    {
        // Try to get the first user
        $user = User::first();

        if ($user) {
            $this->line("  Using existing user: {$user->email}");
            return $user->id;
        }

        // Create a system admin user if none exists
        $this->info("  No users found. Creating system admin...");

        try {
            $user = User::create([
                'name' => 'System Admin',
                'email' => 'admin@bitespot.local',
                'password' => Hash::make('password'),
                'email_verified_at' => Carbon::now(),
            ]);

            $this->line("  ✓ Created: {$user->email}");
            return $user->id;
        } catch (\Exception $e) {
            $this->error("Failed to create system user: {$e->getMessage()}");
            return null;
        }
    }

    private function seedSampleData($userId)
    {
        $this->info("📍 Creating sample restaurants in Tacloban...");

        $created = 0;
        $bar = $this->output->createProgressBar(count($this->sampleVendors));
        $bar->start();

        foreach ($this->sampleVendors as $data) {
            if ($this->createVendor($data, $userId)) {
                $created++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("  ✓ Created {$created} restaurants");
    }

    private function createVendor($data, $userId)
    {
        if (Vendor::where('business_name', $data['name'])->exists()) {
            return false;
        }

        $categorySlug = $data['category'];
        $categoryId = Category::where('slug', $categorySlug)->value('id');

        if (!$categoryId) {
            return false;
        }

        try {
            // Generate sample S3 photo paths (format: s3://bucket-name/photos/{vendor_id}.jpg)
            $photoId = Str::random(12);
            
            Vendor::create([
                'user_id' => $userId,
                'category_id' => $categoryId,
                'business_name' => $data['name'],
                'slug' => Str::slug($data['name'] . '-' . Str::random(3)),
                'description' => 'Local establishment in Tacloban City.',
                'phone' => $data['phone'] ?? null,
                'website' => $data['website'] ?? null,
                'address' => $data['address'],
                'city' => 'Tacloban City',
                'province' => 'Leyte',
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'cover_photo' => "photos/cover_{$photoId}.jpg",
                'profile_photo' => "photos/profile_{$photoId}.jpg",
                'price_tier' => $data['price'],
                'tier' => 'free',
                'status' => 'approved',
                'avg_rating' => rand(35, 50) / 10,
                'review_count' => rand(5, 150),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}





