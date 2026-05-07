<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TaclobanVendorSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Load the JSON Data
        $jsonPath = database_path('data/tacloban_vendors.json');
        if (!File::exists($jsonPath)) {
            $this->command->warn("Data file not found at {$jsonPath}");
            return;
        }

        $vendorsData = json_decode(File::get($jsonPath), true);
        $categories = Category::all();

        foreach ($vendorsData as $data) {
            $this->command->info("Seeding: {$data['business_name']}");

            // 2. Create a dedicated User (Owner) for this Vendor
            $owner = User::firstOrCreate(
                ['email' => $data['slug'] . '@bitespot.com'],
                [
                    'name' => $data['business_name'] . ' Owner',
                    'password' => Hash::make('password'),
                    'role' => 'vendor',
                    'location' => 'Tacloban City'
                ]
            );

            // 3. Find the matching category ID
            $category = $categories->where('name', $data['category'])->first();

            // 4. Handle Photos (Upload Local to S3)
            $coverKey = $this->uploadPhoto($data['photos']['cover'], "vendors/covers/{$data['slug']}.jpg");
            $profileKey = $this->uploadPhoto($data['photos']['profile'], "vendors/profiles/{$data['slug']}.jpg");

            // 5. Create the Vendor Record
            $vendor = Vendor::updateOrCreate(
                ['slug' => $data['slug']], // Don't duplicate if re-running
                [
                    'user_id' => $owner->id,
                    'category_id' => $category ? $category->id : null,
                    'business_name' => $data['business_name'],
                    'description' => $data['description'],
                    'address' => $data['address'],
                    'district' => $data['district'] ?? null,
                    'city' => $data['city'],
                    'lat' => $data['lat'],
                    'lng' => $data['lng'],
                    'phone' => $data['phone'] ?? null,
                    'hours' => $data['hours'] ?? null,
                    'price_tier' => $data['price_tier'],
                    'status' => 'approved',
                    'cover_photo' => $coverKey,
                    'profile_photo' => $profileKey,
                ]
            );

            // 6. Seed Menu Items
            if (isset($data['menu']) && is_array($data['menu'])) {
                foreach ($data['menu'] as $menuData) {
                    $menuPhotoKey = $this->uploadPhoto(
                        $menuData['photo'], 
                        "vendors/menu/{$vendor->slug}/" . ($menuData['photo'] ?? 'null')
                    );

                    MenuItem::updateOrCreate(
                        ['vendor_id' => $vendor->id, 'name' => $menuData['name']],
                        [
                            'description' => $menuData['description'],
                            'price' => $menuData['price'],
                            'category' => $menuData['category'],
                            'photo' => $menuPhotoKey,
                            'is_available' => true,
                        ]
                    );
                }
            }
        }
    }

    /**
     * Helper to read local file and push to S3
     */
    private function uploadPhoto(?string $filename, string $s3Path): ?string
    {
        if (!$filename) return null;

        $localPath = database_path("data/photos/{$filename}");
        
        if (File::exists($localPath)) {
            $fileContents = File::get($localPath);
            Storage::disk('s3')->put($s3Path, $fileContents);
            return $s3Path;
        }

        $this->command->warn("Photo not found locally: {$filename}");
        return null;
    }
}