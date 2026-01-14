<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockStudents
{
    /**
     * Handle an incoming request.
     * Block Students from accessing admin routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Student')) {
            return redirect()->route('welcome')
                ->with('error', 'Students do not have access to the admin dashboard. You can view and submit publications from the home page.');
        }

        return $next($request);
    }
}
