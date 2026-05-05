<?php

namespace Tests\Feature\Api;

use App\Models\Bookmark;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_their_bookmarks(): void
    {
        $user = User::factory()->create();
        Bookmark::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/user/bookmarks');

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_bookmark_a_vendor(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_user_cannot_bookmark_same_vendor_twice(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        Bookmark::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->postJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(422);
    }

    public function test_user_can_remove_a_bookmark(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        Bookmark::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->deleteJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
        ]);
    }

    public function test_guest_cannot_bookmark_a_vendor(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->postJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(401);
    }

    public function test_guest_cannot_remove_a_bookmark(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->deleteJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(401);
    }

    public function test_removing_nonexistent_bookmark_returns_404(): void
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/user/bookmarks/{$vendor->id}");

        $response->assertStatus(404);
    }
}
