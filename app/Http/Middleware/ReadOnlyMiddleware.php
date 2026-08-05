<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReadOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if the user is a monitoring admin
        if ($user && $user->isSuperAdminMonitoring()) {
            $method = strtoupper($request->method());
            
            // Check if it's a write operation
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                // Exclude the monitoring flags store route
                if (!$request->routeIs('monitoring.flags.store')) {
                    return response()->json([
                        'message' => 'Akses ditolak. Peran Monitoring Kinerja hanya memiliki akses baca (read-only).'
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
