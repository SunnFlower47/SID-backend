<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Notifications\CustomPasswordResetNotification;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity;

    /**
     * Dynamically use the tenant connection if tenancy is initialized.
     * This prevents errors when the User model is queried before tenancy
     * is booted (e.g. by guest middleware checking old sessions).
     */
    public function getConnectionName()
    {
        if (function_exists('tenancy') && tenancy()->initialized) {
            return 'tenant';
        }
        return config('database.default');
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (function_exists('tenant') && tenant() !== null) {
                // Bypass when running seeds / console setup commands
                if (app()->runningInConsole() && !app()->runningUnitTests()) {
                    return;
                }

                $allocation = \App\Models\Central\TenantAllocation::where('tenant_id', tenant('id'))->first();
                if ($allocation) {
                    $currentUserCount = static::count();
                    if ($currentUserCount >= $allocation->max_users) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'email' => ["Batas maksimal pengguna ({$allocation->max_users}) untuk desa ini telah tercapai. Silakan hubungi Diskominfo untuk menambah kuota."]
                        ]);
                    }
                }
            }
        });
        
        static::created(function ($user) {
            // Log user creation
            \App\Models\Central\TenantActivityLog::log('user_created', "User baru dengan email {$user->email} berhasil ditambahkan.");
        });

        static::deleted(function ($user) {
            // Log user deletion
            \App\Models\Central\TenantActivityLog::log('user_deleted', "User dengan email {$user->email} telah dihapus.");
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordResetNotification($token));
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'email_verified_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
