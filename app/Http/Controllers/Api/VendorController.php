<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class VendorController extends StubController
{
    public function index(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('VendorController::index');
    }

    public function show(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('VendorController::show');
    }

    public function trending(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('VendorController::trending');
    }

    public function register(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('VendorController::register');
    }
}
