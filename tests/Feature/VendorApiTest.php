<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_register_new_vendor()
    {
        $user = User::factory()->create();
        $category = Category::firstOrCreate(['slug' => 'cafes'], ['name' => 'Cafes']);

        // Authenticate using the default web guard instead of sanctum for simpler testing 
        // since the test acts directly as a user instance.
        $response = $this->actingAs($user)->postJson('/api/vendor/register', [
            'business_name' => 'The Cozy Cafe',
            'description' => 'A very nice place to study.',
            'category_id' => $category->id,
            'address' => '123 Main St.',
            'city' => 'Tacloban City',
            'phone' => '123456789'
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('vendor.business_name', 'The Cozy Cafe')
                 ->assertJsonPath('vendor.status', 'pending');

        $this->assertDatabaseHas('vendors', [
            'business_name' => 'The Cozy Cafe',
            'user_id' => $user->id
        ]);
    }

    public function test_can_search_and_filter_approved_vendors()
    {
        $user = User::factory()->create();
        
        $c1 = Category::firstOrCreate(['slug' => 'cafes'], ['name' => 'Cafes']);
        $c2 = Category::firstOrCreate(['slug' => 'restaurants'], ['name' => 'Restaurants']);

        // Create an approved vendor
        Vendor::create([
            'user_id' => $user->id,
            'category_id' => $c1->id,
            'business_name' => 'Cozy Cafe',
            'slug' => 'cozy-cafe',
            'address' => '123 Main St',
            'status' => 'approved'
        ]);

        // Create a pending vendor (should not show up in search)
        Vendor::create([
            'user_id' => $user->id,
            'category_id' => $c2->id,
            'business_name' => 'Secret Spot',
            'slug' => 'secret-spot',
            'address' => '456 Hidden St',
            'status' => 'pending'
        ]);

        // Basic index should return 1
        $response = $this->getJson('/api/vendors');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.business_name', 'Cozy Cafe');

        // Search by category
        $responseCat = $this->getJson('/api/vendors?category=cafes');
        $responseCat->assertStatus(200)
                    ->assertJsonCount(1, 'data');
                    
        $responseCatEmpty = $this->getJson('/api/vendors?category=restaurants');
        $responseCatEmpty->assertStatus(200)
                         ->assertJsonCount(0, 'data'); // Pending vendor is in restaurants, shouldn't show
    }
}
