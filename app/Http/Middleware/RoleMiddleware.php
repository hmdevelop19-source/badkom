<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! $request->user() || ! in_array($request->user()->level, $roles)) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Akses ditolak. Anda tidak memiliki izin untuk rute ini.'
            ], 403);
        }

        return $next($request);
    }
}
