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
     * Top-up balance with input sanitization (strips "Rp", dots, spaces).
     */
    public function topup(\App\Http\Requests\TopUpRequest $request)
    {
        $user = $request->user();

        // Amount has been sanitized to pure integer in TopUpRequest
        $amount = (float) $request->input('amount', $request->input('jumlah'));

        if ($amount < 10000) {
            return response()->json(['message' => 'Nominal top up minimal adalah Rp 10.000.'], 422);
        }

        $roleContext = $request->input('role', $request->query('role'));
        $roleName = $user->role ? $user->role->nama : '';

        $profile = null;
        if ($roleContext === 'teknisi' && $user->teknisi) {
            $profile = $user->teknisi;
        } elseif ($roleContext === 'toko' && $user->toko) {
            $profile = $user->toko;
        } elseif ($roleContext === 'pelanggan' && $user->pelanggan) {
            $profile = $user->pelanggan;
        } elseif ($roleName === 'Teknisi' && $user->teknisi && !$roleContext) {
            $profile = $user->teknisi;
        } elseif ($roleName === 'Toko' && $user->toko && !$roleContext) {
            $profile = $user->toko;
        } elseif ($user->pelanggan) {
            $profile = $user->pelanggan;
        } elseif ($user->teknisi) {
            $profile = $user->teknisi;
        } elseif ($user->toko) {
            $profile = $user->toko;
        } else {
            // Auto-create Pelanggan profile if missing
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

        return DB::transaction(function () use ($user, $amount, $request) {
            $newBalance = $user->addUnifiedSaldo($amount);

            WalletTransaction::create([
                'user_id'   => $user->id,
                'tipe'      => 'IN',
                'jumlah'    => $amount,
                'deskripsi' => 'Top-up saldo dompet REVARO',
            ]);

            ActivityLog::create([
                'user_id'    => $user->id,
                'aktivitas'  => "Top-up saldo Rp " . number_format($amount, 0, ',', '.'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message'     => 'Top-up saldo berhasil.',
                'new_balance' => $newBalance,
                'saldo'       => $newBalance,
            ]);
        });
    }
}
