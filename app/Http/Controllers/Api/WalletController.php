<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Get wallet balance and transactions.
     */
    public function getBalance(Request $request)
    {
        $user = $request->user();
        $balance = $user->getUnifiedSaldo();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'balance' => $balance,
            'saldo' => $balance,
            'transactions' => $transactions
        ]);
    }

    /**
     * Request withdrawal with full bank details & pessimistic locking.
     */
    public function withdraw(\App\Http\Requests\WithdrawalRequest $request)
    {
        $user = $request->user();
        $withdrawalService = new \App\Services\WithdrawalService();

        try {
            $withdrawal = $withdrawalService->createWithdrawal($user, $request->validated());

            $profile = $user->pelanggan ?: ($user->teknisi ?: $user->toko);

            // Record ledger transaction
            WalletTransaction::create([
                'user_id'   => $user->id,
                'tipe'      => 'OUT',
                'jumlah'    => $withdrawal->amount,
                'deskripsi' => "Penarikan dana ke {$withdrawal->bank_name} a.n. {$withdrawal->account_name} ({$withdrawal->account_number})",
            ]);

            return response()->json([
                'message'     => 'Permintaan penarikan dana berhasil diajukan. Admin akan memproses dalam 1x24 jam.',
                'withdrawal'  => $withdrawal,
                'new_balance' => $user->getUnifiedSaldo(),
                'saldo'       => $user->getUnifiedSaldo(),
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memproses penarikan dana: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Top-up balance via Midtrans Sandbox Snap payment gateway.
     */
    public function topup(\App\Http\Requests\TopUpRequest $request)
    {
        $user = $request->user();

        // Amount has been sanitized to pure integer in TopUpRequest
        $amount = (float) $request->input('amount', $request->input('jumlah'));

        if ($amount < 10000) {
            return response()->json(['message' => 'Nominal top up minimal adalah Rp 10.000.'], 422);
        }

        $profile = $user->pelanggan ?: ($user->teknisi ?: ($user->toko ?: null));
        if (!$profile) {
            $profile = \App\Models\Pelanggan::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'hp' => $user->no_telp ?? '081234567890',
                    'alamat' => 'Alamat Topup',
                    'saldo' => 0.00,
                ]
            );
        }

        $orderId = 'TOPUP-' . $user->id . '-' . time() . '-' . mt_rand(100, 999);
        $phone = $profile->hp ?? ($profile->telepon ?? '081234567890');

        $midtransService = new \App\Services\MidtransService();

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $user->nama ?: 'Pengguna REVARO',
                'email' => $user->email,
                'phone' => $phone,
            ],
            'item_details' => [
                [
                    'id' => 'TOPUP-REVARO',
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo REVARO Rp ' . number_format($amount, 0, ',', '.'),
                ]
            ],
            'page_options' => [
                'language' => 'id',
            ],
            'ui' => [
                'language' => 'id',
            ],
        ];

        try {
            $snapData = $midtransService->createSnapTransaction($payload);

            // Simpan catatan transaksi pending
            WalletTransaction::create([
                'user_id'   => $user->id,
                'tipe'      => 'PENDING',
                'jumlah'    => $amount,
                'deskripsi' => "Menunggu pembayaran Midtrans [ORDER_ID:{$orderId}]",
            ]);

            return response()->json([
                'message'      => 'Instruksi pembayaran top-up berhasil dibuat.',
                'snap_token'   => $snapData['token'],
                'redirect_url' => $snapData['redirect_url'] ?? null,
                'order_id'     => $orderId,
                'amount'       => $amount,
                'client_key'   => $midtransService->getClientKey(),
            ]);

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Midtrans Topup Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal membuat tagihan pembayaran Midtrans: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook notification handler from Midtrans Sandbox.
     */
    public function handleMidtransNotification(Request $request)
    {
        $orderId = (string) $request->input('order_id');
        $statusCode = (string) $request->input('status_code');
        $grossAmount = (string) $request->input('gross_amount');
        $signatureKey = (string) $request->input('signature_key');
        $transactionStatus = (string) $request->input('transaction_status');
        $fraudStatus = (string) $request->input('fraud_status');

        $midtransService = new \App\Services\MidtransService();

        if (!$midtransService->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature key.'], 403);
        }

        // Parse user id from order_id format (e.g. TOPUP-{userId}-{timestamp}-{rand})
        $user = null;
        if (str_starts_with($orderId, 'TOPUP-')) {
            $parts = explode('-', $orderId);
            $userId = isset($parts[1]) ? (int) $parts[1] : null;
            $user = $userId ? \App\Models\User::find($userId) : null;
        }

        if (!$user) {
            $pendingRecord = WalletTransaction::where('deskripsi', 'like', "%{$orderId}%")->first();
            if ($pendingRecord) {
                $user = \App\Models\User::find($pendingRecord->user_id);
            }
        }

        if (!$user) {
            return response()->json(['message' => 'User not found for this transaction.'], 404);
        }

        $userId = $user->id;

        // Cek apakah transaksi sudah pernah diselesaikan sebelumnya (idempotency)
        $alreadySuccess = WalletTransaction::where('user_id', $userId)
            ->where('tipe', 'IN')
            ->where('deskripsi', 'like', "%{$orderId}%")
            ->exists();

        if ($alreadySuccess) {
            return response()->json(['message' => 'Transaction already processed.'], 200);
        }

        // Cari record transaksi PENDING yang sesuai untuk verifikasi nominal asli
        $pendingTx = WalletTransaction::where('user_id', $userId)
            ->where('tipe', 'PENDING')
            ->where('deskripsi', 'like', "%{$orderId}%")
            ->first();

        // Validasi nominal jika record pending ditemukan
        if ($pendingTx && abs(((float) $pendingTx->jumlah) - ((float) $grossAmount)) > 0.01) {
            \Illuminate\Support\Facades\Log::warning("Midtrans Webhook: Nominal tidak cocok untuk {$orderId}. Expected {$pendingTx->jumlah}, received {$grossAmount}");
            return response()->json(['message' => 'Gross amount mismatch.'], 422);
        }

        $isPaid = false;
        if ($transactionStatus === 'capture') {
            if ($fraudStatus === 'accept') {
                $isPaid = true;
            }
        } elseif ($transactionStatus === 'settlement') {
            $isPaid = true;
        }

        if ($isPaid) {
            $settleResult = $this->settleTopupTransaction($user, $orderId, (float) $grossAmount, $request);
            return response()->json([
                'message'          => $settleResult['message'],
                'new_balance'      => $settleResult['new_balance'],
                'is_newly_settled' => $settleResult['is_newly_settled'],
            ], 200);
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            WalletTransaction::where('user_id', $user->id)
                ->where('deskripsi', 'like', "%{$orderId}%")
                ->update([
                    'deskripsi' => "Pembayaran Midtrans {$transactionStatus} [ORDER_ID:{$orderId}]",
                ]);

            return response()->json(['message' => "Transaction status updated to {$transactionStatus}."], 200);
        }

        return response()->json(['message' => 'Notification recorded.'], 200);
    }

    /**
     * Cek status pembayaran secara langsung ke server Midtrans API.
     * Dipanggil otomatis oleh frontend saat Snap onSuccess, onClose, atau saat klik Refresh Saldo.
     */
    public function checkStatus(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $orderId = (string) $request->input('order_id');

        // Jika order_id tidak dikirim, ambil order_id dari transaksi PENDING terakhir milik user
        if (!$orderId) {
            $latestPending = WalletTransaction::where('user_id', $user->id)
                ->where('tipe', 'PENDING')
                ->orderBy('id', 'desc')
                ->first();

            if ($latestPending && preg_match('/\[ORDER_ID:([^\]]+)\]/', $latestPending->deskripsi, $matches)) {
                $orderId = trim($matches[1]);
            }
        }

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada transaksi pending yang perlu diperiksa.',
                'balance' => $user->getUnifiedSaldo()
            ], 200);
        }

        // Keamanan Anti-IDOR: Pastikan order_id ini benar-benar milik user yang sedang login!
        $isOwnedByUser = WalletTransaction::where('user_id', $user->id)
            ->where('deskripsi', 'like', "%{$orderId}%")
            ->exists() || str_starts_with($orderId, 'TOPUP-' . $user->id . '-');

        if (!$isOwnedByUser) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak valid atau bukan milik akun Anda.',
                'balance' => $user->getUnifiedSaldo()
            ], 403);
        }

        // Cek apakah transaksi sudah pernah sukses sebelumnya (idempotency global)
        $alreadySuccess = WalletTransaction::where('tipe', 'IN')
            ->where('deskripsi', 'like', "%{$orderId}%")
            ->exists();

        if ($alreadySuccess) {
            return response()->json([
                'success'            => true,
                'status'             => 'success',
                'transaction_status' => 'settlement',
                'is_newly_settled'   => false,
                'message'            => 'Pembayaran telah berhasil diselesaikan sebelumnya.',
                'new_balance'        => $user->getUnifiedSaldo(),
                'saldo'              => $user->getUnifiedSaldo()
            ], 200);
        }

        $midtransService = new \App\Services\MidtransService();

        try {
            $statusData = $midtransService->getTransactionStatus($orderId);
            $transactionStatus = $statusData['transaction_status'] ?? '';
            $fraudStatus = $statusData['fraud_status'] ?? '';
            $grossAmount = (float) ($statusData['gross_amount'] ?? 0);

            $isPaid = false;
            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept' || empty($fraudStatus)) {
                    $isPaid = true;
                }
            } elseif ($transactionStatus === 'settlement') {
                $isPaid = true;
            }

            if ($isPaid && $grossAmount > 0) {
                $settleResult = $this->settleTopupTransaction($user, $orderId, $grossAmount, $request);

                return response()->json([
                    'success'            => true,
                    'status'             => 'success',
                    'transaction_status' => $transactionStatus,
                    'is_newly_settled'   => $settleResult['is_newly_settled'],
                    'message'            => $settleResult['message'],
                    'new_balance'        => $settleResult['new_balance'],
                    'saldo'              => $settleResult['new_balance']
                ], 200);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                WalletTransaction::where('user_id', $user->id)
                    ->where('tipe', 'PENDING')
                    ->where('deskripsi', 'like', "%{$orderId}%")
                    ->delete();

                return response()->json([
                    'success'            => false,
                    'status'             => $transactionStatus,
                    'transaction_status' => $transactionStatus,
                    'message'            => 'Status pembayaran Midtrans: ' . strtoupper($transactionStatus),
                    'balance'            => $user->getUnifiedSaldo(),
                    'saldo'              => $user->getUnifiedSaldo()
                ], 200);
            } else {
                return response()->json([
                    'success'            => false,
                    'status'             => $transactionStatus ?: 'pending',
                    'transaction_status' => $transactionStatus ?: 'pending',
                    'message'            => 'Pembayaran belum diselesaikan di Midtrans.',
                    'balance'            => $user->getUnifiedSaldo(),
                    'saldo'              => $user->getUnifiedSaldo()
                ], 200);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Check Midtrans Status Error ({$orderId}): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sedang menunggu konfirmasi dari sistem Midtrans: ' . $e->getMessage(),
                'balance' => $user->getUnifiedSaldo(),
                'saldo'   => $user->getUnifiedSaldo()
            ], 200);
        }
    }

    /**
     * Settle top-up transaction secara atomik untuk mencegah race condition (double crediting).
     * Jika terjadi duplikasi akibat eksekusi simultan sebelumnya, sistem otomatis mengoreksi kelebihan saldo.
     *
     * @return array ['settled' => bool, 'is_newly_settled' => bool, 'new_balance' => float, 'message' => string]
     */
    private function settleTopupTransaction(\App\Models\User $user, string $orderId, float $grossAmount, Request $request): array
    {
        $lockKey = "revaro_topup_lock_" . md5($orderId);
        $lock = Cache::lock($lockKey, 15);

        try {
            $lock->block(5);
        } catch (\Throwable $e) {}

        try {
            return DB::transaction(function () use ($user, $orderId, $grossAmount, $request) {
                // 1. Periksa apakah sudah ada catatan transaksi IN untuk order ID ini (dengan row-level lock)
                $existingRecords = WalletTransaction::where('user_id', $user->id)
                    ->where('tipe', 'IN')
                    ->where('deskripsi', 'like', "%{$orderId}%")
                    ->lockForUpdate()
                    ->get();

                // Jika terdeteksi lebih dari 1 transaksi sukses untuk order ID yang sama (duplikat ganda)
                if ($existingRecords->count() > 1) {
                    $excessCount = $existingRecords->count() - 1;
                    $excessAmount = $excessCount * $grossAmount;

                    // Hapus baris duplikat yang berlebih
                    for ($i = 1; $i < $existingRecords->count(); $i++) {
                        $existingRecords[$i]->delete();
                    }

                    // Koreksi saldo pengguna agar kembali ke nilai yang semestinya
                    $currentSaldo = $user->getUnifiedSaldo();
                    $correctedSaldo = max(0, $currentSaldo - $excessAmount);

                    if ($user->pelanggan) {
                        $user->pelanggan->saldo = $correctedSaldo;
                        $user->pelanggan->save();
                    }
                    if ($user->teknisi) {
                        $user->teknisi->saldo = $correctedSaldo;
                        $user->teknisi->save();
                    }
                    if ($user->toko) {
                        $user->toko->saldo = $correctedSaldo;
                        $user->toko->save();
                    }

                    return [
                        'settled'          => true,
                        'is_newly_settled' => false,
                        'new_balance'      => $correctedSaldo,
                        'message'          => 'Saldo telah disesuaikan dan dikoreksi dari pencatatan ganda.'
                    ];
                }

                if ($existingRecords->count() === 1) {
                    return [
                        'settled'          => true,
                        'is_newly_settled' => false,
                        'new_balance'      => $user->getUnifiedSaldo(),
                        'message'          => 'Pembayaran telah berhasil diselesaikan sebelumnya.'
                    ];
                }

                // 2. Transaksi baru: Hapus catatan PENDING lama
                WalletTransaction::where('user_id', $user->id)
                    ->where('deskripsi', 'like', "%{$orderId}%")
                    ->delete();

                // 3. Tambahkan saldo sekali saja
                $newBalance = $user->addUnifiedSaldo($grossAmount);

                // 4. Catat transaksi IN
                WalletTransaction::create([
                    'user_id'   => $user->id,
                    'tipe'      => 'IN',
                    'jumlah'    => $grossAmount,
                    'deskripsi' => "Top-up via Midtrans Berhasil [ORDER_ID:{$orderId}]",
                ]);

                \App\Models\Notifikasi::create([
                    'user_id' => $user->id,
                    'judul'   => 'Top-Up Saldo Berhasil',
                    'pesan'   => "Saldo Anda telah bertambah sebesar Rp " . number_format($grossAmount, 0, ',', '.') . " melalui pembayaran Midtrans.",
                    'dibaca'  => false,
                ]);

                ActivityLog::create([
                    'user_id'    => $user->id,
                    'aktivitas'  => "Top-up saldo Midtrans Rp " . number_format($grossAmount, 0, ',', '.') . " berhasil dikonfirmasi",
                    'ip_address' => $request->ip(),
                ]);

                return [
                    'settled'          => true,
                    'is_newly_settled' => true,
                    'new_balance'      => $newBalance,
                    'message'          => 'Pembayaran Midtrans berhasil dikonfirmasi! Saldo sebesar Rp ' . number_format($grossAmount, 0, ',', '.') . ' telah ditambahkan.'
                ];
            });
        } finally {
            try {
                $lock->release();
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Rekonsiliasi transaksi ganda jika pernah ada duplikasi akibat race condition terdahulu.
     */
    private function reconcileUserTransactions(\App\Models\User $user): void
    {
        try {
            $inTransactions = WalletTransaction::where('user_id', $user->id)
                ->where('tipe', 'IN')
                ->where('deskripsi', 'like', '%[ORDER_ID:%')
                ->get();

            $orderGroups = [];
            foreach ($inTransactions as $tx) {
                if (preg_match('/\[ORDER_ID:([^\]]+)\]/', $tx->deskripsi, $m)) {
                    $oid = trim($m[1]);
                    $orderGroups[$oid][] = $tx;
                }
            }

            foreach ($orderGroups as $oid => $txList) {
                if (count($txList) > 1) {
                    $excessCount = count($txList) - 1;
                    $deductAmount = 0;
                    for ($i = 1; $i < count($txList); $i++) {
                        $deductAmount += (float) $txList[$i]->jumlah;
                        $txList[$i]->delete();
                    }

                    if ($deductAmount > 0) {
                        $current = $user->getUnifiedSaldo();
                        $corrected = max(0, $current - $deductAmount);
                        if ($user->pelanggan) {
                            $user->pelanggan->saldo = $corrected;
                            $user->pelanggan->save();
                        }
                        if ($user->teknisi) {
                            $user->teknisi->saldo = $corrected;
                            $user->teknisi->save();
                        }
                        if ($user->toko) {
                            $user->toko->saldo = $corrected;
                            $user->toko->save();
                        }
                    }
                }
            }
        } catch (\Throwable $e) {}
    }

    /**
     * Endpoint kompatibilitas: Meneruskan ke checkStatus agar tidak 404 jika ada klien lama yang memanggil.
     */
    public function simulateSuccess(Request $request)
    {
        return $this->checkStatus($request);
    }
}

