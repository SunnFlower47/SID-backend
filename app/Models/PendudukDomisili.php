<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class PendudukDomisili extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'nik',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'asal_daerah',
        'alamat_asal',
        'rt_id',
        'rw_id',
        'dusun_id',
        'alamat_tinggal',
        'keperluan_domisili',
        'tanggal_masuk',
        'tanggal_berlaku',
        'status',
        'status_perkawinan',
        'kewarganegaraan',
        'perpanjangan_ke',
        'nomor_surat',
        'surat_pengajuan_id',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_lahir'   => 'date',
        'tanggal_masuk'   => 'date',
        'tanggal_berlaku' => 'date',
        'perpanjangan_ke' => 'integer',
    ];

    protected $appends = [
        'sisa_hari_berlaku',
        'rt_label',
        'rw_label',
        'dusun_label',
    ];

    // =========================================================
    // RELATIONS
    // =========================================================

    public function rt(): BelongsTo
    {
        return $this->belongsTo(Rt::class);
    }

    public function rw(): BelongsTo
    {
        return $this->belongsTo(Rw::class);
    }

    public function dusun(): BelongsTo
    {
        return $this->belongsTo(Dusun::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    // ACCESSORS
    // =========================================================

    public function getSisaHariBerlakuAttribute(): int
    {
        if (!$this->tanggal_berlaku) return 0;
        return max(0, now()->startOfDay()->diffInDays($this->tanggal_berlaku, false));
    }

    public function getRtLabelAttribute(): string
    {
        return $this->rt ? $this->rt->kode : '-';
    }

    public function getRwLabelAttribute(): string
    {
        return $this->rw ? $this->rw->kode : '-';
    }

    public function getDusunLabelAttribute(): string
    {
        return $this->dusun ? $this->dusun->nama : '-';
    }

    // =========================================================
    // SCOPES
    // =========================================================

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeDicabut($query)
    {
        return $query->where('status', 'dicabut');
    }

    public function scopeWarningExpiry($query, int $days = 30)
    {
        return $query->where('status', 'aktif')
                     ->whereBetween('tanggal_berlaku', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('asal_daerah', 'like', "%{$search}%");
            });
        });

        $query->when($filters['status'] ?? null, fn($q, $v) => $q->where('status', $v));
        $query->when($filters['rt_id'] ?? null, fn($q, $v) => $q->where('rt_id', $v));
        $query->when($filters['rw_id'] ?? null, fn($q, $v) => $q->where('rw_id', $v));
        $query->when($filters['dusun_id'] ?? null, fn($q, $v) => $q->where('dusun_id', $v));
        $query->when($filters['asal_daerah'] ?? null, fn($q, $v) => $q->where('asal_daerah', 'like', "%{$v}%"));
        $query->when($filters['keperluan_domisili'] ?? null, fn($q, $v) => $q->where('keperluan_domisili', $v));
    }

    // =========================================================
    // ACTIVITY LOG
    // =========================================================

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nik', 'nama', 'status', 'tanggal_berlaku', 'perpanjangan_ke', 'nomor_surat', 'catatan'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
