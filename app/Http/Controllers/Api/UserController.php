<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'avatar'     => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'location'   => $user->location,
            'role'       => $user->role,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'location' => 'sometimes|nullable|string|max:255',
            'avatar'   => 'sometimes|nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $disk = (config('filesystems.default', 'public') === 's3' && config('filesystems.disks.s3.key')) ? 's3' : 'public';

            if ($user->avatar) {
                if (!str_starts_with($user->avatar, 'http')) {
                    Storage::disk($disk)->delete($user->avatar);
                } else if (str_contains($user->avatar, '/storage/')) {
                    // Fallback for previously uploaded local avatars
                    $oldPath = parse_url($user->avatar, PHP_URL_PATH);
                    $localPath = ltrim(str_replace('/storage/', '', $oldPath), '/');
                    Storage::disk('public')->delete($localPath);
                }
            }
            
            $key = 'users/avatars/' . Str::slug($user->name) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            Storage::disk($disk)->put($key, file_get_contents($file), 'public');
            
            $data['avatar'] = $key;
        }

        $user->fill($data)->save();

        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'avatar'     => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'location'   => $user->location,
        ]);
    }
}
