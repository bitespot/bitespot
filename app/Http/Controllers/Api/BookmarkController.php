<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class BookmarkController extends StubController
{
    public function index(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('BookmarkController::index');
    }

    public function store(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('BookmarkController::store');
    }

    public function destroy(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('BookmarkController::destroy');
    }
}
