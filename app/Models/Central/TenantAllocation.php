<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantAllocation extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id',
        'max_users',
        'storage_limit_mb',
        'is_active',
        'subscription_ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subscription_ends_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Calculate current storage usage of the tenant in Megabytes.
     */
    public function getStorageUsedMb(): float
    {
        $tenant = \App\Models\Tenant::find($this->tenant_id);
        if (!$tenant) {
            return 0.0;
        }

        $totalBytes = 0;
        $tenant->run(function () use (&$totalBytes) {
            try {
                $disk = \Illuminate\Support\Facades\Storage::disk('s3');
                $files = $disk->allFiles();
                foreach ($files as $file) {
                    try {
                        $totalBytes += $disk->size($file);
                    } catch (\Exception $ex) {
                        // Ignore files that are deleted or inaccessible
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to calculate storage for tenant: " . $e->getMessage());
            }
        });

        return round($totalBytes / (1024 * 1024), 2);
    }
}
