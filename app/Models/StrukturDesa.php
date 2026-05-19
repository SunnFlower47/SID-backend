<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasWilayahLabels;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class StrukturDesa extends Model
{
    use HasWilayahLabels, LogsActivity;

    protected $fillable = [
        'nama',
        'jabatan',
        'kategori',
        'nik',
        'no_hp',
        'email',
        'alamat',
        'rt_id',
        'rw_id',
        'dusun_id',
        'tugas_wewenang',
        'tanggal_pengangkatan',
        'tanggal_berakhir',
        'foto',
        'status_aktif',
        'urutan',
    ];

    protected $casts = [
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
        'tanggal_pengangkatan' => 'date',
        'tanggal_berakhir' => 'date',
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

    /**
     * Scope for Eager Loading Wilayah Master (High Performance)
     */
    public function scopeWithWilayah($query)
    {
        return $query->with(['rtMaster', 'rwMaster', 'dusunMaster']);
    }


    /**
     * Get the kategori label
     */
    public function getKategoriLabelAttribute()
    {
        $labels = [
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kaur_keuangan' => 'KAUR Keuangan',
            'kaur_perencanaan' => 'KAUR Perencanaan',
            'kaur_umum' => 'KAUR Umum',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'staf_kaur' => 'Staf KAUR',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->kategori] ?? $this->kategori;
    }

    /**
     * Get the full address
     */
    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->alamat ?? '';
        return "{$alamat}, RT {$this->rt_label}/RW {$this->rw_label}, {$this->dusun_label}";
    }


    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status_aktif ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Scope for active positions
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope for ordering by hierarchy
     */
    public function scopeByHierarchy($query)
    {
        return $query->orderBy('urutan')->orderBy('kategori')->orderBy('nama');
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
