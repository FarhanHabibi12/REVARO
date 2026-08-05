<?php

namespace App\Policies;

use App\Models\Transaksi;
use App\Models\User;

class TransaksiPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transaksi $transaksi): bool
    {
        // Strictly block Monitoring Kinerja from viewing individual transaction details
        if ($user->isSuperAdminMonitoring()) {
            return false;
        }

        if ($user->isSuperAdminPemilik()) {
            return true;
        }

        if ($user->isPelanggan() && $user->pelanggan) {
            return $transaksi->order->pelanggan_id === $user->pelanggan->id;
        }

        if ($user->isTeknisi() && $user->teknisi) {
            return $transaksi->order->teknisi_id === $user->teknisi->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Block all direct user creations - system only
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transaksi $transaksi): bool
    {
        // Block all direct user updates - system only
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transaksi $transaksi): bool
    {
        // Block all direct user deletions - system only
        return false;
    }
}
