<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminDeviceLockMiddleware
{
    /**
     * Kunci Rahasia Perangkat (Device Secret Token).
     * Mencegah siapa pun di jaringan WiFi/hotspot yang sama membuka portal admin
     * kecuali browser laptop pemilik yang telah di-otorisasi dengan device_key.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = env('ADMIN_DEVICE_KEY', 'revaro_device_senn_1926');
        $expectedHash = hash_hmac('sha256', 'revaro_verified_admin_device_' . $configuredKey, config('app.key') ?: 'revaro_salt_secret');

        // 1. Cek apakah ada aktivasi perangkat baru via URL (?device_key=...)
        $providedKey = $request->query('device_key');
        if ($providedKey && hash_equals((string) $configuredKey, (string) $providedKey)) {
            // Bersihkan query param dari URL agar kunci tidak tersimpan di riwayat browser
            $cleanUrl = rtrim($request->fullUrlWithQuery(['device_key' => null]), '?');

            // Set cookie rahasia 1 tahun (525600 menit), HttpOnly, SameSite=Lax
            return redirect($cleanUrl)->withCookie(
                cookie('revaro_device_key', $expectedHash, 525600, '/', null, false, true, false, 'Lax')
            );
        }

        // 2. Cek apakah browser ini sudah memiliki cookie rahasia yang sah
        $cookieToken = $request->cookie('revaro_device_key');
        if ($cookieToken && (hash_equals($expectedHash, $cookieToken) || hash_equals((string) $configuredKey, $cookieToken))) {
            return $next($request);
        }

        // 3. Jika perangkat TIDAK dikenal:
        if (auth()->check()) {
            \Illuminate\Support\Facades\Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login-admin?unauthorized_device=1')
                ->withCookie(cookie()->forget('auth_token'));
        }
        abort(404, 'Halaman yang Anda tuju tidak ditemukan.');
    }
}
