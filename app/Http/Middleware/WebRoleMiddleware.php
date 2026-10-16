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
        $path = $request->path();
        $isAdminRoute = str_starts_with($path, 'superadmin') || str_starts_with($path, 'admin');

        $user = null;
        try {
            // Prioritas 1: Jika query parameter _token_auth dikirim secara eksplisit (misal redirect langsung dari login admin/user)
            $explicitToken = $request->query('_token_auth');
            if ($explicitToken) {
                if (str_starts_with($explicitToken, 'Bearer ')) {
                    $explicitToken = substr($explicitToken, 7);
                }
                $pat = PersonalAccessToken::findToken($explicitToken);
                if ($pat && $pat->tokenable) {
                    $user = $pat->tokenable;
                    Auth::guard('web')->login($user);
                    if ($isAdminRoute) {
                        session(['admin_bearer_token' => $explicitToken]);
                    }
                    session(['auth_token' => $explicitToken]);
                }
            }

            // Prioritas 2: Resolve user from Web Auth Guard jika belum ada user dari token eksplisit
            if (!$user) {
                $user = Auth::guard('web')->user();
            }

            // Prioritas 3: Jika rute ini adalah portal admin tapi sesi web saat ini BUKAN admin (misal sesi pelanggan tertinggal di Chrome),
            // coba cari apakah ada token admin yang valid di cookie, session, atau header
            if ($isAdminRoute && $user && !$user->isAdminVerifikator() && !$user->isAdminResolusi() && !$user->isSuperAdminPemilik() && !$user->isSuperAdminMonitoring()) {
                $candidateToken = session('admin_bearer_token')
                    ?? $request->cookie('auth_token')
                    ?? $request->bearerToken();

                if ($candidateToken) {
                    if (str_starts_with($candidateToken, 'Bearer ')) {
                        $candidateToken = substr($candidateToken, 7);
                    }
                    $pat = PersonalAccessToken::findToken($candidateToken);
                    if ($pat && $pat->tokenable) {
                        $candUser = $pat->tokenable;
                        if ($candUser->isAdminVerifikator() || $candUser->isAdminResolusi() || $candUser->isSuperAdminPemilik() || $candUser->isSuperAdminMonitoring()) {
                            $user = $candUser;
                            Auth::guard('web')->login($user);
                        }
                    }
                }
            }

            // Prioritas 4: Jika belum ada user terotentikasi, coba resolve dari cookie/header/session
            if (!$user) {
                $tokenString = ($isAdminRoute ? session('admin_bearer_token') : null)
                    ?? $request->cookie('auth_token')
                    ?? $request->bearerToken()
                    ?? session('auth_token');

                if ($tokenString) {
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
                'is_admin_portal' => $isAdminRoute,
                'register_url' => $partnerRegisterUrl,
                'message' => 'Akun Anda saat ini masuk sebagai ' . $userRoleLabel . ' (' . $user->nama . ') dan belum terdaftar untuk mengakses ' . $targetPortal . '.'
            ], 403);
        }

        $response = $next($request);
        if (!empty($tokenString) && !$request->hasCookie('auth_token')) {
            return $response->withCookie(cookie('auth_token', $tokenString, 120, '/', null, false, false));
        }
        return $response;
    }
}
