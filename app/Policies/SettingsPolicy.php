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
        return $user->hasPermissionTo('settings.view');
    }

    /**
     * Determine whether the user can manage users.
     */
    public function usersManage(User $user)
    {
        return $user->hasPermissionTo('settings.users.manage');
    }

    /**
     * Determine whether the user can create users.
     */
    public function usersCreate(User $user)
    {
        return $user->hasPermissionTo('settings.users.create');
    }

    /**
     * Determine whether the user can edit users.
     */
    public function usersEdit(User $user)
    {
        return $user->hasPermissionTo('settings.users.edit');
    }

    /**
     * Determine whether the user can delete users.
     */
    public function usersDelete(User $user)
    {
        return $user->hasPermissionTo('settings.users.delete');
    }

    /**
     * Determine whether the user can manage roles.
     */
    public function rolesManage(User $user)
    {
        return $user->hasPermissionTo('settings.roles.manage');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function rolesCreate(User $user)
    {
        return $user->hasPermissionTo('settings.roles.create');
    }

    /**
     * Determine whether the user can edit roles.
     */
    public function rolesEdit(User $user)
    {
        return $user->hasPermissionTo('settings.roles.edit');
    }

    /**
     * Determine whether the user can delete roles.
     */
    public function rolesDelete(User $user)
    {
        return $user->hasPermissionTo('settings.roles.delete');
    }
}

