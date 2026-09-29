<?php

use App\Http\Controllers\Api\AdminAccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BackupDbController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\OrderLifecycleController;
use App\Http\Controllers\Api\SparepartSearchController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DisputeController;
use App\Http\Controllers\Api\MonitoringDashboardController;
use App\Http\Controllers\Api\JasaServisController;
use App\Http\Controllers\Api\KebijakanKomisiController;
use App\Http\Controllers\Api\WilayahOperasionalController;
use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\SystemSettingController;
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
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('/mitra/documents/{type}/{id}', [AuthController::class, 'downloadDocument']);

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
    Route::get('/verifications/my-decisions', [VerificationController::class, 'myDecisions'])
        ->middleware('role:admin-verifikator');

    // Admin personal profile management
    Route::post('/admin/profile', [AdminAccountController::class, 'updateProfile']);
    Route::post('/admin/profile/password', [AdminAccountController::class, 'updatePassword']);

    // Admin account management (Super Admin Pemilik only)
    Route::get('/admin/accounts', [AdminAccountController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/accounts', [AdminAccountController::class, 'store'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'patch'], '/admin/accounts/{id}/toggle', [AdminAccountController::class, 'toggleActive'])
        ->middleware('role:super-admin-pemilik');
    Route::get('/admin/audit-logs', [AdminAccountController::class, 'getAuditLogs'])
        ->middleware('role:super-admin-pemilik');

    // Kategori Servis (Super Admin Pemilik only)
    Route::get('/admin/services', [JasaServisController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/services', [JasaServisController::class, 'store'])
        ->middleware('role:super-admin-pemilik');
    Route::put('/admin/services/{id}', [JasaServisController::class, 'update'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'patch'], '/admin/services/{id}/toggle', [JasaServisController::class, 'toggleActive'])
        ->middleware('role:super-admin-pemilik');

    // Kebijakan Komisi (Super Admin Pemilik only)
    Route::get('/admin/commission-policy', [KebijakanKomisiController::class, 'getPolicy'])
        ->middleware('role:super-admin-pemilik');
    Route::put('/admin/commission-policy', [KebijakanKomisiController::class, 'updatePolicy'])
        ->middleware('role:super-admin-pemilik');

    // Wilayah Operasional (Super Admin Pemilik only)
    Route::get('/admin/regions', [WilayahOperasionalController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/regions', [WilayahOperasionalController::class, 'store'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'patch'], '/admin/regions/{id}/toggle', [WilayahOperasionalController::class, 'toggleActive'])
        ->middleware('role:super-admin-pemilik');

    // Flag Notifikasi (Super Admin Pemilik only)
    Route::get('/admin/flags', [MonitoringDashboardController::class, 'getFlags'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'patch'], '/admin/flags/{id}/read', function($id) {
        $notification = \App\Models\Notifikasi::find($id);
        if ($notification) {
            $notification->update(['dibaca' => true]);
        }
        return response()->json(['message' => 'Success']);
    })->middleware('role:super-admin-pemilik');

    Route::post('/admin/notifications', function(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'judul' => 'required|string',
            'pesan' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $notification = \App\Models\Notifikasi::create([
            'user_id' => $request->user_id,
            'judul' => $request->judul,
            'pesan' => $request->pesan,
            'dibaca' => false,
        ]);
        return response()->json($notification, 201);
    })->middleware('role:super-admin-pemilik');

    // Dispute eskalasi list & resolution (Super Admin Pemilik only)
    Route::get('/admin/disputes', [DisputeController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'patch'], '/admin/disputes/{dispute}/resolve', [DisputeController::class, 'resolveDispute'])
        ->middleware('role:super-admin-pemilik');

    // Onboarding verifications overview (Super Admin Pemilik only)
    Route::get('/admin/verifications', [VerificationController::class, 'getAllVerifications'])
        ->middleware('role:super-admin-pemilik');

    // Dashboard metrics
    Route::get('/admin/financial-summary', [MonitoringDashboardController::class, 'getFinancialSummary'])
        ->middleware('role:super-admin-pemilik');
    Route::get('/admin/monthly-trends', [MonitoringDashboardController::class, 'getMonthlyTrends'])
        ->middleware('role:super-admin-pemilik');
    Route::get('/admin/verification-summary', [MonitoringDashboardController::class, 'getVerificationSummary'])
        ->middleware('role:super-admin-pemilik');

    // Database Backup, Restore, Cache & Reset (Super Admin Pemilik only)
    Route::get('/admin/database/backup', [BackupDbController::class, 'backup'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/database/restore', [BackupDbController::class, 'restore'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/database/clear-cache', [BackupDbController::class, 'clearCache'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/database/reset-transactions', [BackupDbController::class, 'resetTransactions'])
        ->middleware('role:super-admin-pemilik');

    // Audit Log System (Super Admin Pemilik only)
    Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::get('/admin/audit-logs/export', [AuditLogController::class, 'export'])
        ->middleware('role:super-admin-pemilik');

    // System Settings (Super Admin Pemilik only)
    Route::get('/admin/settings', [SystemSettingController::class, 'index'])
        ->middleware('role:super-admin-pemilik');
    Route::match(['post', 'put'], '/admin/settings', [SystemSettingController::class, 'update'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/settings/upload-asset', [SystemSettingController::class, 'uploadAsset'])
        ->middleware('role:super-admin-pemilik');

    // Maintenance Mode System (Super Admin Pemilik only)
    Route::get('/admin/maintenance', [SystemSettingController::class, 'getMaintenance'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/maintenance', [SystemSettingController::class, 'saveMaintenance'])
        ->middleware('role:super-admin-pemilik');

    // Security & User Account status (Super Admin Pemilik only)
    Route::get('/admin/security/active-visitors', [\App\Http\Controllers\Api\SecurityController::class, 'listActiveVisitors'])
        ->middleware('role:super-admin-pemilik');
    Route::get('/admin/security/blacklist', [\App\Http\Controllers\Api\SecurityController::class, 'listBlacklist'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/security/blacklist', [\App\Http\Controllers\Api\SecurityController::class, 'banIp'])
        ->middleware('role:super-admin-pemilik');
    Route::delete('/admin/security/blacklist/{id}', [\App\Http\Controllers\Api\SecurityController::class, 'unbanIp'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/auth/switch-role', [AuthController::class, 'switchRole']);

    Route::post('/admin/security/sessions/kick', [\App\Http\Controllers\Api\SecurityController::class, 'kickSession'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/security/logs/purge', [\App\Http\Controllers\Api\SecurityController::class, 'purgeIncidentLogs'])
        ->middleware('role:super-admin-pemilik');

    // Storage Orphan Files Cleaner (Super Admin Pemilik only)
    Route::get('/admin/storage/orphans', [\App\Http\Controllers\Api\StorageCleanerController::class, 'getOrphans'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/admin/storage/clean', [\App\Http\Controllers\Api\StorageCleanerController::class, 'cleanOrphans'])
        ->middleware('role:super-admin-pemilik');

    // Sent flags history
    Route::get('/admin/flags/sent', function(Request $request) {
        $flags = \App\Models\Notifikasi::with('user')
            ->where('judul', 'like', '%FLAG ALERT%')
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($flags);
    });

    // Backward compatibility aliases
    Route::get('/jasa-servis', [JasaServisController::class, 'index']);
    Route::post('/jasa-servis/store', [JasaServisController::class, 'store'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/jasa-servis/{id}', [JasaServisController::class, 'update'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/jasa-servis/{id}/toggle-active', [JasaServisController::class, 'toggleActive'])
        ->middleware('role:super-admin-pemilik');

    Route::get('/master/kebijakan-komisi', [KebijakanKomisiController::class, 'getPolicy']);
    Route::post('/master/kebijakan-komisi', [KebijakanKomisiController::class, 'updatePolicy'])
        ->middleware('role:super-admin-pemilik');

    Route::get('/master/wilayah-operasional', [WilayahOperasionalController::class, 'index']);
    Route::post('/master/wilayah-operasional', [WilayahOperasionalController::class, 'store'])
        ->middleware('role:super-admin-pemilik');
    Route::post('/master/wilayah-operasional/{id}/toggle-active', [WilayahOperasionalController::class, 'toggleActive'])
        ->middleware('role:super-admin-pemilik');

    // Monitoring Flag Exception Route (Excluded from ReadOnlyMiddleware write block)
    Route::post('/monitoring/flags', [MonitoringDashboardController::class, 'storeFlag'])
        ->name('monitoring.flags.store')->middleware('role:super-admin-monitoring');
    Route::get('/monitoring/flags', [MonitoringDashboardController::class, 'getFlags'])
        ->middleware('role:super-admin-pemilik');

    // Monitoring Dashboard Metrics
    Route::get('/monitoring/metrics', [MonitoringDashboardController::class, 'getMetrics'])
        ->middleware('role:super-admin-monitoring,super-admin-pemilik');

    // --- Order Lifecycle Endpoints ---
    Route::get('/orders', [OrderLifecycleController::class, 'index']);
    Route::get('/orders/available', [OrderLifecycleController::class, 'available']);
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

    // --- Wallet Endpoints ---
    Route::get('/wallet/balance', [\App\Http\Controllers\Api\WalletController::class, 'getBalance']);
    Route::post('/wallet/withdraw', [\App\Http\Controllers\Api\WalletController::class, 'withdraw']);
    Route::post('/wallet/topup', [\App\Http\Controllers\Api\WalletController::class, 'topup']);

    // --- Sparepart Search ---
    Route::get('/spareparts/search', [SparepartSearchController::class, 'search']);

    // --- Chat Messages ---
    Route::post('/orders/{order}/chats', [ChatController::class, 'sendMessage']);
    Route::get('/orders/{order}/chats', [ChatController::class, 'getMessages']);

    // --- Dispute Endpoints for Admin Resolusi ---
    Route::get('/disputes/my-assigned', [DisputeController::class, 'myAssigned'])
        ->middleware('role:admin-resolusi');
    Route::get('/disputes/history/mitra', [DisputeController::class, 'getMitraViolations'])
        ->middleware('role:admin-resolusi');
    Route::get('/disputes/my-decisions', [DisputeController::class, 'myDecisions'])
        ->middleware('role:admin-resolusi');

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
        $user = $request->user();
        if (!$user->isToko() && !$user->isSuperAdminPemilik()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($user->isToko()) {
            if (!$user->toko || $user->toko->status !== 'ACTIVE') {
                return response()->json(['message' => 'Forbidden.'], 403);
            }
        }

        $tokoId = $request->toko_id;
        if (!$tokoId && $user->isToko() && $user->toko) {
            $tokoId = $user->toko->id;
        }
        $request->merge(['toko_id' => $tokoId]);

        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'merk' => 'required|string|max:255',
            'toko_id' => 'required|integer|exists:toko,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $sparepart = Sparepart::updateOrCreate(
            ['id' => $request->id],
            [
                'toko_id' => $request->toko_id,
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'merk' => $request->merk,
            ]
        );

        return response()->json([
            'message' => 'Sparepart berhasil disimpan.',
            'sparepart' => $sparepart
        ], 200);
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
