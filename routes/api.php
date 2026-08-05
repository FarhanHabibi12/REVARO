<?php

use App\Http\Controllers\Api\AdminAccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\OrderLifecycleController;
use App\Http\Controllers\Api\SparepartSearchController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DisputeController;
use App\Http\Controllers\Api\MonitoringDashboardController;
use App\Models\Dispute;
use App\Models\Order;
use App\Models\PendaftaranVerifikasi;
use App\Models\Sparepart;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- Public Auth Routes (with Rate Limiting) ---
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

// --- Protected Routes ---
Route::middleware(['auth:sanctum', 'read_only'])->group(function () {
    
    // Auth actions
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/profile/resubmit', [AuthController::class, 'resubmit']);

    // Onboarding & Verification (Admin Verifikator only)
    Route::get('/verifications/pending', [VerificationController::class, 'index'])
        ->middleware('role:admin-verifikator');
    Route::post('/verifications/decision', [VerificationController::class, 'verify'])
        ->middleware('role:admin-verifikator');

    // Admin account management (Super Admin Pemilik only)
    Route::post('/admin/accounts', [AdminAccountController::class, 'store'])
        ->middleware('role:super-admin-pemilik');

    // Monitoring Flag Exception Route (Excluded from ReadOnlyMiddleware write block)
    Route::post('/monitoring/flags', [MonitoringDashboardController::class, 'storeFlag'])
        ->name('monitoring.flags.store')->middleware('role:super-admin-monitoring');

    // Monitoring Dashboard Metrics
    Route::get('/monitoring/metrics', [MonitoringDashboardController::class, 'getMetrics'])
        ->middleware('role:super-admin-monitoring,super-admin-pemilik');

    // --- Order Lifecycle Endpoints ---
    Route::get('/orders', [OrderLifecycleController::class, 'index']);
    Route::get('/orders/{order}', [OrderLifecycleController::class, 'show']);
    Route::post('/orders', [OrderLifecycleController::class, 'createSOS']);
    Route::post('/orders/{order}/accept', [OrderLifecycleController::class, 'acceptOrder']);
    Route::post('/orders/{order}/ontheway', [OrderLifecycleController::class, 'startOnTheWay']);
    Route::post('/orders/{order}/arrive', [OrderLifecycleController::class, 'arriveAndDiagnose']);
    Route::post('/orders/{order}/spareparts', [OrderLifecycleController::class, 'requestSpareparts']);
    Route::post('/orders/{order}/spareparts/approve', [OrderLifecycleController::class, 'approveSpareparts']);
    Route::post('/toko/orders/{order}/spareparts/confirm', [OrderLifecycleController::class, 'confirmSparepartsByShop']);
    Route::post('/orders/{order}/complete', [OrderLifecycleController::class, 'completeService']);
    Route::post('/orders/{order}/pay', [OrderLifecycleController::class, 'payOrder']);
    Route::post('/orders/{order}/confirm-release', [OrderLifecycleController::class, 'confirmRelease']);
    Route::post('/orders/{order}/cancel', [OrderLifecycleController::class, 'cancelOrder']);

    // --- Sparepart Search ---
    Route::get('/spareparts/search', [SparepartSearchController::class, 'search']);

    // --- Chat Messages ---
    Route::post('/orders/{order}/chats', [ChatController::class, 'sendMessage']);
    Route::get('/orders/{order}/chats', [ChatController::class, 'getMessages']);

    // --- Dispute Endpoints ---
    Route::get('/disputes', [DisputeController::class, 'index']);
    Route::post('/disputes', [DisputeController::class, 'createDispute']);
    Route::get('/disputes/{dispute}', [DisputeController::class, 'getDisputeDetails']);
    Route::post('/disputes/{dispute}/resolve', [DisputeController::class, 'resolveDispute']);

    // --- Policy Testing Endpoints ---
    
    // Orders
    Route::get('/test/orders/{order}', function (Order $order) {
        if (Gate::denies('view', $order)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json($order);
    });

    Route::post('/test/orders', function (Request $request) {
        if (Gate::denies('create', Order::class)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Order created successfully.']);
    });

    Route::put('/test/orders/{order}', function (Request $request, Order $order) {
        if (Gate::denies('update', $order)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Order updated successfully.']);
    });

    // Disputes
    Route::get('/test/disputes/{dispute}', function (Dispute $dispute) {
        if (Gate::denies('view', $dispute)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json($dispute);
    });

    Route::put('/test/disputes/{dispute}', function (Request $request, Dispute $dispute) {
        if (Gate::denies('update', $dispute)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Dispute resolved.']);
    });

    // Pendaftaran Verifikasi
    Route::get('/test/verifications/{verification}', function (PendaftaranVerifikasi $verification) {
        if (Gate::denies('view', $verification)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json($verification);
    });

    Route::post('/test/verifications', function (Request $request) {
        if (Gate::denies('create', PendaftaranVerifikasi::class)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Verification processed.']);
    });

    // Spareparts
    Route::get('/test/spareparts/{sparepart}', function (Sparepart $sparepart) {
        if (Gate::denies('view', $sparepart)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json($sparepart);
    });

    Route::post('/test/spareparts', function (Request $request) {
        if (Gate::denies('create', Sparepart::class)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Sparepart added.']);
    });

    // Transactions
    Route::get('/test/transactions/{transaksi}', function (Transaksi $transaksi) {
        if (Gate::denies('view', $transaksi)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json($transaksi);
    });

    Route::post('/test/transactions', function (Request $request) {
        if (Gate::denies('create', Transaksi::class)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }
        return response()->json(['message' => 'Transaction modified.']);
    });
});
