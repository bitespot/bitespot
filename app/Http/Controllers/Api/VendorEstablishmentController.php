<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorEstablishmentController extends Controller
{
    /**
     * GET /api/vendor/establishments
     * List all establishments owned by the authenticated vendor user.
     */
    public function index(Request $request): JsonResponse
    {
        $establishments = Vendor::where('user_id', auth()->id())
            ->with('category')
            ->orderBy('created_at')
            ->get();

        return response()->json(['data' => $establishments]);
    }

    /**
     * POST /api/vendor/establishments
     * Create a new establishment for the authenticated vendor user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'phone'         => 'nullable|string|max:30',
            'address'       => 'required|string|max:500',
            'city'          => 'nullable|string|max:100',
            'province'      => 'nullable|string|max:100',
            'price_tier'    => 'nullable|in:$,$$,$$$',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug']    = Str::slug($validated['business_name']) . '-' . Str::random(6);
        $validated['status']  = 'approved';

        $vendor = Vendor::create($validated);
        $vendor->load('category');

        return response()->json($vendor, 201);
    }

    /**
     * DELETE /api/vendor/establishments/{vendor}
     * Permanently delete an establishment owned by the authenticated vendor.
     */
    public function destroy(Vendor $vendor): JsonResponse
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $vendor->delete();

        return response()->json(['message' => 'Establishment deleted.']);
    }
}
