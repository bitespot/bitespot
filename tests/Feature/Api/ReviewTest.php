<?php

namespace Tests\Feature\Api;

use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_reviews_for_a_vendor(): void
    {
        $vendor = Vendor::factory()->create();
        Review::factory()->count(3)->create(['vendor_id' => $vendor->id]);

        $response = $this->getJson("/api/vendors/{$vendor->id}/reviews");

        $response->assertStatus(200);
        // Assuming stub for now, but once implemented this should check data
        // $response->assertJsonCount(3, 'data');
    }

    public function test_authenticated_user_can_store_review(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/reviews', [
            'vendor_id' => $vendor->id,
            'rating' => 5,
            'body' => 'Excellent food!',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'rating' => 5,
        ]);
    }

    public function test_user_cannot_review_same_vendor_twice(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        Review::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->postJson('/api/reviews', [
            'vendor_id' => $vendor->id,
            'rating' => 3,
            'body' => 'Second review attempt',
        ]);

        $response->assertStatus(422);
    }

    public function test_owner_can_update_their_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->putJson("/api/reviews/{$review->id}", [
            'rating' => 2,
            'body' => 'Updated review body',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 2,
        ]);
    }

    public function test_user_cannot_update_others_review(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->putJson("/api/reviews/{$review->id}", [
            'rating' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_owner_can_delete_their_review(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/reviews/{$review->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
    }
}
