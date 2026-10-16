<?php

namespace App\Http\Middleware;

use App\Models\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class WebRoleMiddleware
{
    /**
     * Handle an incoming request for web portal routes with server-side authentication & role enforcement.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$slugs
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$slugs): Response
    {
        $user = null;
        try {
            // 1. Resolve user from Web Auth Guard
            $user = Auth::guard('web')->user();

            // 2. If not found in session, try resolving via cookie, Bearer header, or query token
            if (!$user) {
                $tokenString = $request->cookie('auth_token')
                    ?? $request->bearerToken()
                    ?? $request->query('_token_auth')
                    ?? session('admin_bearer_token')
                    ?? session('auth_token');

                if ($tokenString) {
                    // Strip Bearer prefix if accidentally included in cookie
                    if (str_starts_with($tokenString, 'Bearer ')) {
                        $tokenString = substr($tokenString, 7);
                    }

                    $accessToken = PersonalAccessToken::findToken($tokenString);
                    if ($accessToken && $accessToken->tokenable) {
                        $user = $accessToken->tokenable;
                        Auth::guard('web')->login($user);
                    }
                }
            }
        } catch (\Throwable $e) {
            $user = null;
        }

        // 3. Determine if this route is an Admin Portal or User Portal
        $path = $request->path();
        $isAdminRoute = str_starts_with($path, 'superadmin') || str_starts_with($path, 'admin');

        // 4. If user is still not authenticated, redirect to appropriate login page immediately
        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            if ($isAdminRoute) {
                return redirect('/login-admin?session_required=1')->with('error', 'Silakan masuk dengan akun administrator Anda terlebih dahulu.');
            }

            return redirect('/?auth=login&session_required=1')->with('error', 'Silakan masuk ke akun Anda terlebih dahulu.');
        }

        // 5. Ensure user account is active
        if (!$user->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($isAdminRoute) {
                return redirect('/login-admin')->with('error', 'Akun Anda dinonaktifkan oleh pemilik sistem.');
            }
            return redirect('/?auth=login')->with('error', 'Akun Anda dinonaktifkan.');
        }

        // 6. If no specific roles required, allow through
        if (empty($slugs)) {
            return $next($request);
        }

        // 7. Check if user satisfies ANY of the required role slugs
        $userRoleName = $user->role ? $user->role->nama : null;
        $isAuthorized = false;

        foreach ($slugs as $slug) {
            $mappedRoleName = Role::$slugMap[$slug] ?? null;

            // Direct role match
            if ($mappedRoleName && $userRoleName === $mappedRoleName) {
                $isAuthorized = true;
                break;
            }

            // Role helpers & profile checks
            if ($slug === 'super-admin-pemilik' && method_exists($user, 'isSuperAdminPemilik') && $user->isSuperAdminPemilik()) {
                $isAuthorized = true;
                break;
            }

            if ($slug === 'super-admin-monitoring' && method_exists($user, 'isSuperAdminMonitoring') && $user->isSuperAdminMonitoring()) {
                $isAuthorized = true;
                break;
            }

            if ($slug === 'admin-verifikator' && method_exists($user, 'isAdminVerifikator') && $user->isAdminVerifikator()) {
                $isAuthorized = true;
                break;
            }

            if ($slug === 'admin-resolusi' && method_exists($user, 'isAdminResolusi') && $user->isAdminResolusi()) {
                $isAuthorized = true;
                break;
            }

            // Multi-role customer / partner checks
            if ($slug === 'pelanggan') {
                if ($userRoleName === 'Pelanggan' || ($user->pelanggan !== null) || method_exists($user, 'isPelanggan') && $user->isPelanggan()) {
                    $isAuthorized = true;
                    break;
                }
            }

            if ($slug === 'teknisi') {
                if ($userRoleName === 'Teknisi' || ($user->teknisi !== null) || method_exists($user, 'isTeknisi') && $user->isTeknisi()) {
                    $isAuthorized = true;
                    break;
                }
            }

            if ($slug === 'toko-sparepart') {
                if ($userRoleName === 'Toko Sparepart' || ($user->toko !== null) || method_exists($user, 'isToko') && $user->isToko()) {
                    $isAuthorized = true;
                    break;
                }
            }

            // Super Admin Pemilik is supreme controller and can view all admin portals
            if ($user->isSuperAdminPemilik() && $isAdminRoute) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Forbidden: Akses ditolak untuk peran akun Anda.'], 403);
            }

            // Hitung peran aktif yang dimiliki pengguna saat ini
            $userRoleLabel = 'Pelanggan';
            $userDashboardUrl = '/pelanggan/dashboard';
            if (method_exists($user, 'isSuperAdminPemilik') && $user->isSuperAdminPemilik()) {
                $userRoleLabel = 'Super Admin Pemilik';
                $userDashboardUrl = '/superadmin/pemilik/dashboard';
            } elseif ((method_exists($user, 'isTeknisi') && $user->isTeknisi()) || $user->teknisi !== null) {
                $userRoleLabel = 'Mitra Teknisi';
                $userDashboardUrl = '/teknisi/dashboard';
            } elseif ((method_exists($user, 'isToko') && $user->isToko()) || $user->toko !== null) {
                $userRoleLabel = 'Mitra Toko Sparepart';
                $userDashboardUrl = '/toko/dashboard';
            } elseif ((method_exists($user, 'isPelanggan') && $user->isPelanggan()) || $user->pelanggan !== null) {
                $userRoleLabel = 'Pelanggan';
                $userDashboardUrl = '/pelanggan/dashboard';
            } elseif ($user->role) {
                $userRoleLabel = $user->role->nama;
            }

            // Tentukan target portal yang coba dibuka
            $targetPortal = 'Portal Sistem';
            $partnerRegisterUrl = null;
            if (in_array('toko-sparepart', $slugs) || str_starts_with($path, 'toko')) {
                $targetPortal = 'Dasbor Toko Sparepart';
                $partnerRegisterUrl = '/partner/register?role=toko-sparepart';
            } elseif (in_array('teknisi', $slugs) || str_starts_with($path, 'teknisi')) {
                $targetPortal = 'Dasbor Mitra Teknisi';
                $partnerRegisterUrl = '/partner/register?role=teknisi';
            } elseif (in_array('pelanggan', $slugs) || str_starts_with($path, 'pelanggan')) {
                $targetPortal = 'Dasbor Pelanggan';
            } elseif ($isAdminRoute) {
                $targetPortal = 'Portal Administrasi Khusus';
            }

            return response()->view('errors.403', [
                'error_type' => 'role_unregistered',
                'user_name' => $user->nama,
                'user_role' => $userRoleLabel,
                'target_portal' => $targetPortal,
                'user_dashboard' => $userDashboardUrl,
                'register_url' => $partnerRegisterUrl,
                'message' => 'Akun Anda saat ini masuk sebagai ' . $userRoleLabel . ' dan belum terdaftar untuk mengakses ' . $targetPortal . '.'
            ], 403);
        }

        $response = $next($request);
        if (!empty($tokenString) && !$request->hasCookie('auth_token')) {
            return $response->withCookie(cookie('auth_token', $tokenString, 120, '/', null, false, false));
        }
        return $response;
    }
}
