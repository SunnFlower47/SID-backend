<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SuratPengajuan extends Model
{
    use HasFactory, LogsActivity;

    const STATUS_LIST = [
        'pending'  => 'Menunggu Persetujuan',
        'diproses' => 'Diproses',
        'ditolak'  => 'Ditolak',
        'selesai'  => 'Selesai',
    ];

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::saved(function ($surat) {
            \Illuminate\Support\Facades\Cache::forget('dashboard_surat_stats');
        });

        static::deleted(function ($surat) {
            \Illuminate\Support\Facades\Cache::forget('dashboard_surat_stats');
        });
    }

    protected $fillable = [
        'nik_pengaju',
        'nama_pengaju',
        'email_pengaju',
        'no_hp_pengaju',
        'jenis_surat',
        'penduduk_id',
        'nomor_surat',
        'keperluan',
        'tujuan',
        'tanggal_surat',
        'keterangan_tambahan',
        'data_tambahan',
        'status',
        'keterangan',
        'keterangan_admin',
        'file_lampiran',
        'admin_id',
        'approved_at',
        'processed_at',
        'completed_at',
        'penandatangan',
        // TTE (Tanda Tangan Elektronik)
        'is_tte',
        'tte_at',
        'signed_pdf_path',
        'tte_signer_nik',
        'tte_signer_name',
        'qr_token',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'approved_at'   => 'datetime',
        'processed_at'  => 'datetime',
        'completed_at'  => 'datetime',
        'data_tambahan' => 'array',
        'is_tte'        => 'boolean',
        'tte_at'        => 'datetime',
    ];

    /**
     * Get the penduduk that owns the surat pengajuan.
     */
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class)->withTrashed();
    }

    /**
     * Get the admin that processed the surat pengajuan.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get surat type name
     */
    public function getSuratTypeNameAttribute()
    {
        $types = [
            'keterangan-domisili' => 'Surat Keterangan Domisili',
            'pengantar' => 'Surat Pengantar',
            'pindah' => 'Surat Keterangan Pindah',
            'kematian' => 'Surat Keterangan Kematian',
            'kelahiran' => 'Surat Keterangan Kelahiran',
            'tidak-mampu-dewasa' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            'tidak-mampu-anak' => 'Surat Keterangan Tidak Mampu (Anak)',
            'sku' => 'Surat Keterangan Usaha',
            'sktm_dewasa' => 'Surat Keterangan Tidak Mampu (Dewasa)',
            'sktm_anak' => 'Surat Keterangan Tidak Mampu (Anak)',
            'domisili' => 'Surat Keterangan Domisili',
            'berita-acara-penghapusan-aset' => 'Berita Acara Penghapusan Aset',
            'sk-penghapusan-aset' => 'Surat Keputusan (SK) Penghapusan Aset'
        ];

        if (isset($types[$this->jenis_surat])) {
            return $types[$this->jenis_surat];
        }

        // Cek ke database jika tidak ada di mapping (untuk surat lainnya)
        if (is_numeric($this->jenis_surat)) {
            $masterType = \App\Models\SuratType::find($this->jenis_surat);
            if ($masterType) {
                return $masterType->nama;
            }
        }

        return $this->jenis_surat ?? 'Surat Tidak Diketahui';
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'diproses' => 'blue',
            'selesai' => 'green',
            'ditolak' => 'red',
            // legacy fallback
            'approved' => 'green',
            'rejected' => 'red',
            'completed' => 'blue'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Scope for pending surat
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved surat
     */
    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'diproses']);
    }

    /**
     * Scope for completed surat
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['completed', 'selesai']);
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nik_pengaju', 'nama_pengaju', 'email_pengaju', 'no_hp_pengaju', 'jenis_surat', 'penduduk_id', 'nomor_surat', 'keperluan', 'status', 'tanggal_pengajuan', 'tanggal_selesai'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}

