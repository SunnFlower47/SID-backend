<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastAnnouncement extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'broadcast_announcements';

    protected $fillable = [
        'title',
        'message',
        'type',
        'expires_at',
        'sender_name',
        'target_type',
        'target_tenant_ids',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'target_tenant_ids' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(CentralUser::class, 'created_by');
    }

    /**
     * Scope to only include active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to filter announcements targeted to a specific tenant.
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where(function ($q) use ($tenantId) {
            $q->where('target_type', 'all')
              ->orWhere(function ($sq) use ($tenantId) {
                  $sq->where('target_type', 'specific')
                     ->whereJsonContains('target_tenant_ids', $tenantId);
              });
        });
    }
}
