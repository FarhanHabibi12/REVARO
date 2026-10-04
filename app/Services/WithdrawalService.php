<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WithdrawalService
{
    /**
     * Process a withdrawal request with DB transaction and pessimistic locking.
     */
    public function createWithdrawal(User $user, array $data): Withdrawal
    {
        return DB::transaction(function () use ($user, $data) {
            $amount = (float) $data['amount'];

            if ($amount <= 0) {
                throw new InvalidArgumentException('Nominal penarikan harus lebih dari 0.');
            }

            $currentSaldo = $user->getUnifiedSaldo();
            if ($currentSaldo < $amount) {
                throw new InvalidArgumentException('Saldo Anda tidak mencukupi untuk melakukan penarikan dana ini.');
            }

            // Deduct balance across unified user wallet
            $user->deductUnifiedSaldo($amount);

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_holder_name'] ?? $data['account_name'] ?? $user->nama,
                'account_number' => $data['account_number'],
                'amount' => $amount,
                'status' => 'PENDING',
            ]);

            // Log activity
            SecurityLoggerService::log(
                event: 'WITHDRAWAL_REQUESTED',
                details: "Withdrawal of Rp " . number_format($amount, 0, ',', '.') . " requested to {$data['bank_name']} ({$data['account_number']})",
                userId: $user->id
            );

            return $withdrawal;
        });
    }
}
