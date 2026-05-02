<?php

namespace App\Policies;

use App\Models\Mutasi;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MutasiPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('kependudukan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Mutasi $mutasi): bool
    {
        return $user->hasPermissionTo('kependudukan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('kependudukan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Mutasi $mutasi): bool
    {
        return $user->hasPermissionTo('kependudukan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Mutasi $mutasi): bool
    {
        return $user->hasPermissionTo('kependudukan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Mutasi $mutasi): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Mutasi $mutasi): bool
    {
        return false;
    }
}
