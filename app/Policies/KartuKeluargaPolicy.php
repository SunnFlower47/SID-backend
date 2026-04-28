<?php

namespace App\Policies;

use App\Models\KartuKeluarga;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class KartuKeluargaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('kartu-keluarga.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KartuKeluarga $kartuKeluarga = null): bool
    {
        return $user->can('kartu-keluarga.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('kartu-keluarga.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KartuKeluarga $kartuKeluarga = null): bool
    {
        return $user->can('kartu-keluarga.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KartuKeluarga $kartuKeluarga = null): bool
    {
        return $user->can('kartu-keluarga.delete');
    }
}
