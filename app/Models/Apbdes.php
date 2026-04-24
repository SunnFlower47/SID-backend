<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Apbdes extends Model
{
    use LogsActivity;

    protected $fillable = [
        'tahun',
        'jenis',
        'sumber_dana',
        'kode_rekening',
        'nama_rekening',
        'anggaran',
        'realisasi',
        'sisa_anggaran',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'anggaran' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'sisa_anggaran' => 'decimal:2',
    ];

    /**
     * Get the jenis label
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'pendapatan' => 'Pendapatan',
            'belanja' => 'Belanja',
            'pembiayaan' => 'Pembiayaan',
        ];

        return $labels[$this->jenis] ?? $this->jenis;
    }

    /**
     * Get the sumber dana label
     */
    public function getSumberDanaLabelAttribute()
    {
        $labels = [
            'dana_desa_ad' => 'Dana Desa - Alokasi Dasar (AD)',
            'dana_desa_af' => 'Dana Desa - Alokasi Formula (AF)',
            'dana_desa_ak' => 'Dana Desa - Alokasi Kinerja (AK)',
            'dau' => 'Dana Alokasi Umum (DAU)',
            'dak' => 'Dana Alokasi Khusus (DAK)',
            'dbh' => 'Dana Bagi Hasil (DBH)',
            'did' => 'Dana Insentif Daerah (DID)',
            'pad' => 'Pendapatan Asli Desa (PAD)',
        ];

        return $labels[$this->sumber_dana] ?? $this->sumber_dana;
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => 'Draft',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get the status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get the percentage of realization
     */
    public function getPersentaseRealisasiAttribute()
    {
        if ($this->anggaran == 0) return 0;
        return round(($this->realisasi / $this->anggaran) * 100, 2);
    }

    /**
     * Scope for specific year
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope for specific jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope for approved status
     */
    public function scopeDisetujui($query)
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * Get all expenditure history for this APBDes
     */
    public function historiPengeluarans()
    {
        return $this->hasMany(HistoriPengeluaran::class);
    }

    /**
     * Get all projects linked to this APBDes
     */
    public function proyekDesas()
    {
        return $this->hasMany(ProyekDesa::class);
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['tahun', 'jenis', 'sumber_dana', 'kode_rekening', 'nama_rekening', 'anggaran', 'realisasi', 'sisa_anggaran', 'keterangan', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
