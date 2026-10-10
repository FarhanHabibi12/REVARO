<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
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
            return DB::transaction(function () use ($user, $grossAmount, $orderId, $request) {
                // Hapus catatan PENDING lama untuk order ini jika ada
                WalletTransaction::where('user_id', $user->id)
                    ->where('deskripsi', 'like', "%{$orderId}%")
                    ->delete();

                // Tambahkan saldo pengguna secara aman dengan pessimistic lock
                $newBalance = $user->addUnifiedSaldo((float) $grossAmount);

                WalletTransaction::create([
                    'user_id'   => $user->id,
                    'tipe'      => 'IN',
                    'jumlah'    => (float) $grossAmount,
                    'deskripsi' => "Top-up via Midtrans Berhasil [ORDER_ID:{$orderId}]",
                ]);

                \App\Models\Notifikasi::create([
                    'user_id' => $user->id,
                    'judul'   => 'Top-Up Saldo Berhasil',
                    'pesan'   => "Saldo Anda telah bertambah sebesar Rp " . number_format((float) $grossAmount, 0, ',', '.') . " melalui pembayaran Midtrans.",
                    'dibaca'  => false,
                ]);

                ActivityLog::create([
                    'user_id'    => $user->id,
                    'aktivitas'  => "Top-up saldo Midtrans Rp " . number_format((float) $grossAmount, 0, ',', '.') . " berhasil",
                    'ip_address' => $request->ip(),
                ]);

                return response()->json(['message' => 'Top-up settled successfully.', 'new_balance' => $newBalance], 200);
            });
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

        // Cek apakah transaksi sudah pernah sukses sebelumnya (idempotency)
        $alreadySuccess = WalletTransaction::where('user_id', $user->id)
            ->where('tipe', 'IN')
            ->where('deskripsi', 'like', "%{$orderId}%")
            ->exists();

        if ($alreadySuccess) {
            return response()->json([
                'success'     => true,
                'status'      => 'settlement',
                'message'     => 'Pembayaran telah berhasil diselesaikan sebelumnya.',
                'new_balance' => $user->getUnifiedSaldo()
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
                $newBalance = DB::transaction(function () use ($user, $grossAmount, $orderId, $request) {
                    // Hapus record pending
                    WalletTransaction::where('user_id', $user->id)
                        ->where('deskripsi', 'like', "%{$orderId}%")
                        ->delete();

                    // Tambahkan saldo
                    $newBal = $user->addUnifiedSaldo($grossAmount);

                    // Buat catatan transaksi sukses
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

                    return $newBal;
                });

                return response()->json([
                    'success'     => true,
                    'status'      => $transactionStatus,
                    'message'     => 'Pembayaran Midtrans berhasil dikonfirmasi! Saldo sebesar Rp ' . number_format($grossAmount, 0, ',', '.') . ' telah ditambahkan ke dompet Anda.',
                    'new_balance' => $newBalance
                ], 200);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                WalletTransaction::where('user_id', $user->id)
                    ->where('tipe', 'PENDING')
                    ->where('deskripsi', 'like', "%{$orderId}%")
                    ->delete();

                return response()->json([
                    'success' => false,
                    'status'  => $transactionStatus,
                    'message' => 'Status pembayaran Midtrans: ' . strtoupper($transactionStatus),
                    'balance' => $user->getUnifiedSaldo()
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'status'  => $transactionStatus ?: 'pending',
                    'message' => 'Pembayaran belum diselesaikan di Midtrans.',
                    'balance' => $user->getUnifiedSaldo()
                ], 200);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Check Midtrans Status Error ({$orderId}): " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sedang menunggu konfirmasi dari sistem Midtrans.',
                'balance' => $user->getUnifiedSaldo()
            ], 200);
        }
    }

    /**
     * Endpoint kompatibilitas: Meneruskan ke checkStatus agar tidak 404 jika ada klien lama yang memanggil.
     */
    public function simulateSuccess(Request $request)
    {
        return $this->checkStatus($request);
    }
}

