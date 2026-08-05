<?php

namespace App\Policies;

use App\Models\Sparepart;
use App\Models\User;

class SparepartPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sparepart $sparepart): bool
    {
        return true; // Any authenticated user can view spareparts
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Must be a Shop and status must be ACTIVE
        return $user->isToko() && $user->toko && $user->toko->status === 'ACTIVE';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sparepart $sparepart): bool
    {
        // Must be a Shop, status must be ACTIVE, and must own the sparepart
        return $user->isToko() && 
               $user->toko && 
               $user->toko->status === 'ACTIVE' && 
               $sparepart->toko_id === $user->toko->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sparepart $sparepart): bool
    {
        return $this->update($user, $sparepart);
    }
}
