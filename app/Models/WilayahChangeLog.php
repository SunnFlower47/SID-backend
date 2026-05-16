<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WilayahChangeLog extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'user_id',
        'preview_token',
        'before_payload',
        'after_payload',
        'backup_payload',
        'affected_count',
        'status',
        'applied_at',
        'rolled_back_at',
        'rolled_back_by',
    ];

    protected $casts = [
        'before_payload' => 'array',
        'after_payload' => 'array',
        'backup_payload' => 'array',
        'applied_at' => 'datetime',
        'rolled_back_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function operatorRollback()
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }
}
