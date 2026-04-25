<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends Model
{
    protected $fillable = [
        'nkk',
        'nama_kepala_keluarga',
        'nik_kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'jumlah_anggota',
        'anggota_aktif',
        'anggota_mutasi',
        'anggota_meninggal',
        'anggota_pindah',
        'anggota_pisah_kk',
        // Kolom fitur KK Bermasalah
        'status_kk',
        'mutasi_penyebab_id',
        'kk_sementara_id',
        'kk_bermasalah_sejak',
        'catatan_bermasalah',
    ];

    protected $casts = [
        'kk_bermasalah_sejak' => 'datetime',
    ];

    // =========================================================
    // RELATIONS — existing
    // =========================================================

    /**
     * Get the penduduks for the kartu keluarga.
     */
    public function penduduks(): HasMany
    {
        return $this->hasMany(Penduduk::class, 'nkk', 'nkk');
    }

    /**
     * Get the active penduduks (not dead/moved).
     */
    public function anggotaAktif(): HasMany
    {
        return $this->penduduks()->whereDoesntHave('mutasis', function ($q) {
            $q->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
        });
    }

    // =========================================================
    // RELATIONS — KK Bermasalah
    // =========================================================

    /**
     * Mutasi yang menjadi penyebab KK bermasalah.
     * Disimpan langsung oleh MutasiObserver saat flagging — 100% akurat.
     */
    public function mutasiPenyebab(): BelongsTo
    {
        return $this->belongsTo(Mutasi::class, 'mutasi_penyebab_id');
    }

    /**
     * Penduduk yang ditunjuk sebagai Kepala Keluarga sementara.
     */
    public function kkSementara(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'kk_sementara_id');
    }

    // =========================================================
    // SCOPES — KK Bermasalah
    // =========================================================

    /**
     * KK yang memiliki masalah (bermasalah atau bermasalah_sementara).
     * Menggantikan query lama: anggota_aktif > 0 AND anggota_mutasi > 0
     */
    public function scopeBermasalah($query)
    {
        return $query->whereIn('status_kk', ['bermasalah', 'bermasalah_sementara']);
    }

    /**
     * KK yang sudah diselesaikan secara permanen.
     */
    public function scopeResolved($query)
    {
        return $query->where('status_kk', 'resolved');
    }

    /**
     * KK yang statusnya normal (tidak bermasalah).
     */
    public function scopeNormal($query)
    {
        return $query->where('status_kk', 'normal');
    }

    // =========================================================
    // HELPERS
    // =========================================================

    /**
     * Berapa hari KK sudah bermasalah.
     */
    public function harisBermasalah(): ?int
    {
        if (!$this->kk_bermasalah_sejak) {
            return null;
        }
        return (int) $this->kk_bermasalah_sejak->diffInDays(now());
    }

    /**
     * Apakah KK ini sedang bermasalah (belum diselesaikan).
     */
    public function isBermasalah(): bool
    {
        return in_array($this->status_kk, ['bermasalah', 'bermasalah_sementara']);
    }
}
