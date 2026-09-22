<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class VendorPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = Vendor::all();

        if ($vendors->isEmpty()) {
            $this->command->warn('No vendors found. Run DatabaseSeeder first.');
            return;
        }

        $bar = $this->command->getOutput()->createProgressBar($vendors->count() * 2);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%\n");
        $bar->start();

        $disk = (config('filesystems.default', 'public') === 's3' && config('filesystems.disks.s3.key')) ? 's3' : 'public';

        foreach ($vendors as $vendor) {
            // Use slug as seed so the same vendor always gets the same image
            $seed = $vendor->slug;

            // Cover photo — landscape 1200×400
            $bar->setMessage("cover  → {$vendor->business_name}");
            $coverKey = "vendors/covers/{$seed}.jpg";
            try {
                $cover = Http::timeout(2)->get("https://picsum.photos/seed/{$seed}-cover/1200/400");
                if ($cover->successful()) {
                    Storage::disk($disk)->put($coverKey, $cover->body(), 'public');
                }
                $vendor->update(['cover_photo' => $coverKey]);
            } catch (\Throwable $e) {
                $vendor->update(['cover_photo' => $coverKey]);
            }
            $bar->advance();

            // Profile photo — square 400×400
            $bar->setMessage("profile → {$vendor->business_name}");
            $profileKey = "vendors/profiles/{$seed}.jpg";
            try {
                $profile = Http::timeout(2)->get("https://picsum.photos/seed/{$seed}-profile/400/400");
                if ($profile->successful()) {
                    Storage::disk($disk)->put($profileKey, $profile->body(), 'public');
                }
                $vendor->update(['profile_photo' => $profileKey]);
            } catch (\Throwable $e) {
                $vendor->update(['profile_photo' => $profileKey]);
            }
            $bar->advance();
        }

        $bar->finish();

        $this->command->newLine();
        $this->command->info("Photos seeded for {$vendors->count()} vendors.");
        $this->command->newLine();

        // Print a summary table
        $this->command->table(
            ['Vendor', 'Cover Key', 'Profile Key'],
            Vendor::all()->map(fn($v) => [
                $v->business_name,
                $v->cover_photo   ?? '—',
                $v->profile_photo ?? '—',
            ])->toArray()
        );
    }
}
