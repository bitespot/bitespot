<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    /**
     * GET /api/vendors/{vendor}/photos  — public gallery for establishment page
     */
    public function publicIndex(Request $request, Vendor $vendor)
    {
        if ($vendor->status !== 'approved') {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $photos = $vendor->photos()->orderByDesc('is_primary')->orderBy('created_at')->get();

        return response()->json(['data' => $photos]);
    }

    /**
     * GET /api/vendor/establishments/{vendor}/photos
     */
    public function index(Request $request, Vendor $vendor)
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $photos = $vendor->photos()->orderByDesc('is_primary')->orderBy('created_at')->get();

        return response()->json(['data' => $photos]);
    }

    /**
     * POST /api/vendor/establishments/{vendor}/photos  — upload a gallery photo
     */
    public function store(Request $request, Vendor $vendor)
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('photo');
        $key  = 'vendors/' . $vendor->id . '/gallery/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('s3')->put($key, file_get_contents($file));

        $isPrimary = $vendor->photos()->count() === 0;

        $photo = $vendor->photos()->create([
            'url'        => $key,
            'is_primary' => $isPrimary,
        ]);

        if ($isPrimary) {
            $vendor->update(['profile_photo' => $key]);
        }

        return response()->json($photo, 201);
    }

    /**
     * PUT /api/vendor/establishments/{vendor}/photos/{photo}/primary
     */
    public function setPrimary(Request $request, Vendor $vendor, Photo $photo)
    {
        if ($vendor->user_id !== auth()->id() || $photo->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $vendor->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);
        $vendor->update(['profile_photo' => $photo->url]);

        return response()->json(['message' => 'Primary photo updated', 'photo' => $photo]);
    }

    /**
     * DELETE /api/vendor/establishments/{vendor}/photos/{photo}
     */
    public function destroy(Request $request, Vendor $vendor, Photo $photo)
    {
        if ($vendor->user_id !== auth()->id() || $photo->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::disk('s3')->delete($photo->url);

        if ($photo->is_primary) {
            $next = $vendor->photos()->where('id', '!=', $photo->id)->latest()->first();
            $vendor->update(['profile_photo' => $next?->url]);
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted']);
    }

    /**
     * POST /api/vendor/establishments/{vendor}/photos/cover
     */
    public function uploadCover(Request $request, Vendor $vendor)
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('cover_photo');

        if ($vendor->cover_photo) {
            Storage::disk('s3')->delete($vendor->cover_photo);
        }

        $key = 'vendors/covers/' . Str::slug($vendor->business_name) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('s3')->put($key, file_get_contents($file));

        $vendor->update(['cover_photo' => $key]);

        return response()->json([
            'message'         => 'Cover photo updated',
            'cover_photo_url' => Storage::disk('s3')->url($key),
        ]);
    }

    /**
     * POST /api/vendor/establishments/{vendor}/photos/profile
     */
    public function uploadProfile(Request $request, Vendor $vendor)
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $file = $request->file('profile_photo');

        if ($vendor->profile_photo) {
            Storage::disk('s3')->delete($vendor->profile_photo);
        }

        $key = 'vendors/profiles/' . Str::slug($vendor->business_name) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::disk('s3')->put($key, file_get_contents($file));

        $vendor->update(['profile_photo' => $key]);

        return response()->json([
            'message'            => 'Profile photo updated',
            'profile_photo_url'  => Storage::disk('s3')->url($key),
        ]);
    }
}
