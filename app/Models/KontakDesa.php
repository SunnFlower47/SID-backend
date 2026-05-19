<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasWilayahLabels;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class KontakDesa extends Model
{
    use HasWilayahLabels, LogsActivity;

    protected $fillable = [
        'nama',
        'jenis',
        'jabatan',
        'alamat',
        'rt_id',
        'rw_id',
        'dusun_id',
        'no_telepon',
        'no_hp',
        'email',
        'website',
        'facebook',
        'instagram',
        'youtube',
        'whatsapp',
        'jam_operasional',
        'deskripsi',
        'foto',
        'status_aktif',
        'urutan',
    ];

    protected $casts = [
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
        'status_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    // =========================================================
    // RELATIONS - WILAYAH MASTER
    // =========================================================

    public function rtMaster(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function rwMaster(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function dusunMaster(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id');
    }

    public function rt(): BelongsTo
    {
        return $this->rtMaster();
    }

    public function rw(): BelongsTo
    {
        return $this->rwMaster();
    }

    public function dusun(): BelongsTo
    {
        return $this->dusunMaster();
    }

    /**
     * Scope for Eager Loading Wilayah Master (High Performance)
     */
    public function scopeWithWilayah($query)
    {
        return $query->with(['rtMaster', 'rwMaster', 'dusunMaster']);
    }


    /**
     * Get the jenis label
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'kantor_desa' => 'Kantor Desa',
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'puskesmas' => 'Puskesmas',
            'posyandu' => 'Posyandu',
            'sekolah' => 'Sekolah',
            'masjid' => 'Masjid',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis] ?? $this->jenis;
    }

    /**
     * Get the full address
     */
    public function getAlamatLengkapAttribute()
    {
        return "{$this->alamat}, RT {$this->rt_label}/RW {$this->rw_label}, {$this->dusun_label}";
    }


    /**
     * Get the primary contact
     */
    public function getKontakUtamaAttribute()
    {
        if ($this->no_hp) return $this->no_hp;
        if ($this->no_telepon) return $this->no_telepon;
        if ($this->whatsapp) return $this->whatsapp;
        return null;
    }

    /**
     * Scope for active contacts
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope for specific jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope for ordering
     */
    public function scopeByOrder($query)
    {
        return $query->orderBy('urutan')->orderBy('jenis')->orderBy('nama');
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
