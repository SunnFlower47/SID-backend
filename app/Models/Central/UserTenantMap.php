<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Tenant;

class UserTenantMap extends Model
{
    /**
     * Harus selalu connect ke mysql (central DB)
     */
    protected $connection = 'mysql';

    protected $table = 'user_tenant_map';

    protected $fillable = [
        'email',
        'tenant_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
