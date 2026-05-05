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
        
        // Fake S3 for testing
        Storage::fake('s3');
    }

    public function test_vendor_can_upload_cover_photo()
    {
        // Create user and vendor
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Restaurants', 'slug' => 'restaurants']);
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'business_name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'lat' => 11.25,
            'lng' => 125.00,
            'status' => 'approved',
        ]);

        // Act: Upload a file
        $file = UploadedFile::fake()->image('cover.jpg', 400, 300, 'jpeg');
        
        $response = $this->actingAs($user)->post('/api/vendor/photos/cover', [
            'cover_photo' => $file,
        ]);

        // Assert: 200 response
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'cover_photo',
            'cover_photo_url',
        ]);

        // Assert: File was stored in S3
        $this->assertNotNull($response->json('cover_photo'));
        Storage::disk('s3')->assertExists($response->json('cover_photo'));

        // Assert: Database was updated
        $vendor->refresh();
        $this->assertNotNull($vendor->cover_photo);
    }

    public function test_vendor_can_upload_profile_photo()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Restaurants', 'slug' => 'restaurants']);
        $vendor = Vendor::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'business_name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'lat' => 11.25,
            'lng' => 125.00,
            'status' => 'approved',
        ]);

        $file = UploadedFile::fake()->image('profile.jpg', 200, 200, 'jpeg');
        
        $response = $this->actingAs($user)->post('/api/vendor/photos/profile', [
            'profile_photo' => $file,
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('profile_photo'));
    }

    public function test_unauthorized_user_cannot_upload()
    {
        $file = UploadedFile::fake()->image('cover.jpg');
        
        $response = $this->post('/api/vendor/photos/cover', [
            'cover_photo' => $file,
        ]);

        $response->assertStatus(401);
    }

    public function test_file_validation()
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Restaurants', 'slug' => 'restaurants']);
        Vendor::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'business_name' => 'Test Restaurant',
            'slug' => 'test-restaurant',
            'lat' => 11.25,
            'lng' => 125.00,
            'status' => 'approved',
        ]);

        // Test: File too large (6MB > 5MB limit)
        $file = UploadedFile::fake()->create('large.jpg', 6144);
        
        $response = $this->actingAs($user)->post('/api/vendor/photos/cover', [
            'cover_photo' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cover_photo');
    }
}
