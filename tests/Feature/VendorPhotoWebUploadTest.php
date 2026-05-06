<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Simulates the full web photo-upload flow without needing the vendor UI:
 *   form POST  →  S3 upload  →  vendors table path saved  →  redirect with flash
 */
class VendorPhotoWebUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('s3');
        Category::updateOrCreate(['slug' => 'restaurants'], ['name' => 'Restaurants']);
    }

    // ── helpers ──────────────────────────────────────────────────────────────

    private function makeVendorUser(): User
    {
        return User::factory()->create(['role' => 'vendor']);
    }

    private function makeVendor(User $user): Vendor
    {
        return Vendor::create([
            'user_id'       => $user->id,
            'category_id'   => Category::where('slug', 'restaurants')->firstOrFail()->id,
            'business_name' => 'Sunrise Eatery',
            'slug'          => 'sunrise-eatery',
            'address'       => '1 Main Street, Tacloban City',
            'status'        => 'approved',
        ]);
    }

    // ── cover photo ──────────────────────────────────────────────────────────

    public function test_cover_upload_stores_key_in_db_and_redirects()
    {
        $user   = $this->makeVendorUser();
        $vendor = $this->makeVendor($user);

        $file = UploadedFile::fake()->image('banner.jpg', 1200, 400);

        $response = $this->actingAs($user)
            ->post(route('vendor.photos.cover'), ['cover_photo' => $file]);

        // 1. Redirect back to the photos page with a flash message
        $response->assertRedirect(route('vendor.photos'));
        $response->assertSessionHas('success');

        // 2. DB row has the S3 key stored
        $vendor->refresh();
        $this->assertNotNull($vendor->cover_photo);
        $this->assertStringStartsWith('vendors/covers/', $vendor->cover_photo);

        // 3. File actually exists on (fake) S3
        Storage::disk('s3')->assertExists($vendor->cover_photo);

        // 4. Accessor resolves the stored key → full S3 URL
        $this->assertStringContainsString($vendor->cover_photo, $vendor->cover_photo_url);

        echo "\n  [cover]  DB key   : {$vendor->cover_photo}";
        echo "\n  [cover]  Full URL : {$vendor->cover_photo_url}\n";
    }

    public function test_cover_upload_deletes_old_s3_file_before_replacing()
    {
        $user   = $this->makeVendorUser();
        $vendor = $this->makeVendor($user);

        // Seed an existing cover key on fake S3
        $oldKey = 'vendors/covers/sunrise-eatery-old.jpg';
        Storage::disk('s3')->put($oldKey, 'dummy', 'public');
        $vendor->update(['cover_photo' => $oldKey]);

        $this->actingAs($user)
            ->post(route('vendor.photos.cover'), [
                'cover_photo' => UploadedFile::fake()->image('new-banner.jpg'),
            ]);

        // Old file removed, new file present
        Storage::disk('s3')->assertMissing($oldKey);
        $vendor->refresh();
        Storage::disk('s3')->assertExists($vendor->cover_photo);
    }

    // ── profile photo ────────────────────────────────────────────────────────

    public function test_profile_upload_stores_key_in_db_and_redirects()
    {
        $user   = $this->makeVendorUser();
        $vendor = $this->makeVendor($user);

        $file = UploadedFile::fake()->image('avatar.png', 400, 400);

        $response = $this->actingAs($user)
            ->post(route('vendor.photos.profile'), ['profile_photo' => $file]);

        $response->assertRedirect(route('vendor.photos'));
        $response->assertSessionHas('success');

        $vendor->refresh();
        $this->assertNotNull($vendor->profile_photo);
        $this->assertStringStartsWith('vendors/profiles/', $vendor->profile_photo);
        Storage::disk('s3')->assertExists($vendor->profile_photo);

        $this->assertStringContainsString($vendor->profile_photo, $vendor->profile_photo_url);

        echo "\n  [profile] DB key   : {$vendor->profile_photo}";
        echo "\n  [profile] Full URL : {$vendor->profile_photo_url}\n";
    }

    // ── validation ───────────────────────────────────────────────────────────

    public function test_rejects_non_image_file()
    {
        $user = $this->makeVendorUser();
        $this->makeVendor($user);

        $response = $this->actingAs($user)
            ->post(route('vendor.photos.cover'), [
                'cover_photo' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            ]);

        $response->assertSessionHasErrors('cover_photo');
    }

    public function test_rejects_file_over_5mb()
    {
        $user = $this->makeVendorUser();
        $this->makeVendor($user);

        $response = $this->actingAs($user)
            ->post(route('vendor.photos.cover'), [
                'cover_photo' => UploadedFile::fake()->create('huge.jpg', 6144),
            ]);

        $response->assertSessionHasErrors('cover_photo');
    }

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->post(route('vendor.photos.cover'), [
            'cover_photo' => UploadedFile::fake()->image('x.jpg'),
        ]);

        $response->assertRedirect(route('login'));
    }
}
