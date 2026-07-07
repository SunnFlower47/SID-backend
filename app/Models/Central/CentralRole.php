<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CentralRole extends Model
{
    /**
     * Tentukan connection eksplisit ke central DB.
     */
    protected $connection = 'mysql';

    protected $table = 'central_roles';

    protected $fillable = [
        'name',
        'display_name',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::saved(function ($role) {
            Cache::forget("central_role_perms_{$role->name}");
        });

        static::deleted(function ($role) {
            Cache::forget("central_role_perms_{$role->name}");
        });
    }
}
