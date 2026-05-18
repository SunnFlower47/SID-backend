<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PeraturanDesa extends Model
{
    use LogsActivity;

    protected $fillable = [
        'jenis_peraturan',
        'tahun_anggaran',
        'judul',
        'nomor_peraturan',
        'tanggal_ditetapkan',
        'status',
        'keterangan_bpd',
        'file_dokumen',
    ];

    protected $casts = [
        'tahun_anggaran' => 'integer',
        'tanggal_ditetapkan' => 'date',
    ];

    /**
     * Activity logger config
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('keuangan');
    }

    /**
     * Get full URL for uploaded document
     */
    public function getFileDokumenUrlAttribute()
    {
        if (!$this->file_dokumen) return null;
        return asset('storage/' . $this->file_dokumen);
    }

    /**
     * Appends
     */
    protected $appends = ['file_dokumen_url'];

    /**
     * Helper to check if APBDes for a given year is locked
     * An APBDes is considered locked if there is a Peraturan Desa (jenis APBDes)
     * for that year that has been Approved (disetujui).
     */
    public static function isLocked($tahun)
    {
        return self::where('tahun_anggaran', $tahun)
            ->where('jenis_peraturan', 'APBDes')
            ->where('status', 'disetujui')
            ->exists();
    }
}
