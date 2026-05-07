<?php

namespace App\Http\Controllers;

use App\Models\BiteSpot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BiteSpotController extends Controller
{
    public function create()
    {
        return view('bitespot.create');
    }

    public function store(Request $request)
    {
        // 1. Validate the incoming data
        $validated = $request->validate([
            'spot_name' => 'required|string|max:255',
            'vendor_id' => 'nullable|exists:vendors,id',
            'general_photo' => 'nullable|image|max:5120', 
            'spot_rating' => 'required|integer|min:1|max:5',
            'spot_review' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // 2. Handle the Image Upload
        $photoPath = null;
        if ($request->hasFile('general_photo')) {
            // Stores the file in storage/app/public/bitespots
            $photoPath = $request->file('general_photo')->store('bitespots', 'public');
        }

        // 3. Save to the Database
        BiteSpot::create([
            'user_id' => auth()->id(),
            'vendor_id' => $validated['vendor_id'] ?? null,
            'spot_name' => $validated['spot_name'],
            'general_photo' => $photoPath,
            'spot_rating' => $validated['spot_rating'],
            'spot_review' => $validated['spot_review'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
        ]);

        // 4. Redirect back to the dashboard newsfeed
        return redirect()->route('dashboard')->with('status', 'BiteSpot posted successfully!');
    }

    public function toggleLike(BiteSpot $bitespot)
    {
        $bitespot->likes()->toggle(auth()->id());

        return response()->json([
            'status' => 'success',
            'likes_count' => $bitespot->likes()->count()
        ]);
    }

    public function toggleSave(BiteSpot $bitespot)
    {
        $bitespot->saves()->toggle(auth()->id());

        return response()->json([
            'status' => 'success'
        ]);
    }
}