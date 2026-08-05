<?php

namespace App\Policies;

use App\Models\Dispute;
use App\Models\User;

class DisputePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dispute $dispute): bool
    {
        // Strictly block Monitoring Kinerja from viewing individual Dispute records
        if ($user->isSuperAdminMonitoring()) {
            return false;
        }

        if ($user->isSuperAdminPemilik() || $user->isAdminResolusi()) {
            return true;
        }

        if ($user->isPelanggan() && $user->pelanggan) {
            return $dispute->order->pelanggan_id === $user->pelanggan->id;
        }

        if ($user->isTeknisi() && $user->teknisi) {
            return $dispute->order->teknisi_id === $user->teknisi->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dispute $dispute): bool
    {
        return $user->isAdminResolusi();
    }
}
