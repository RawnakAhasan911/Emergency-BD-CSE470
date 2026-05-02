<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Feature 1 & 2: Role-Based Access Control and Protected Admin Dashboard
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  (e.g., 'admin', 'citizen')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // First, ensure the user is actually logged in via the API (Sanctum)
        if (!auth('sanctum')->check()) {
            return response()->json([
                'error' => 'Unauthenticated. Please log in first.'
            ], 401);
        }

        // Second, verify if the logged-in user's role matches the required role
        if (auth('sanctum')->user()->role !== $role) {
            return response()->json([
                'error' => 'Unauthorized access. You do not have the required system permissions.'
            ], 403);
        }

        // If they pass both checks, allow the request to proceed to the controller
        return $next($request);
    }
}