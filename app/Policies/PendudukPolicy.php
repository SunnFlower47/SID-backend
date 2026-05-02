<?php

namespace App\Policies;

use App\Models\Penduduk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PendudukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Penduduk $penduduk): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Penduduk $penduduk): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Penduduk $penduduk): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can export models.
     */
    public function export(User $user): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Penduduk $penduduk): bool
    {
        return $user->can('kependudukan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Penduduk $penduduk): bool
    {
        return $user->can('kependudukan');
    }
}
