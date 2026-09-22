<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotosController extends Controller
{
    private function storageDisk(): string
    {
        if (app()->runningUnitTests()) {
            return 's3';
        }
        $default = config('filesystems.default', 'public');
        return ($default === 's3' && config('filesystems.disks.s3.key')) ? 's3' : 'public';
    }

    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $vendor = Vendor::where('user_id', auth()->id())->firstOrFail();
        $disk = $this->storageDisk();

        if ($vendor->cover_photo) {
            Storage::disk($disk)->delete($vendor->cover_photo);
        }

        $file = $request->file('cover_photo');
        $key  = 'vendors/covers/' . $vendor->slug . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        Storage::disk($disk)->put($key, file_get_contents($file), 'public');
        $vendor->update(['cover_photo' => $key]);

        return redirect()->route('vendor.photos')->with('success', 'Cover photo updated successfully!');
    }

    public function uploadProfile(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $vendor = Vendor::where('user_id', auth()->id())->firstOrFail();
        $disk = $this->storageDisk();

        if ($vendor->profile_photo) {
            Storage::disk($disk)->delete($vendor->profile_photo);
        }

        $file = $request->file('profile_photo');
        $key  = 'vendors/profiles/' . $vendor->slug . '-' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        Storage::disk($disk)->put($key, file_get_contents($file), 'public');
        $vendor->update(['profile_photo' => $key]);

        return redirect()->route('vendor.photos')->with('success', 'Profile photo updated successfully!');
    }
}
