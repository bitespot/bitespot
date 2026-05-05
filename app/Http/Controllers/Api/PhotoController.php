<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Vendor;

class PhotoController extends Controller
{
    /**
     * Upload cover photo to S3
     * POST /api/vendor/photos/cover
     */
    public function uploadCover(Request $request)
    {
        // Ensure authenticated vendor
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Validate file
        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            
            // Delete old photo if exists
            if ($vendor->cover_photo) {
                Storage::disk('s3')->delete($vendor->cover_photo);
            }

            // Generate unique filename
            $fileName = 'vendors/covers/' . Str::slug($vendor->business_name) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Upload to S3 with public visibility
            Storage::disk('s3')->put($fileName, file_get_contents($file), 'public');

            // Update database
            $vendor->cover_photo = $fileName;
            $vendor->save();

            return response()->json([
                'message' => 'Cover photo uploaded successfully',
                'cover_photo' => $fileName,
                'cover_photo_url' => Storage::disk('s3')->url($fileName),
            ], 200);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Upload profile photo to S3
     * POST /api/vendor/photos/profile
     */
    public function uploadProfile(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            
            if ($vendor->profile_photo) {
                Storage::disk('s3')->delete($vendor->profile_photo);
            }

            $fileName = 'vendors/profiles/' . Str::slug($vendor->business_name) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put($fileName, file_get_contents($file), 'public');

            $vendor->profile_photo = $fileName;
            $vendor->save();

            return response()->json([
                'message' => 'Profile photo uploaded successfully',
                'profile_photo' => $fileName,
                'profile_photo_url' => Storage::disk('s3')->url($fileName),
            ], 200);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }

    /**
     * Get vendor photos (public)
     * GET /api/vendors/{vendor}/photos
     */
    public function publicIndex(Request $request, Vendor $vendor)
    {
        if ($vendor->status !== 'approved') {
            return response()->json(['message' => 'Vendor not found or not approved'], 404);
        }

        $photos = [];
        if ($vendor->cover_photo) {
            $photos[] = [
                'type' => 'cover',
                'path' => $vendor->cover_photo,
                'url' => Storage::disk('s3')->url($vendor->cover_photo),
            ];
        }
        if ($vendor->profile_photo) {
            $photos[] = [
                'type' => 'profile',
                'path' => $vendor->profile_photo,
                'url' => Storage::disk('s3')->url($vendor->profile_photo),
            ];
        }

        return response()->json(['data' => $photos]);
    }

    /**
     * Get vendor's own photos (authenticated)
     * GET /api/vendor/photos
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vendor = Vendor::where('user_id', auth()->id())->first();
        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        return $this->publicIndex($request, $vendor);
    }

    public function store(Request $request)
    {
        // Deprecated - use uploadCover or uploadProfile
        return response()->json(['message' => 'Use uploadCover or uploadProfile endpoints'], 400);
    }

    public function destroy(Request $request)
    {
        return response()->json(['message' => 'Not implemented'], 400);
    }
}
