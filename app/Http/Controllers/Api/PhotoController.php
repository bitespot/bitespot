<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class PhotoController extends StubController
{
    public function publicIndex(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('PhotoController::publicIndex');
    }

    public function index(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('PhotoController::index');
    }

    public function store(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('PhotoController::store');
    }

    public function destroy(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('PhotoController::destroy');
    }
}
