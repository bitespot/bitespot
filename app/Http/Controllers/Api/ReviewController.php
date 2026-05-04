<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    public function publicIndex(Request $request, Vendor $vendor): JsonResponse
    {
        $reviews = $vendor->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function userIndex(Request $request): JsonResponse
    {
        $reviews = auth()->user()->reviews()
            ->with('vendor:id,business_name,slug')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function vendorIndex(Request $request): JsonResponse
    {
        // Assuming the vendor owner is the one logged in
        $vendor = Vendor::where('user_id', auth()->id())->first();
        
        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $reviews = $vendor->reviews()
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user already reviewed this vendor
        $exists = Review::where('user_id', auth()->id())
            ->where('vendor_id', $request->vendor_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You have already reviewed this BiteSpot.'
            ], 422);
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'vendor_id' => $request->vendor_id,
            'rating' => $request->rating,
            'body' => $request->body,
        ]);

        $this->updateVendorStats($request->vendor_id);

        return response()->json($review, 201);
    }

    public function update(Request $request, Review $review): JsonResponse
    {
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'body' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $review->update($request->only(['rating', 'body']));

        $this->updateVendorStats($review->vendor_id);

        return response()->json($review);
    }

    public function destroy(Review $review): JsonResponse
    {
        if ($review->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $vendorId = $review->vendor_id;
        $review->delete();

        $this->updateVendorStats($vendorId);

        return response()->json(['message' => 'Review deleted successfully.']);
    }

    protected function updateVendorStats(int $vendorId): void
    {
        $vendor = Vendor::find($vendorId);
        if ($vendor) {
            $stats = Review::where('vendor_id', $vendorId)
                ->selectRaw('COUNT(*) as count, AVG(rating) as avg')
                ->first();

            $vendor->update([
                'review_count' => $stats->count,
                'avg_rating' => round($stats->avg, 1),
            ]);
        }
    }
}
