<?php

namespace Tests\Feature;

use App\Models\Bookmark;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_approved_vendor_page_loads(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'approved']);

        $response = $this->get("/place/{$vendor->slug}");

        $response->assertStatus(200);
        $response->assertSee($vendor->business_name);
    }

    public function test_pending_vendor_page_returns_404(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'pending']);

        $response = $this->get("/place/{$vendor->slug}");

        $response->assertStatus(404);
    }

    public function test_rejected_vendor_page_returns_404(): void
    {
        $vendor = Vendor::factory()->create(['status' => 'rejected']);

        $response = $this->get("/place/{$vendor->slug}");

        $response->assertStatus(404);
    }

    public function test_guest_sees_page_with_bookmark_status_false(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get("/place/{$vendor->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('isBookmarked', false);
    }

    public function test_authenticated_user_without_bookmark_sees_status_false(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();

        $response = $this->actingAs($user)->get("/place/{$vendor->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('isBookmarked', false);
    }

    public function test_authenticated_user_with_bookmark_sees_status_true(): void
    {
        $user   = User::factory()->create();
        $vendor = Vendor::factory()->create();
        Bookmark::factory()->create(['user_id' => $user->id, 'vendor_id' => $vendor->id]);

        $response = $this->actingAs($user)->get("/place/{$vendor->slug}");

        $response->assertStatus(200);
        $response->assertViewHas('isBookmarked', true);
    }

    public function test_page_exposes_vendor_id_to_javascript(): void
    {
        $vendor = Vendor::factory()->create();

        $response = $this->get("/place/{$vendor->slug}");

        $response->assertStatus(200);
        $response->assertSee("window.VENDOR_ID     = {$vendor->id}", false);
    }
}
