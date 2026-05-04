<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the user's bookmarks.
     */
    public function index(Request $request): JsonResponse
    {
        $bookmarks = auth()->user()->bookmarks()
            ->with(['vendor.category'])
            ->latest()
            ->paginate(15);

        return response()->json($bookmarks);
    }

    /**
     * Store a newly created bookmark in storage.
     */
    public function store(Request $request, Vendor $vendor): JsonResponse
    {
        // Check if already bookmarked
        $exists = Bookmark::where('user_id', auth()->id())
            ->where('vendor_id', $vendor->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You have already bookmarked this BiteSpot.'
            ], 422);
        }

        $bookmark = Bookmark::create([
            'user_id' => auth()->id(),
            'vendor_id' => $vendor->id,
        ]);

        return response()->json($bookmark, 201);
    }

    /**
     * Remove the specified bookmark from storage.
     */
    public function destroy(Request $request, Vendor $vendor): JsonResponse
    {
        $bookmark = Bookmark::where('user_id', auth()->id())
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bookmark) {
            return response()->json(['message' => 'Bookmark not found.'], 404);
        }

        $bookmark->delete();

        return response()->json(['message' => 'Bookmark removed successfully.']);
    }
}
