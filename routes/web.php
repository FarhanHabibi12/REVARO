<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SuperAdmin\SmtpConfigController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — REVARO Platform
|--------------------------------------------------------------------------
| All sensitive portals are strictly protected by server-side web.role
| middleware to prevent unauthorized direct URL access and information leaks.
*/

// Welcome & Public Landing
Route::get('/', function() {
    $jasaServis = \App\Models\JasaServis::where('is_active', true)->orderBy('id', 'asc')->get();
    return view('welcome', compact('jasaServis'));
});

// Google OAuth 2.0 (Socialite)
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/complete-profile', [GoogleAuthController::class, 'showCompleteProfile'])->name('auth.google.complete');
Route::post('/auth/google/complete-profile', [GoogleAuthController::class, 'saveCompleteProfile'])->name('auth.google.complete.save');

Route::get('/login',            fn() => redirect('/?auth=login'))->name('login');
Route::get('/login-admin', function (\Illuminate\Http\Request $request) {
    // Jika laptop pemilik menginput ?device_key=..., terbitkan cookie izin akses Super Admin
    $configuredKey = env('ADMIN_DEVICE_KEY', 'revaro_device_senn_1926');
    $providedKey = $request->query('device_key');
    if ($providedKey && hash_equals((string) $configuredKey, (string) $providedKey)) {
        $expectedHash = hash_hmac('sha256', 'revaro_verified_admin_device_' . $configuredKey, config('app.key') ?: 'revaro_salt_secret');
        $cleanUrl = rtrim($request->fullUrlWithQuery(['device_key' => null]), '?');
        return redirect($cleanUrl)->withCookie(
            cookie('revaro_device_key', $expectedHash, 525600, '/', null, false, false, false, 'Lax')
        );
    }
    return view('auth.admin_login');
})->name('admin.login');
Route::get('/admin/login',      fn() => redirect('/login-admin'));
Route::get('/partner/register', fn() => view('auth.partner_register'));
Route::get('/session-expired',   fn() => response()->view('errors.419', [], 419))->name('session.expired');
Route::get('/error/419',        fn() => response()->view('errors.419', [], 419));
Route::get('/error/403',        fn() => response()->view('errors.403', [], 403));
Route::get('/error/404',        fn() => response()->view('errors.404', [], 404));
Route::get('/error/500',        fn() => response()->view('errors.500', [], 500));
Route::get('/error/429',        fn() => response()->view('errors.429', [], 429));

// ── User Portals (Server-Side Role Protected) ───────────────────────────

// Pelanggan Portal
Route::middleware(['web.role:pelanggan'])->prefix('pelanggan')->group(function () {
    Route::get('/dashboard', fn() => view('pelanggan.dashboard'))->name('pelanggan.dashboard');
    Route::get('/riwayat',   fn() => view('pelanggan.riwayat'))->name('pelanggan.riwayat');
    Route::get('/dompet',    fn() => view('pelanggan.dompet'))->name('pelanggan.dompet');
    Route::get('/settings',  fn() => view('pelanggan.settings'))->name('pelanggan.settings');
});

// Teknisi Portal
Route::middleware(['web.role:teknisi'])->prefix('teknisi')->group(function () {
    Route::get('/dashboard', fn() => view('teknisi.dashboard'))->name('teknisi.dashboard');
    Route::get('/riwayat',   fn() => view('teknisi.riwayat'))->name('teknisi.riwayat');
    Route::get('/dompet',    fn() => view('teknisi.dompet'))->name('teknisi.dompet');
    Route::get('/settings',  fn() => view('teknisi.settings'))->name('teknisi.settings');
});

// Toko Sparepart Portal
Route::middleware(['web.role:toko-sparepart'])->prefix('toko')->group(function () {
    Route::get('/dashboard', fn() => view('toko.dashboard'))->name('toko.dashboard');
    Route::get('/keuangan',  fn() => view('toko.keuangan'))->name('toko.keuangan');
    Route::get('/settings',  fn() => view('toko.settings'))->name('toko.settings');
});

