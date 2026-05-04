<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the menu items for a specific vendor (Public).
     */
    public function publicIndex(Request $request, Vendor $vendor): JsonResponse
    {
        $menuItems = $vendor->menuItems()
            ->where('is_available', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json($menuItems);
    }

    /**
     * Display a listing of the menu items for the authenticated vendor owner.
     */
    public function index(Request $request): JsonResponse
    {
        $vendor = Vendor::where('user_id', auth()->id())->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $menuItems = $vendor->menuItems()
            ->orderBy('sort_order')
            ->get();

        return response()->json($menuItems);
    }

    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $vendor = Vendor::where('user_id', auth()->id())->first();

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $menuItem = $vendor->menuItems()->create($request->all());

        return response()->json($menuItem, 201);
    }

    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, MenuItem $menuItem): JsonResponse
    {
        $vendor = Vendor::where('user_id', auth()->id())->first();

        if (!$vendor || $menuItem->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category' => 'nullable|string|max:100',
            'is_available' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $menuItem->update($request->all());

        return response()->json($menuItem);
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(Request $request, MenuItem $menuItem): JsonResponse
    {
        $vendor = Vendor::where('user_id', auth()->id())->first();

        if (!$vendor || $menuItem->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $menuItem->delete();

        return response()->json(['message' => 'Menu item deleted successfully.']);
    }
}
