<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->isSuperAdminPemilik() || $user->isSuperAdminMonitoring() || $user->isAdminResolusi()) {
            return true;
        }

        if ($user->isPelanggan() && $user->pelanggan) {
            return $order->pelanggan_id === $user->pelanggan->id;
        }

        if ($user->isTeknisi() && $user->teknisi) {
            return $order->teknisi_id === $user->teknisi->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        if ($user->isAdminResolusi()) {
            return true;
        }

        if ($user->isTeknisi() && $user->teknisi) {
            return $order->teknisi_id === $user->teknisi->id;
        }

        return false;
    }
}
