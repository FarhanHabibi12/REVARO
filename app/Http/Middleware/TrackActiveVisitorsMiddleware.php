<?php

namespace App\Http\Middleware;

use App\Models\ActiveVisitor;
use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackActiveVisitorsMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip background static asset requests
        $path = $request->path();
        if (str_starts_with($path, 'css') || str_starts_with($path, 'js') || str_starts_with($path, 'images') || str_starts_with($path, 'favicon')) {
            return $response;
        }

        try {
            $user = $request->user('sanctum') ?: auth()->user();

            // Fallback token extraction from Bearer header if sanctum user null
            if (!$user && $request->hasHeader('Authorization')) {
                $tokenStr = str_replace('Bearer ', '', $request->header('Authorization'));
                $pat = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenStr);
                if ($pat) {
                    $user = $pat->tokenable;
                }
            }

            $ip = $request->ip() ?: '127.0.0.1';
            $userAgent = substr($request->userAgent() ?? 'Unknown Browser', 0, 255);
            $endpoint = '/' . ltrim($path, '/');

            $nama = 'Pengunjung Umum (Guest)';
            $roleName = 'GUEST VISITOR';
            $userId = null;

            if ($user) {
                $userId = $user->id;
                $nama = $user->nama ?? 'Pengguna Registered';

                // Remove guest entry for this IP if user is authenticated
                ActiveVisitor::where('ip_address', $ip)->whereNull('user_id')->delete();

                $userRoleName = '';
                if ($user->role_id) {
                    $r = Role::find($user->role_id);
                    $userRoleName = $r?->nama ?? '';
                }

                $roleSlug = array_search($userRoleName, Role::$slugMap) ?: $userRoleName;

                switch ($roleSlug) {
                    case 'super-admin-pemilik':
                    case 'Super Admin (Pemilik Sistem)':
                        $roleName = 'SUPER ADMIN PLATFORM';
                        break;
                    case 'admin-verifikator':
                    case 'Admin Verifikator':
                        $roleName = 'ADMIN VERIFIKATOR';
                        break;
                    case 'admin-resolusi':
                    case 'Admin Resolusi':
                        $roleName = 'ADMIN RESOLUSI';
                        break;
                    case 'toko':
                    case 'Toko Sparepart':
                        $roleName = 'MITRA TOKO SPAREPART';
                        break;
                    case 'teknisi':
                    case 'Teknisi':
                        $roleName = 'MITRA TEKNISI';
                        break;
                    case 'pelanggan':
                    case 'Pelanggan':
                        $roleName = 'PELANGGAN';
                        break;
                    default:
                        $roleName = strtoupper($userRoleName ?: 'REGISTERED USER');
                }
            }

            ActiveVisitor::updateOrCreate(
                ['ip_address' => $ip, 'user_id' => $userId],
                [
                    'nama' => $nama,
                    'role' => $roleName,
                    'endpoint' => $endpoint,
                    'user_agent' => $userAgent,
                    'last_seen_at' => now(),
                ]
            );

            // Housekeeping: delete visitors older than 60 minutes
            if (rand(1, 100) <= 5) {
                ActiveVisitor::where('last_seen_at', '<', now()->subMinutes(60))->delete();
            }
        } catch (\Throwable $e) {
            // Silently swallow tracking errors to not disrupt response
        }

        return $response;
    }
}
