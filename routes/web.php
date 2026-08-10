<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome & Public
Route::get('/', fn() => view('welcome'));

// Auth — Login & Register
Route::get('/admin/login',      fn() => view('auth.admin_login'));
Route::get('/partner/register', fn() => view('auth.partner_register'));

// ── Super Admin Pemilik Sistem ──────────────────────────────────────────
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
});

// ── Super Admin Monitoring Kinerja ─────────────────────────────────────
Route::prefix('superadmin/monitoring')->group(function () {
    Route::get('/scorecard-verifikator', fn() => view('superadmin.monitoring.scorecard_verifikator'));
    Route::get('/scorecard-resolusi',    fn() => view('superadmin.monitoring.scorecard_resolusi'));
    Route::get('/trends',                fn() => view('superadmin.monitoring.trends'));
    Route::get('/alerts',                fn() => view('superadmin.monitoring.alerts'));
});

// ── Admin Verifikator ───────────────────────────────────────────────────
Route::prefix('admin/verifikator')->group(function () {
    Route::get('/dashboard',       fn() => view('admin.verifikator.dashboard'));
    Route::get('/resubmitted',     fn() => view('admin.verifikator.resubmitted'));
    Route::get('/reverifications', fn() => view('admin.verifikator.reverifications'));
    Route::get('/history',         fn() => view('admin.verifikator.history'));
});

// ── Admin Resolusi ──────────────────────────────────────────────────────
Route::prefix('admin/resolusi')->group(function () {
    Route::get('/dashboard',       fn() => view('admin.resolusi.dashboard'));
    Route::get('/history',         fn() => view('admin.resolusi.history'));
});
