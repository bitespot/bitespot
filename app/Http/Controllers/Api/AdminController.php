<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class AdminController extends StubController
{
    public function pendingVendors(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::pendingVendors');
    }

    public function approveVendor(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::approveVendor');
    }

    public function rejectVendor(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::rejectVendor');
    }

    public function removeReview(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::removeReview');
    }

    public function banUser(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::banUser');
    }

    public function suspendVendor(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::suspendVendor');
    }

    public function analytics(\Illuminate\Http\Request $request, $param = null): \Illuminate\Http\JsonResponse
    {
        return $this->stub('AdminController::analytics');
    }
}
