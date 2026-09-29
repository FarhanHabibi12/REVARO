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
        $balance = 0.00;

        if ($user->isPelanggan() && $user->pelanggan) {
            $balance = $user->pelanggan->saldo;
        } elseif ($user->isTeknisi() && $user->teknisi) {
            $balance = $user->teknisi->saldo;
        } elseif ($user->isToko() && $user->toko) {
            $balance = $user->toko->saldo;
        }

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'balance' => $balance,
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
                'new_balance' => $profile ? $profile->fresh()->saldo : 0,
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

        $profile = null;
        if ($user->isPelanggan() && $user->pelanggan) {
            $profile = $user->pelanggan;
        } elseif ($user->isTeknisi() && $user->teknisi) {
            $profile = $user->teknisi;
        } elseif ($user->isToko() && $user->toko) {
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

        return DB::transaction(function () use ($user, $amount, $profile, $request) {
            $profile->increment('saldo', $amount);

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
                'new_balance' => $profile->fresh()->saldo
            ]);
        });
    }
}
