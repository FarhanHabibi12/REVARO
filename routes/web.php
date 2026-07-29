<?php

use Illuminate\Support\Facades\Route;
use App\Models\Teknisi;
use App\Models\Toko;
use App\Models\Dispute;
use App\Http\Controllers\Auth\AuthController;

// ==== Root ====
Route::get('/', function () {
    return redirect()->route(
        auth()->check() && auth()->user()->role === 'super_admin' ? 'superadmin.dashboard' : 'admin.dashboard'
    );
});

// ==== Auth (guest only) ====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ==== Admin: verifikasi & sengketa ====
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', function () {
        return view('admin.dashboard', [
            'verifikasiTeknisi' => Teknisi::where('status', 'pending_review')->get(),
            'verifikasiToko'    => Toko::where('status', 'pending_review')->get(),
            'disputes'          => Dispute::latest()->get(),
        ]);
    })->name('dashboard');

    Route::post('/verifikasi/teknisi/{teknisi}/approve', function (Teknisi $teknisi) {
        $teknisi->update(['status' => 'active']);
        return back();
    })->name('verifikasi.teknisi.approve');

    Route::post('/verifikasi/teknisi/{teknisi}/reject', function (Teknisi $teknisi) {
        $teknisi->update(['status' => 'suspended']);
        return back();
    })->name('verifikasi.teknisi.reject');

    Route::post('/verifikasi/toko/{toko}/approve', function (Toko $toko) {
        $toko->update(['status' => 'active']);
        return back();
    })->name('verifikasi.toko.approve');

    Route::post('/verifikasi/toko/{toko}/reject', function (Toko $toko) {
        $toko->update(['status' => 'needs_revision']);
        return back();
    })->name('verifikasi.toko.reject');

    Route::get('/sengketa/{dispute}', function (Dispute $dispute) {
        return view('admin.sengketa-detail', compact('dispute')); // buat view ini menyusul
    })->name('sengketa.show');
});

// ==== Super admin: monitoring kinerja ====
Route::middleware('auth')->prefix('super-admin')->name('superadmin.')->group(function () {

    Route::get('/', function () {
        return view('superadmin.dashboard');
        // isi $adminScorecard & $alerts dari controller/model kamu sendiri nanti
    })->name('dashboard');
});
