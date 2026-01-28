<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockStudents
{
    /**
     * Handle an incoming request.
     * Faculty users have access to admin routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Faculty users have access to admin routes, so no blocking needed
        return $next($request);
    }
}
