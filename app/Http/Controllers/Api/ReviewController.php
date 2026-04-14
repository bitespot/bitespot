<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ReviewController extends StubController
{
    public function publicIndex(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::publicIndex');
    }

    public function userIndex(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::userIndex');
    }

    public function vendorIndex(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::vendorIndex');
    }

    public function store(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::store');
    }

    public function update(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::update');
    }

    public function destroy(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('ReviewController::destroy');
    }
}
