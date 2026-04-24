<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Dusun extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'kode',
        'nama',
        'is_active',
        'is_auto_generated',
        'needs_review',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_auto_generated' => 'boolean',
        'needs_review' => 'boolean',
    ];

    public function rts(): HasMany
    {
        return $this->hasMany(Rt::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('master_wilayah')
            ->logOnly(['kode', 'nama', 'is_active', 'is_auto_generated', 'needs_review'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
