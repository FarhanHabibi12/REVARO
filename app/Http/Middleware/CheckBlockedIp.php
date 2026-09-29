<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckBlockedIp
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if ($ip) {
            $blockedIps = Cache::remember('blocked_ips_list', 60, function () {
                return BlockedIp::pluck('ip_address')->toArray();
            });

            if (in_array($ip, $blockedIps)) {
                return response()->json([
                    'message' => 'Akses IP Anda ditolak oleh administrator sistem.',
                    'ip_address' => $ip,
                ], 403);
            }
        }

        return $next($request);
    }
}
