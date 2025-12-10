<?php

namespace App\Policies;

use App\Models\Pasien;
use App\Models\User;

class PasienPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Hanya Admin yang bisa melihat list pasien
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pasien $pasien): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pasien $pasien): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pasien $pasien): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Pasien $pasien): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Pasien $pasien): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Bulk actions
     */
    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function restoreAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
