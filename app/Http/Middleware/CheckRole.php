<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        if (!$user || $user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Role required: ' . $role,
                    'user_role' => $user?->role,
                ], 403);
            }
            
            // Log the failed role check for debugging
            \Log::warning('Role check failed', [
                'expected_role' => $role,
                'user_id' => $user?->id,
                'user_role' => $user?->role,
                'path' => $request->path(),
            ]);
            
            return redirect('/');
        }

        return $next($request);
    }
}
