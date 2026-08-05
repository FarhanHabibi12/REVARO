<?php

namespace App\Policies;

use App\Models\PendaftaranVerifikasi;
use App\Models\User;

class PendaftaranVerifikasiPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PendaftaranVerifikasi $verification): bool
    {
        if ($user->isSuperAdminPemilik() || $user->isSuperAdminMonitoring() || $user->isAdminVerifikator()) {
            return true;
        }

        if ($user->isTeknisi() && $user->teknisi) {
            return $verification->teknisi_id === $user->teknisi->id;
        }

        if ($user->isToko() && $user->toko) {
            return $verification->toko_id === $user->toko->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdminVerifikator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PendaftaranVerifikasi $verification): bool
    {
        return $user->isAdminVerifikator();
    }
}
