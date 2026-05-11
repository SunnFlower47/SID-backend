<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

use App\Traits\HasWilayahLabels;

class Umkm extends Model
{
    use SoftDeletes, LogsActivity, HasWilayahLabels;

    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'nik_pemilik',
        'alamat_usaha',
        'rt_id',
        'rw_id',
        'dusun_id',
        'no_telepon',
        'email',
        'jenis_usaha',
        'deskripsi_usaha',
        'modal_awal',
        'omset_bulanan',
        'jumlah_karyawan',
        'status_usaha',
        'tanggal_berdiri',
        'produk_unggulan',
        'foto_usaha',
        'latitude',
        'longitude',
        'is_unggulan',
        'is_verified',
    ];

    protected $casts = [
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
        'produk_unggulan' => 'array',
        'foto_usaha' => 'array',
        'modal_awal' => 'decimal:2',
        'omset_bulanan' => 'decimal:2',
        'tanggal_berdiri' => 'date',
        'is_unggulan' => 'boolean',
        'is_verified' => 'boolean',
    ];

    protected $appends = ['rt_label', 'rw_label', 'dusun_label'];

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


    // Accessors
    public function getJenisUsahaLabelAttribute()
    {
        $labels = [
            'makanan' => 'Makanan',
            'minuman' => 'Minuman',
            'kerajinan' => 'Kerajinan',
            'jasa' => 'Jasa',
            'perdagangan' => 'Perdagangan',
            'pertanian' => 'Pertanian',
            'peternakan' => 'Peternakan',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis_usaha] ?? $this->jenis_usaha;
    }

    public function getStatusUsahaLabelAttribute()
    {
        $labels = [
            'aktif' => 'Aktif',
            'tutup' => 'Tutup',
            'pindah' => 'Pindah',
        ];

        return $labels[$this->status_usaha] ?? $this->status_usaha;
    }

    public function getAlamatLengkapAttribute()
    {
        return "{$this->alamat_usaha}, RT {$this->rt_label}/RW {$this->rw_label}, {$this->dusun_label}";
    }


    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status_usaha', 'aktif');
    }

    public function scopeUnggulan($query)
    {
        return $query->where('is_unggulan', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeJenisUsaha($query, $jenis)
    {
        return $query->where('jenis_usaha', $jenis);
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_usaha', 'nama_pemilik', 'nik_pemilik', 'alamat_usaha', 'rt_id', 'rw_id', 'dusun_id', 'no_telepon', 'jenis_usaha', 'status_usaha', 'jumlah_karyawan', 'is_unggulan', 'is_verified'])

            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
