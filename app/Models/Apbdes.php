<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Apbdes extends Model
{
    use LogsActivity;

    /**
     * 5 Bidang APBDes sesuai Permendagri No. 20 Tahun 2018
     */
    const BIDANG = [
        1 => 'Penyelenggaraan Pemerintahan Desa',
        2 => 'Pelaksanaan Pembangunan Desa',
        3 => 'Pembinaan Kemasyarakatan Desa',
        4 => 'Pemberdayaan Masyarakat Desa',
        5 => 'Penanggulangan Bencana, Kedaruratan & Mendesak',
    ];

    /**
     * Sumber Dana APBDes sesuai Permendagri No. 20 Tahun 2018
     */
    const SUMBER_DANA = [
        // Dana Transfer Pusat
        'dana_desa_ad'        => 'Dana Desa - Alokasi Dasar (AD)',
        'dana_desa_af'        => 'Dana Desa - Alokasi Formula (AF)',
        'dana_desa_ak'        => 'Dana Desa - Alokasi Kinerja (AK)',
        // Dana Transfer Daerah
        'add'                 => 'Alokasi Dana Desa (ADD)',
        'bhpr'                => 'Bagi Hasil Pajak & Retribusi (BHPR)',
        'bantuan_keuangan'    => 'Bantuan Keuangan APBD Prov/Kab',
        // Dana Lain
        'dau'                 => 'Dana Alokasi Umum (DAU)',
        'dak'                 => 'Dana Alokasi Khusus (DAK)',
        'dbh'                 => 'Dana Bagi Hasil (DBH)',
        'did'                 => 'Dana Insentif Daerah (DID)',
        // Pendapatan Desa
        'pad'                 => 'Pendapatan Asli Desa (PAD)',
        'hibah'               => 'Hibah & Sumbangan Pihak Ketiga',
        'lain_lain'           => 'Lain-Lain PADes yang Sah',
    ];

    protected $fillable = [
        'tahun',
        'bidang',
        'sub_bidang',
        'kegiatan',
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
        'tahun'        => 'integer',
        'bidang'       => 'integer',
        'anggaran'     => 'decimal:2',
        'realisasi'    => 'decimal:2',
        'sisa_anggaran'=> 'decimal:2',
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
        return self::SUMBER_DANA[$this->sumber_dana] ?? $this->sumber_dana;
    }

    /**
     * Get the bidang label (Permendagri 20/2018)
     */
    public function getBidangLabelAttribute()
    {
        return self::BIDANG[$this->bidang] ?? 'Bidang ' . $this->bidang;
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
     * Scope for specific bidang (1-5)
     */
    public function scopeBidang($query, $bidang)
    {
        return $query->where('bidang', $bidang);
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
            ->logOnly(['tahun', 'bidang', 'sub_bidang', 'kegiatan', 'jenis', 'sumber_dana', 'kode_rekening', 'nama_rekening', 'anggaran', 'realisasi', 'sisa_anggaran', 'keterangan', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
