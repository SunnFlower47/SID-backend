<?php

namespace App\Models\Central;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CentralUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Tentukan connection eksplisit ke central DB.
     * Walaupun di-query dari dalam tenant, dia akan tetap baca central DB.
     */
    protected $connection = 'mysql';

    protected $table = 'central_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'failed_login_attempts',
        'locked_until',
        'two_factor_secret',
        'two_factor_enabled',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at'         => 'datetime',
        'locked_until'              => 'datetime',
        'password'                  => 'hashed',
        'failed_login_attempts'     => 'integer',
        'two_factor_enabled'        => 'boolean',
        'two_factor_secret'         => 'encrypted',
        'two_factor_recovery_codes' => 'encrypted:array',
        'two_factor_confirmed_at'   => 'datetime',
    ];
}
