<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorDashboardController extends Controller
{
    private function authorize(Vendor $vendor): bool
    {
        return $vendor->user_id === auth()->id();
    }

    public function index(Request $request, Vendor $vendor)
    {
        if (!$this->authorize($vendor)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menuCount = $vendor->menuItems()->count();

        $recentReviews = $vendor->reviews()
            ->with(['user:id,name', 'vendorReply:review_id,body'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'id'         => $r->id,
                'user_name'  => $r->user?->name,
                'rating'     => $r->rating,
                'body'       => $r->body,
                'created_at' => $r->created_at,
                'reply'      => $r->vendorReply?->body,
            ]);

        return response()->json([
            'vendor_id'      => $vendor->id,
            'business_name'  => $vendor->business_name,
            'status'         => $vendor->status,
            'views'          => $vendor->view_count,
            'views_delta'    => 0,
            'rating'         => $vendor->avg_rating ?? 0,
            'rating_delta'   => 0,
            'menu_count'     => $menuCount,
            'menu_delta'     => 0,
            'recent_reviews' => $recentReviews,
        ]);
    }

    public function show(Request $request, Vendor $vendor)
    {
        if (!$this->authorize($vendor)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $vendor->load('category');

        return response()->json(array_merge($vendor->toArray(), [
            'category' => $vendor->category?->name,
        ]));
    }

    public function update(Request $request, Vendor $vendor)
    {
        if (!$this->authorize($vendor)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'phone'         => 'nullable|string|max:30',
            'website'       => 'nullable|url|max:255',
            'address'       => 'nullable|string|max:500',
            'city'          => 'nullable|string|max:100',
            'province'      => 'nullable|string|max:100',
            'hours'         => 'nullable|array',
            'price_tier'    => 'nullable|in:$,$$,$$$',
            'category'      => 'nullable|string|max:100',
        ]);

        if (!empty($validated['category'])) {
            $cat = Category::where('name', $validated['category'])->first();
            if ($cat) {
                $vendor->category_id = $cat->id;
            }
            unset($validated['category']);
        }

        $vendor->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'vendor'  => $vendor,
        ]);
    }
}