// ── Super Admin Pemilik Sistem (Strict Server-Side Protected + Device Lock) ──
Route::middleware(['admin.device', 'web.role:super-admin-pemilik'])->group(function () {
    Route::prefix('superadmin/pemilik')->group(function () {
        Route::get('/dashboard',      fn() => view('superadmin.pemilik.dashboard'));
        Route::get('/accounts',       fn() => view('superadmin.pemilik.accounts'));
        Route::get('/services',       fn() => view('superadmin.pemilik.services'));
        Route::get('/commission',     fn() => view('superadmin.pemilik.commission'));
        Route::get('/regions',        fn() => view('superadmin.pemilik.regions'));
        Route::get('/flags',          fn() => view('superadmin.pemilik.flags'));
        Route::get('/disputes',       fn() => view('superadmin.pemilik.disputes'));
        Route::get('/verifications',  fn() => view('superadmin.pemilik.verifications'));
        Route::get('/backup',         fn() => view('superadmin.pemilik.backup'));
        Route::get('/audit-logs',     fn() => view('superadmin.pemilik.audit_logs'));
        Route::get('/security',       fn() => view('superadmin.pemilik.security'));
        Route::get('/jejak-audit',    fn() => view('superadmin.pemilik.jejak_audit'));
        Route::get('/settings',       fn() => view('superadmin.pemilik.settings'));
        Route::get('/profile',        fn() => view('superadmin.pemilik.profile'));
        Route::get('/maps-api',       fn() => view('superadmin.pemilik.maps_api'))->name('superadmin.maps_api');
        Route::get('/smtp',           [SmtpConfigController::class, 'edit'])->name('superadmin.smtp.edit');
        Route::get('/maintenance',    fn() => view('superadmin.pemilik.maintenance'));
    });

    // SMTP Configuration Routes
    Route::get('/superadmin/smtp',       [SmtpConfigController::class, 'edit']);
    Route::post('/superadmin/smtp',      [SmtpConfigController::class, 'update'])->name('superadmin.smtp.update');
    Route::post('/superadmin/smtp/test', [SmtpConfigController::class, 'testConnection'])->name('superadmin.smtp.test');

    // Convenient Super Admin URL Aliases
    Route::get('/superadmin/keamanan',    fn() => redirect('/superadmin/pemilik/security'));
    Route::get('/superadmin/jejak-audit', fn() => redirect('/superadmin/pemilik/jejak-audit'));
    Route::get('/superadmin/database',    fn() => redirect('/superadmin/pemilik/backup'));
    Route::get('/superadmin/settings',    fn() => redirect('/superadmin/pemilik/settings'));
    Route::get('/superadmin/maintenance', fn() => redirect('/superadmin/pemilik/maintenance'));
});

// ── Super Admin Monitoring Kinerja (Server-Side Protected + Device Lock) ────
Route::middleware(['admin.device', 'web.role:super-admin-monitoring,super-admin-pemilik'])->prefix('superadmin/monitoring')->group(function () {
    Route::get('/scorecard-verifikator', fn() => view('superadmin.monitoring.scorecard_verifikator'));
    Route::get('/scorecard-resolusi',    fn() => view('superadmin.monitoring.scorecard_resolusi'));
    Route::get('/trends',                fn() => view('superadmin.monitoring.trends'));
    Route::get('/alerts',                fn() => view('superadmin.monitoring.alerts'));
});

// ── Admin Verifikator (Server-Side Role Protected) ─────────────────────────
Route::middleware(['web.role:admin-verifikator,super-admin-pemilik'])->prefix('admin/verifikator')->group(function () {
    Route::get('/dashboard',       fn() => view('admin.verifikator.dashboard'));
    Route::get('/resubmitted',     fn() => view('admin.verifikator.resubmitted'));
    Route::get('/reverifications', fn() => view('admin.verifikator.reverifications'));
    Route::get('/history',         fn() => view('admin.verifikator.history'));
    Route::get('/instruksi',       fn() => view('admin.verifikator.instruksi'));
    Route::get('/settings',        fn() => view('admin.verifikator.settings'));
});

// ── Admin Resolusi (Server-Side Role Protected) ────────────────────────────
Route::middleware(['web.role:admin-resolusi,super-admin-pemilik'])->prefix('admin/resolusi')->group(function () {
    Route::get('/dashboard',       fn() => view('admin.resolusi.dashboard'));
    Route::get('/evaluasi-rating', fn() => view('admin.resolusi.evaluasi_rating'));
    Route::get('/history',         fn() => view('admin.resolusi.history'));
    Route::get('/instruksi',       fn() => view('admin.resolusi.instruksi'));
    Route::get('/settings',        fn() => view('admin.resolusi.settings'));
});
