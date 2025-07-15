<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPrivilege
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user() == null) {
            return response()->json(['error' => 'Unauthorized'], 401);
        } else if (!auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
