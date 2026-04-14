<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class AuthController extends StubController
{
    public function me(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AuthController::me');
    }

    public function logout(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AuthController::logout');
    }
}
