<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Mutasi extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'penduduk_id',
        'jenis_mutasi',
        'kategori_mutasi',
        'asal_tujuan',
        'tanggal_mutasi',
        'detail_tambahan',
        'alasan',
        'dokumen_pendukung',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'detail_tambahan' => 'array',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['penduduk_id', 'jenis_mutasi', 'kategori_mutasi', 'asal_tujuan', 'tanggal_mutasi', 'alasan', 'dokumen_pendukung'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relasi ke model Penduduk (termasuk yang soft deleted)
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class)->withTrashed();
    }

    // Note: created_by dan updated_by tidak ada di database
    // Relasi ini dinonaktifkan sampai kolom ditambahkan ke database

    /**
     * Scope untuk mutasi kelahiran
     */
    public function scopeKelahiran($query)
    {
        return $query->where('jenis_mutasi', 'kelahiran');
    }

    /**
     * Scope untuk mutasi kematian
     */
    public function scopeKematian($query)
    {
        return $query->where('jenis_mutasi', 'kematian');
    }

    /**
     * Scope untuk mutasi pindah masuk
     */
    public function scopePindahMasuk($query)
    {
        return $query->where('jenis_mutasi', 'pindah_masuk');
    }

    /**
     * Scope untuk mutasi pindah keluar
     */
    public function scopePindahKeluar($query)
    {
        return $query->where('jenis_mutasi', 'pindah_keluar');
    }

    /**
     * Scope untuk mutasi pindah RT/RW
     */
    public function scopePindahRtRw($query)
    {
        return $query->where('jenis_mutasi', 'pindah_rt_rw');
    }

    /**
     * Scope untuk mutasi pisah KK
     */
    public function scopePisahKk($query)
    {
        return $query->where('jenis_mutasi', 'pisah_kk');
    }

    /**
     * Scope untuk mutasi pembaruan KK (resolusi KK bermasalah) — FASE 4
     */
    public function scopePembaruanKk($query)
    {
        return $query->where('jenis_mutasi', 'pembaruan_kk');
    }

    /**
     * Accessor untuk jenis mutasi label
     */
    public function getJenisMutasiLabelAttribute(): string
    {
        return match($this->jenis_mutasi) {
            'kelahiran'     => 'Kelahiran',
            'kematian'      => 'Kematian',
            'pindah_masuk'  => 'Pindah Masuk',
            'pindah_keluar' => 'Pindah Keluar',
            'pindah_rt_rw'  => 'Pindah RT/RW',
            'pisah_kk'      => 'Pisah KK',
            'pembaruan_kk'  => 'Pembaruan KK',  // FASE 4: resolusi KK bermasalah
            default         => 'Tidak Diketahui'
        };
    }

    /**
     * Cek apakah mutasi ini memerlukan soft-delete (undo) vs cancel.
     * Digunakan di blade views untuk menentukan tombol Undo vs Cancel.
     */
    public function isSoftDeleteType(): bool
    {
        if (in_array($this->jenis_mutasi, ['kematian', 'pindah_keluar'])) {
            return true;
        }

        if ($this->jenis_mutasi === 'pisah_kk') {
            return !in_array($this->kategori_mutasi, ['dalam_desa']);
        }

        return false;
    }

    /**
     * Accessor untuk data kematian
     */
    public function getDataKematianAttribute(): ?array
    {
        return $this->detail_tambahan['kematian'] ?? null;
    }

    /**
     * Accessor untuk data pemakaman
     */
    public function getDataPemakamanAttribute(): ?array
    {
        return $this->detail_tambahan['pemakaman'] ?? null;
    }

    /**
     * Accessor untuk data pelapor (kematian)
     */
    public function getDataPelaporAttribute(): ?array
    {
        if (!$this->detail_tambahan) return null;
        return [
            'nama' => $this->detail_tambahan['pelapor_nama'] ?? null,
            'umur' => $this->detail_tambahan['pelapor_umur'] ?? null,
            'pekerjaan' => $this->detail_tambahan['pelapor_pekerjaan'] ?? null,
            'alamat' => $this->detail_tambahan['pelapor_alamat'] ?? null,
            'hubungan' => $this->detail_tambahan['pelapor_hubungan'] ?? null,
        ];
    }

    /**
     * Accessor untuk data snapshot sebelum mutasi (pisah KK / pindah RT/RW)
     */
    public function getDataSnapshotAttribute(): ?array
    {
        if (!$this->detail_tambahan) return null;
        // Untuk pisah KK, snapshot ada di root detail_tambahan
        // Untuk pindah RT/RW, snapshot ada di key 'snapshot_asal'
        return $this->detail_tambahan['snapshot_asal'] ?? $this->detail_tambahan;
    }
}
