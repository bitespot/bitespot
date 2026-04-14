<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * StubController
 *
 * All Api controllers extend this until real implementations replace them.
 * Returns a 200 JSON stub so frontend fetch() calls don't hard-fail.
 *
 * USAGE — in your concrete controller, replace stub() with real logic:
 *
 *   public function index(Request $request): JsonResponse
 *   {
 *       // real implementation here
 *   }
 */
abstract class StubController extends Controller
{
    protected function stub(string $message = 'Stub — not yet implemented', array $data = []): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'stub'    => true,
            'message' => $message,
            'data'    => $data,
        ], 200);
    }
}
