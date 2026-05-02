<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccountStatus
{
    /**
     * Handle an incoming request.
     * Feature 4: Account Status Control - Block suspended users
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated and their account is marked as suspended
        if (auth('sanctum')->check() && auth('sanctum')->user()->is_suspended) {
            
            // Instantly revoke all active access tokens for this user
            auth('sanctum')->user()->tokens()->delete();
            
            // Return a 403 Forbidden response
            return response()->json([
                'error' => 'Your account has been suspended. Please contact the Administrator.'
            ], 403);
        }

        // If not suspended, allow the request to proceed
        return $next($request);
    }
}