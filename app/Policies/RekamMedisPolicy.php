<?php

namespace App\Policies;

use App\Models\RekamMedis;
use App\Models\User;

class RekamMedisPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin dan Dokter bisa melihat list
        return $user->hasAnyRole(['admin', 'dokter']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RekamMedis $rekamMedis): bool
    {
        // Admin bisa melihat semua
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dokter hanya bisa melihat rekam medis yang dia buat
        if ($user->hasRole('dokter')) {
            return $rekamMedis->dokter_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya dokter yang bisa membuat rekam medis
        return $user->hasRole('dokter');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RekamMedis $rekamMedis): bool
    {
        // Hanya dokter yang membuat rekam medis tersebut yang bisa edit
        return $user->hasRole('dokter') && $rekamMedis->dokter_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RekamMedis $rekamMedis): bool
    {
        // Hanya dokter yang membuat rekam medis tersebut yang bisa hapus
        return $user->hasRole('dokter') && $rekamMedis->dokter_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RekamMedis $rekamMedis): bool
    {
        return $user->hasRole('dokter') && $rekamMedis->dokter_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RekamMedis $rekamMedis): bool
    {
        return $user->hasRole('dokter') && $rekamMedis->dokter_id === $user->id;
    }
}
