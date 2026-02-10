<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChangeForRoles
{
    /**
     * Handle an incoming request.
     *
     * For users with Dean or Coordinator roles, if they have never changed
     * their password (password_changed_at is null), force them to the
     * change-password screen until they update it.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Only enforce for Dean and Coordinator roles
        if (!$user->hasAnyRole(['Dean', 'Coordinator'])) {
            return $next($request);
        }

        // If password has already been changed, nothing to do
        if (!is_null($user->password_changed_at)) {
            return $next($request);
        }

        // Don't redirect inside the password/profile routes or logout to avoid loops
        if ($request->routeIs('profile.password.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        // Redirect to password change screen with a flag to show a message
        return redirect()
            ->route('profile.password.edit')
            ->with('force_password_change', true);
    }
}

