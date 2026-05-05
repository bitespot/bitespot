<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Vendor;

class VendorDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Return dashboard KPIs
        return response()->json([
            'vendor_id' => $vendor->id,
            'business_name' => $vendor->business_name,
            'status' => $vendor->status,
            'avg_rating' => $vendor->avg_rating,
            'review_count' => $vendor->review_count,
            'view_count' => $vendor->view_count ?? 0,
        ]);
    }

    public function show(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return response()->json($vendor);
    }

    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Validate and update fields
        $validated = $request->validate([
            'business_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'hours' => 'nullable|array',
            'price_tier' => 'nullable|in:$,$$,$$$',
        ]);

        $vendor->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'vendor' => $vendor,
        ], 200);
    }
}
