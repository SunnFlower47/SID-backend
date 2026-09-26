<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Central\UserTenantMap;

class TenantUserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Hanya jalankan jika kita sedang berada dalam konteks Tenant
        if (tenancy()->initialized) {
            UserTenantMap::create([
                'email' => $user->email,
                'tenant_id' => tenant('id'),
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if (tenancy()->initialized && $user->wasChanged('email')) {
            // Update email lama ke email baru di central DB
            $map = UserTenantMap::where('email', $user->getOriginal('email'))
                                ->where('tenant_id', tenant('id'))
                                ->first();
            
            if ($map) {
                $map->update(['email' => $user->email]);
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if (tenancy()->initialized) {
            UserTenantMap::where('email', $user->email)
                         ->where('tenant_id', tenant('id'))
                         ->delete();
        }
    }
}
