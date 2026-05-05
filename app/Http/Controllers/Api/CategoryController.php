<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends StubController
{
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get(['id', 'name', 'slug', 'icon']);

        return response()->json(['success' => true, 'data' => $categories]);
    }
}
