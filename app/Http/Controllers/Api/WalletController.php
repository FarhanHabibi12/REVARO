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
     * Request withdrawal with full bank details.
     */
    public function withdraw(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'bank_name'      => 'required|string|max:50',
            'account_name'   => 'required|string|max:100',
            'account_number' => 'required|string|max:30',
            'amount'         => 'required|numeric|min:50000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amount = $request->amount;

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

            // Record to withdrawals table for admin processing
            DB::table('withdrawals')->insert([
                'user_id'        => $user->id,
                'bank_name'      => $request->bank_name,
                'account_name'   => $request->account_name,
                'account_number' => $request->account_number,
                'amount'         => $amount,
                'status'         => 'PENDING',
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Log wallet ledger
            WalletTransaction::create([
                'user_id'   => $user->id,
                'tipe'      => 'OUT',
                'jumlah'    => $amount,
                'deskripsi' => "Penarikan dana ke {$request->bank_name} a.n. {$request->account_name} ({$request->account_number})",
            ]);

            ActivityLog::create([
                'user_id'    => $user->id,
                'aktivitas'  => "Penarikan dana Rp " . number_format($amount, 0, ',', '.') . " ke {$request->bank_name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'message'     => 'Permintaan penarikan dana berhasil diajukan. Admin akan memproses dalam 1x24 jam.',
                'new_balance' => $profile->fresh()->saldo
            ]);
        });
    }

    /**
     * Top-up balance.
     */
    public function topup(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:10000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $amount  = $request->jumlah;
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
