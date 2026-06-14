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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
