<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('s3');

        // Migration already seeds categories; firstOrCreate is safe if they exist.
        // Using updateOrCreate avoids a unique-constraint crash on repeat setUp calls
        // within the same in-memory DB (e.g. when RefreshDatabase wraps with transactions).
        Category::updateOrCreate(
            ['slug' => 'restaurants'],
            ['name' => 'Restaurants']
        );
    }

    // ── helpers ────────────────────────────────────────────────────────────

    private function makeVendorUser(): User
    {
        return User::factory()->create(['role' => 'vendor']);
    }

    private function makeVendor(User $user): Vendor
    {
        $category = Category::where('slug', 'restaurants')->firstOrFail();

        return Vendor::create([
            'user_id'       => $user->id,
            'category_id'   => $category->id,
            'business_name' => 'Test Restaurant',
            'slug'          => 'test-restaurant',
            'address'       => '123 Test Street, Test City',
            'lat'           => 11.25,
            'lng'           => 125.00,
            'status'        => 'approved',
        ]);
    }

    // ── tests ──────────────────────────────────────────────────────────────

    public function test_vendor_can_upload_cover_photo()
    {
        $user   = $this->makeVendorUser();
        $vendor = $this->makeVendor($user);

        $file = UploadedFile::fake()->image('cover.jpg', 400, 300, 'jpeg');

        $response = $this->actingAs($user)
            ->postJson('/api/vendor/photos/cover', ['cover_photo' => $file]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'cover_photo', 'cover_photo_url']);

        $this->assertNotNull($response->json('cover_photo'));
        Storage::disk('s3')->assertExists($response->json('cover_photo'));

        $vendor->refresh();
        $this->assertNotNull($vendor->cover_photo);
    }

    public function test_vendor_can_upload_profile_photo()
    {
        $user   = $this->makeVendorUser();
        $vendor = $this->makeVendor($user);

        $file = UploadedFile::fake()->image('profile.jpg', 200, 200, 'jpeg');

        $response = $this->actingAs($user)
            ->postJson('/api/vendor/photos/profile', ['profile_photo' => $file]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('profile_photo'));

        $vendor->refresh();
        $this->assertNotNull($vendor->profile_photo);
    }

    public function test_unauthorized_user_cannot_upload()
    {
        $file = UploadedFile::fake()->image('cover.jpg');

        // postJson sends Accept: application/json → auth middleware returns 401 (not redirect)
        $response = $this->postJson('/api/vendor/photos/cover', ['cover_photo' => $file]);

        $response->assertStatus(401);
    }

    public function test_file_validation()
    {
        $user = $this->makeVendorUser();
        $this->makeVendor($user);

        // 6 MB file exceeds the 5 MB (5120 KB) limit
        $file = UploadedFile::fake()->create('large.jpg', 6144);

        $response = $this->actingAs($user)
            ->postJson('/api/vendor/photos/cover', ['cover_photo' => $file]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cover_photo');
    }
}
