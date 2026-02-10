<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * If the authenticated user has no roles, block access and
     * show a clear message asking them to contact the administrator.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->roles()->count() === 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your account has not been assigned any role yet. Please contact the system administrator for role assignment.',
                ], 403);
            }

            return response()->view('errors.no-role', [], 403);
        }

        return $next($request);
    }
}

