<?php

namespace Tests\Feature\Api;

use App\Models\MenuItem;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_menu_items_for_a_vendor_publicly(): void
    {
        $vendor = Vendor::factory()->create();
        MenuItem::factory()->count(5)->create(['vendor_id' => $vendor->id]);

        $response = $this->getJson("/api/vendors/{$vendor->id}/menu");

        $response->assertStatus(200);
    }

    public function test_vendor_can_list_their_own_menu_items(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        MenuItem::factory()->count(3)->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->getJson('/api/vendor/menu');

        $response->assertStatus(200);
    }

    public function test_vendor_can_create_menu_item(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/api/vendor/menu', [
            'name' => 'Signature Burger',
            'price' => 250.00,
            'category' => 'Mains',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', [
            'vendor_id' => $vendor->id,
            'name' => 'Signature Burger',
        ]);
    }

    public function test_vendor_can_update_their_own_menu_item(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $item = MenuItem::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->putJson("/api/vendor/menu/{$item->id}", [
            'name' => 'Updated Burger',
            'price' => 275.00,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menu_items', [
            'id' => $item->id,
            'name' => 'Updated Burger',
        ]);
    }

    public function test_vendor_cannot_update_others_menu_item(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $otherVendor = Vendor::factory()->create();
        $item = MenuItem::factory()->create(['vendor_id' => $otherVendor->id]);

        $response = $this->actingAs($user)->putJson("/api/vendor/menu/{$item->id}", [
            'name' => 'Hacker Burger',
        ]);

        $response->assertStatus(403);
    }

    public function test_vendor_can_delete_their_own_menu_item(): void
    {
        $user = User::factory()->create(['role' => 'vendor']);
        $vendor = Vendor::factory()->create(['user_id' => $user->id]);
        $item = MenuItem::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->deleteJson("/api/vendor/menu/{$item->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }
}
