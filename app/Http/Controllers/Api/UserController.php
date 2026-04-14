<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class UserController extends StubController
{
    public function show(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('UserController::show');
    }

    public function update(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('UserController::update');
    }
}
