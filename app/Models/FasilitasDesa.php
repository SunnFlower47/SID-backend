<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasWilayahLabels;

class FasilitasDesa extends Model
{
    use HasWilayahLabels;

    protected $fillable = [
        'nama',
        'jenis',
        'alamat',
        'rt_id',
        'rw_id',
        'dusun_id',
        'latitude',
        'longitude',
        'deskripsi',
        'kontak',
        'jam_operasional',
        'status_aktif',
        'foto',
    ];

    protected $casts = [
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status_aktif' => 'boolean',
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
            'sekolah' => 'Sekolah',
            'posyandu' => 'Posyandu',
            'masjid' => 'Masjid',
            'gereja' => 'Gereja',
            'puskesmas' => 'Puskesmas',
            'pos_ronda' => 'Pos Ronda',
            'balai_desa' => 'Balai Desa',
            'lapangan' => 'Lapangan',
            'pasar' => 'Pasar',
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

}
