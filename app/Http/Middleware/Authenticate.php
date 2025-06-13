<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API routes, return JSON response instead of redirect
        if ($request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'errors' => ['auth' => ['Please login to access this resource']]
            ], 401));
        }

        // For web routes, you can keep the default behavior
        return route('login');
    }
}
