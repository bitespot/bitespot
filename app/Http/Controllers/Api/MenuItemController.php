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
     * GET /api/vendors/{vendor}/menu  — public menu for establishment page
     */
    public function publicIndex(Request $request, Vendor $vendor): JsonResponse
    {
        $menuItems = $vendor->menuItems()
            ->where('status', 'Active')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return response()->json($menuItems);
    }

    /**
     * GET /api/vendor/establishments/{vendor}/menu
     */
    public function index(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($vendor->menuItems()->orderBy('sort_order')->get());
    }

    /**
     * POST /api/vendor/establishments/{vendor}/menu
     */
    public function store(Request $request, Vendor $vendor): JsonResponse
    {
        if ($vendor->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'status'      => 'nullable|in:Active,Sold Out,Hidden',
            'sort_order'  => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'price', 'category', 'status', 'sort_order']);
        $data['is_available'] = ($data['status'] ?? 'Active') === 'Active';

        $menuItem = $vendor->menuItems()->create($data);

        return response()->json($menuItem, 201);
    }

    /**
     * PUT /api/vendor/establishments/{vendor}/menu/{item}
     */
    public function update(Request $request, Vendor $vendor, MenuItem $item): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $item->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'status'      => 'nullable|in:Active,Sold Out,Hidden',
            'sort_order'  => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'description', 'price', 'category', 'status', 'sort_order']);
        if (isset($data['status'])) {
            $data['is_available'] = $data['status'] === 'Active';
        }

        $item->update($data);

        return response()->json($item);
    }

    /**
     * DELETE /api/vendor/establishments/{vendor}/menu/{item}
     */
    public function destroy(Request $request, Vendor $vendor, MenuItem $item): JsonResponse
    {
        if ($vendor->user_id !== auth()->id() || $item->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $item->delete();

        return response()->json(['message' => 'Menu item deleted successfully.']);
    }
}
