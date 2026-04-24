<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rt extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'rts';

    protected $fillable = [
        'kode',
        'nama',
        'rw_id',
        'dusun_id',
        'is_active',
        'is_auto_generated',
        'needs_review',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_auto_generated' => 'boolean',
        'needs_review' => 'boolean',
    ];

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('master_wilayah')
            ->logOnly(['kode', 'nama', 'rw_id', 'dusun_id', 'is_active', 'is_auto_generated', 'needs_review'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
