<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Laravel\Sanctum\PersonalAccessToken;

class PreventRequestsDuringMaintenance
{
    /**
     * Handle an incoming request and enforce platform maintenance mode from database settings.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // 1. Check Global Maintenance Setting
            $isGlobal = SystemSetting::get('maintenance_global') === 'true' 
                     || SystemSetting::get('maintenance_global') === '1'
                     || SystemSetting::get('maintenance_mode') === 'active'
                     || SystemSetting::get('maintenance_mode') === 'true';

            // 2. Check Selective Role Maintenance Settings
            $maintVerifikator = SystemSetting::get('maintenance_role_verifikator') === 'true' || SystemSetting::get('maintenance_role_verifikator') === '1';
            $maintResolusi   = SystemSetting::get('maintenance_role_resolusi') === 'true' || SystemSetting::get('maintenance_role_resolusi') === '1';
            $maintMitra      = SystemSetting::get('maintenance_role_mitra') === 'true' || SystemSetting::get('maintenance_role_mitra') === '1';
            $maintPelanggan  = SystemSetting::get('maintenance_role_pelanggan') === 'true' || SystemSetting::get('maintenance_role_pelanggan') === '1';

            if (!$isGlobal && !$maintVerifikator && !$maintResolusi && !$maintMitra && !$maintPelanggan) {
                return $next($request);
            }

            // 3. Excluded Paths for Super Admin & Authentication
            // Super Admin portal and login endpoints are strictly protected but accessible to Super Admins
            $superAdminPatterns = [
                'login-admin',
                'admin/login',
                'superadmin*',
                'api/auth/login-admin',
                'api/auth/login',
                'api/auth/verify-otp',
                'api/auth/resend-otp',
                'api/auth/logout',
                'api/admin/maintenance*',
                'api/admin/settings*',
                'api/admin/security*',
                'api/admin/backup*',
                'api/admin/accounts*',
                'api/superadmin*',
                'api/monitoring*',
                'up',
            ];

            foreach ($superAdminPatterns as $pattern) {
                if ($request->is($pattern)) {
                    return $next($request);
                }
            }

            // 4. Resolve authenticated user (Session or Sanctum Bearer Token)
            $user = $request->user();
            if (!$user && $request->bearerToken()) {
                $pat = PersonalAccessToken::findToken($request->bearerToken());
                if ($pat) {
                    $user = $pat->tokenable;
                }
            }

            // 5. Immunity for Super Admin (Pemilik Sistem & Monitoring Kinerja)
            if ($user) {
                $isSuperAdmin = false;
                if (method_exists($user, 'isSuperAdminPemilik') && $user->isSuperAdminPemilik()) {
                    $isSuperAdmin = true;
                }
                if (method_exists($user, 'isSuperAdminMonitoring') && $user->isSuperAdminMonitoring()) {
                    $isSuperAdmin = true;
                }
                if ($isSuperAdmin) {
                    return $next($request);
                }
            }

            // 6. IP Bypass Check (Whitelisted IPs)
            $ipBypassString = (string) SystemSetting::get('maintenance_ip_bypass', '');
            if (!empty($ipBypassString)) {
                $bypassIps = array_filter(array_map('trim', explode(',', $ipBypassString)));
                if (in_array($request->ip(), $bypassIps, true)) {
                    return $next($request);
                }
            }

            // 7. If Global maintenance is OFF, evaluate Selective Role Maintenance
            if (!$isGlobal) {
                $roleHit = false;

                // Check Admin Verifikator routes or role
                if ($maintVerifikator && ($request->is('admin/verifikator*') || ($user && method_exists($user, 'isAdminVerifikator') && $user->isAdminVerifikator()))) {
                    $roleHit = true;
                }
                // Check Admin Resolusi routes or role
                if ($maintResolusi && ($request->is('admin/resolusi*') || ($user && method_exists($user, 'isAdminResolusi') && $user->isAdminResolusi()))) {
                    $roleHit = true;
                }
                // Check Pelanggan routes or role
                if ($maintPelanggan && ($request->is('pelanggan*') || ($user && method_exists($user, 'isPelanggan') && $user->isPelanggan()))) {
                    $roleHit = true;
                }
                // Check Mitra (Teknisi / Toko) routes or role
                if ($maintMitra && ($request->is('teknisi*') || $request->is('toko*') || ($user && ($user->isTeknisi() || $user->isToko())))) {
                    $roleHit = true;
                }

                if (!$roleHit) {
                    return $next($request);
                }
            }

            // 8. Determine if current context is Admin vs Regular User (User / Customer)
            // Regular user: Light theme (Terang). Admin: Dark theme (Gelap).
            $isAdmin = false;
            if ($request->is('admin*') || $request->is('*/admin*') || $request->is('api/admin*')) {
                $isAdmin = true;
            } elseif ($user) {
                $roleName = $user->role ? $user->role->nama : '';
                if (str_contains(strtolower($roleName), 'admin') || str_contains(strtolower($roleName), 'verifikator') || str_contains(strtolower($roleName), 'resolusi')) {
                    $isAdmin = true;
                }
            }

            $message = SystemSetting::get('maintenance_message') 
                ?: 'Platform REVARO sedang dalam pemeliharaan berkala untuk peningkatan stabilitas dan kualitas performa. Silakan coba beberapa saat lagi.';
            $eta = SystemSetting::get('maintenance_eta');

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => $message,
                    'is_maintenance' => true,
                    'is_admin' => $isAdmin,
                    'eta' => $eta,
                ], 503);
            }

            return response()->view('errors.503', [
                'message' => $message,
                'eta' => $eta,
                'isAdmin' => $isAdmin,
            ], 503);
        } catch (\Throwable $e) {
            // If database is not ready or during schema updates, fallback gracefully
            return $next($request);
        }
    }
}
