<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantActivityLog extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'tenant_activity_logs';

    protected $fillable = [
        'tenant_id',
        'action',
        'description',
        'performed_by',
    ];

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    public function performer()
    {
        return $this->belongsTo(CentralUser::class, 'performed_by');
    }

    /**
     * Helper to easily record activity logs from anywhere.
     */
    public static function log(string $action, ?string $description = null)
    {
        $tenantId = null;
        if (function_exists('tenant') && tenant() !== null) {
            $tenantId = tenant('id');
        }

        // If not inside tenant context but we can guess or it is central,
        // we can set tenant_id. We only save if tenant_id is available.
        if ($tenantId) {
            // Check central auth
            $performedBy = auth('landlord')->id();

            self::create([
                'tenant_id' => $tenantId,
                'action' => $action,
                'description' => $description,
                'performed_by' => $performedBy,
            ]);
        }
    }
}
