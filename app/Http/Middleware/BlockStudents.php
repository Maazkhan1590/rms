<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockStudents
{
    /**
     * Handle an incoming request.
     * Student role is treated as Faculty role - they have access to admin routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Student role is treated as Faculty role, so no blocking needed
        return $next($request);
    }
}
