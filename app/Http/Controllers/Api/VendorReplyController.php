<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Vendor;
use App\Models\VendorReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorReplyController extends Controller
{
    /**
     * POST /api/vendor/establishments/{vendor}/reviews/{review}/reply
     */
    public function store(Request $request, Vendor $vendor, Review $review): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $review->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($review->vendorReply) {
            return response()->json(['message' => 'Reply already exists. Use PUT to update.'], 422);
        }

        $validated = $request->validate(['reply' => 'required|string|max:1000']);

        $reply = VendorReply::create([
            'review_id' => $review->id,
            'vendor_id' => $vendor->id,
            'body'      => $validated['reply'],
        ]);

        return response()->json(['message' => 'Reply posted', 'reply' => $reply], 201);
    }

    /**
     * PUT /api/vendor/establishments/{vendor}/reviews/{review}/reply
     */
    public function update(Request $request, Vendor $vendor, Review $review): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $review->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate(['reply' => 'required|string|max:1000']);

        $reply = $review->vendorReply;

        if ($reply) {
            $reply->update(['body' => $validated['reply']]);
        } else {
            $reply = VendorReply::create([
                'review_id' => $review->id,
                'vendor_id' => $vendor->id,
                'body'      => $validated['reply'],
            ]);
        }

        return response()->json(['message' => 'Reply updated', 'reply' => $reply]);
    }
}
