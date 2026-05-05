<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query()
            ->with(['category'])
            ->where('status', 'approved');

        // Search by query (business name, description)
        if ($request->has('q') && $request->q) {
            $q = $request->q;
            $query->where(function($qBuilder) use ($q) {
                $qBuilder->where('business_name', 'like', "%{$q}%")
                         ->orWhere('description', 'like', "%{$q}%");
            });
        }

        // Filter by category slug or ID
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category)
                  ->orWhere('id', $request->category);
            });
        }

        // Filter by city
        if ($request->has('city') && $request->city) {
            $query->where('city', 'like', "%{$request->city}%");
        }
        
        // Filter by price level (e.g., "$", "$$")
        if ($request->has('price') && $request->price) {
            $query->where('price_tier', $request->price);
        }

        // Filter by minimum rating
        if ($request->filled('rating')) {
            $query->where('avg_rating', '>=', (float) $request->rating);
        }

        $vendors = $query->paginate(20);

        return response()->json($vendors);
    }

    public function show(Vendor $vendor)
    {
        $vendor->load(['category', 'user']);
        
        // Return 404 if not approved, unless it's the owner
        if ($vendor->status !== 'approved' && (!auth()->check() || auth()->id() !== $vendor->user_id)) {
            return response()->json(['message' => 'Vendor not found or not approved'], 404);
        }

        return response()->json($vendor);
    }

    public function trending(Request $request)
    {
        // Simple trending logic: featured first, then highly rated (we'll just use created_at/featured for now)
        $vendors = Vendor::query()
            ->with('category')
            ->where('status', 'approved')
            ->orderBy('isFeatured', 'desc')
            ->latest()
            ->take(10)
            ->get();

        return response()->json($vendors);
    }

    public function register(Request $request)
    {
        // Require user auth
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $vendor = new Vendor($request->only([
            'business_name', 'description', 'category_id', 'address', 'city', 'phone'
        ]));

        $vendor->user_id = auth()->id();
        $vendor->slug = Str::slug($request->business_name) . '-' . uniqid();
        $vendor->status = 'pending'; // Default status
        $vendor->save();

        return response()->json([
            'message' => 'Vendor registration submitted successfully.',
            'vendor' => $vendor
        ], 201);
    }
}
