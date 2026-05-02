<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view settings.
     */
    public function view(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can manage users.
     */
    public function usersManage(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can create users.
     */
    public function usersCreate(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can edit users.
     */
    public function usersEdit(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can delete users.
     */
    public function usersDelete(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function rolesManage(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function rolesCreate(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can edit roles.
     */
    public function rolesEdit(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }

    /**
     * Determine whether the user can delete roles.
     */
    public function rolesDelete(User $user)
    {
        return $user->hasPermissionTo('admin_sistem');
    }
}

