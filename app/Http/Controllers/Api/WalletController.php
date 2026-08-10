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
     * Request withdrawal.
     */
    public function withdraw(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:10000',
            'bank' => 'required|string|max:50',
            'no_rekening' => 'required|string|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amount = $request->jumlah;

        return DB::transaction(function () use ($user, $amount, $request) {
            $profile = null;

            if ($user->isPelanggan() && $user->pelanggan) {
                $profile = $user->pelanggan;
            } elseif ($user->isTeknisi() && $user->teknisi) {
                $profile = $user->teknisi;
            } elseif ($user->isToko() && $user->toko) {
                $profile = $user->toko;
            }

            if (!$profile) {
                return response()->json(['message' => 'Profil tidak ditemukan.'], 404);
            }

            if ($profile->saldo < $amount) {
                return response()->json(['message' => 'Saldo Anda tidak mencukupi.'], 400);
            }

            // Deduct balance
            $profile->decrement('saldo', $amount);

            // Log ledger
            WalletTransaction::create([
                'user_id' => $user->id,
                'tipe' => 'OUT',
                'jumlah' => $amount,
                'deskripsi' => "Penarikan dana (Withdrawal) ke rekening {$request->bank} {$request->no_rekening}",
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => "Melakukan penarikan dana sebesar Rp " . number_format($amount, 0, ',', '.'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Penarikan dana berhasil diajukan.',
                'new_balance' => $profile->fresh()->saldo
            ]);
        });
    }

    /**
     * Top-up balance (Customer only).
     */
    public function topup(Request $request)
    {
        $user = $request->user();

        if (!$user->isPelanggan() || !$user->pelanggan) {
            return response()->json(['message' => 'Hanya Pelanggan yang dapat melakukan topup.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amount = $request->jumlah;
        $pelanggan = $user->pelanggan;

        return DB::transaction(function () use ($user, $amount, $pelanggan, $request) {
            $pelanggan->increment('saldo', $amount);

            // Log ledger
            WalletTransaction::create([
                'user_id' => $user->id,
                'tipe' => 'IN',
                'jumlah' => $amount,
                'deskripsi' => 'Top-up saldo dompet REVARO',
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'aktivitas' => "Melakukan top-up saldo sebesar Rp " . number_format($amount, 0, ',', '.'),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message' => 'Top-up saldo berhasil dilakukan.',
                'new_balance' => $pelanggan->fresh()->saldo
            ]);
        });
    }
}
