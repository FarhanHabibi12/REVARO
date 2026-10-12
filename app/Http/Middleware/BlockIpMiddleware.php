<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\IpBlacklist;

class BlockIpMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();

        try {
            if (IpBlacklist::where('ip_address', $ip)->exists()) {
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'IP Anda diblokir karena aktivitas mencurigakan.'
                    ], 403);
                }
                abort(403, 'Akses ditolak. IP Anda telah diblokir karena alasan keamanan.');
            }
        } catch (\Throwable $e) {
            // Fail safely if database is temporarily unreachable
        }

        return $next($request);
    }
}
