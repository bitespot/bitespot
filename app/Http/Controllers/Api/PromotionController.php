<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * GET /api/vendors/{vendor}/promotions  — public active promos
     */
    public function publicIndex(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->status !== 'approved') {
            return response()->json(['data' => []]);
        }

        $promos = $vendor->promotions()
            ->where('is_active', true)
            ->where('valid_until', '>=', now())
            ->orderBy('valid_until')
            ->get();

        return response()->json(['data' => $promos]);
    }

    /**
     * GET /api/vendor/establishments/{vendor}/promotions
     */
    public function index(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(['data' => $vendor->promotions()->orderByDesc('valid_until')->get()]);
    }

    /**
     * POST /api/vendor/establishments/{vendor}/promotions
     */
    public function store(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount'    => 'required|numeric|min:0|max:100',
            'valid_until' => 'required|date|after:today',
        ]);

        return response()->json($vendor->promotions()->create($validated), 201);
    }

    /**
     * PUT /api/vendor/establishments/{vendor}/promotions/{promotion}
     */
    public function update(Request $request, Vendor $vendor, Promotion $promotion): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $promotion->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'discount'    => 'sometimes|required|numeric|min:0|max:100',
            'valid_until' => 'sometimes|required|date',
            'is_active'   => 'boolean',
        ]);

        $promotion->update($validated);

        return response()->json($promotion);
    }

    /**
     * DELETE /api/vendor/establishments/{vendor}/promotions/{promotion}
     */
    public function destroy(Request $request, Vendor $vendor, Promotion $promotion): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $promotion->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $promotion->delete();

        return response()->json(['message' => 'Promotion deleted']);
    }
}
