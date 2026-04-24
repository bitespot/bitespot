<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class MenuItemController extends StubController
{
    public function publicIndex(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('MenuItemController::publicIndex');
    }

    public function index(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('MenuItemController::index');
    }

    public function store(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('MenuItemController::store');
    }

    public function update(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('MenuItemController::update');
    }

    public function destroy(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('MenuItemController::destroy');
    }
}
