<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    
    public function penduduks(): HasMany
    {
        return $this->hasMany(Penduduk::class, 'rt_id');
    }

    public function kartuKeluargas(): HasMany
    {
        return $this->hasMany(KartuKeluarga::class, 'rt_id');
    }

    public function umkms(): HasMany
    {
        return $this->hasMany(Umkm::class, 'rt_id');
    }

    public function fasilitasDesas(): HasMany
    {
        return $this->hasMany(FasilitasDesa::class, 'rt_id');
    }

    public function strukturDesas(): HasMany
    {
        return $this->hasMany(StrukturDesa::class, 'rt_id');
    }

    public function getRwLabelAttribute()
    {
        return $this->rw;
    }

    public function getDusunLabelAttribute()
    {
        return $this->dusun;
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
