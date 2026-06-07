<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsetInventaris extends Model
{
    protected $table = 'aset_inventaris';

    protected $fillable = [
        'aset_barang_id',
        'nup',
        'nama_barang_override',   // nama SPESIFIK aset milik desa (wajib)
        'satuan',
        'lokasi',
        'tanggal_perolehan',
        'asal_usul',
        'kondisi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function barang(): BelongsTo
    {
        return $this->belongsTo(AsetBarang::class, 'aset_barang_id');
    }

    public function mutasis(): HasMany
    {
        return $this->hasMany(AsetMutasi::class, 'aset_inventaris_id')
                    ->orderBy('tanggal');
    }

    // ── Accessor ──────────────────────────────────────────────────────────────

    /**
     * Nama tampil = nama spesifik milik desa (override).
     * Fallback ke nama tipe barang jika belum diisi.
     */
    public function getNamaDisplayAttribute(): string
    {
        return $this->nama_barang_override
            ?: ($this->barang?->nama_barang ?? '-');
    }

    // ── Saldo (dihitung dari mutasi, bukan kolom statis) ─────────────────────

    /**
     * Total kwantitas saat ini berdasarkan semua mutasi.
     */
    public function getSaldoKwantitasAttribute(): float
    {
        return (float) (
            $this->mutasis->where('jenis', 'tambah')->sum('kwantitas')
            - $this->mutasis->where('jenis', 'kurang')->sum('kwantitas')
        );
    }

    /**
     * Total nilai saat ini berdasarkan semua mutasi.
     */
    public function getSaldoNilaiAttribute(): float
    {
        return (float) (
            $this->mutasis->where('jenis', 'tambah')->sum('nilai')
            - $this->mutasis->where('jenis', 'kurang')->sum('nilai')
        );
    }

    /**
     * Apakah aset ini dalam kondisi perlu penghapusan?
     * (saldo kwantitas = 0, akan diproses di fitur Penghapusan Aset — TODO)
     */
    public function getPerluPenghapusanAttribute(): bool
    {
        return $this->saldo_kwantitas <= 0
            && $this->mutasis->isNotEmpty();
    }
}
