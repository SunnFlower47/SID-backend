<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsetMutasi extends Model
{
    protected $table = 'aset_mutasi';

    protected $fillable = [
        'aset_inventaris_id',
        'tahun',
        'semester',
        'tanggal',
        'jenis',           // 'tambah' | 'kurang'
        'alasan_kurang',   // rusak | dijual | disumbangkan | dipindahkan | hibah_masuk | pengadaan_baru | lainnya
        'kwantitas',
        'nilai',
        'keterangan',
        'berita_acara_surat_id',
        'sk_surat_id',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'kwantitas' => 'float',
        'nilai'     => 'float',
        'tahun'     => 'integer',
        'semester'  => 'integer',
    ];

    public const ALASAN_KURANG_LABELS = [
        'rusak'         => 'Rusak / Dihapus',
        'dijual'        => 'Dijual',
        'disumbangkan'  => 'Disumbangkan / Hibah Keluar',
        'dipindahkan'   => 'Dipindahkan',
        'lainnya'       => 'Lainnya',
    ];

    public const ALASAN_TAMBAH_LABELS = [
        'pengadaan_baru' => 'Pengadaan Baru',
        'hibah_masuk'    => 'Hibah / Sumbangan Masuk',
        'lainnya'        => 'Lainnya',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(AsetInventaris::class, 'aset_inventaris_id');
    }

    public function beritaAcaraSurat(): BelongsTo
    {
        return $this->belongsTo(SuratPengajuan::class, 'berita_acara_surat_id');
    }

    public function skSurat(): BelongsTo
    {
        return $this->belongsTo(SuratPengajuan::class, 'sk_surat_id');
    }

    // ── Scope ────────────────────────────────────────────────────────────────

    public function scopePeriode($query, int $tahun, int $semester)
    {
        return $query->where('tahun', $tahun)->where('semester', $semester);
    }

    public function scopeTambah($query)
    {
        return $query->where('jenis', 'tambah');
    }

    public function scopeKurang($query)
    {
        return $query->where('jenis', 'kurang');
    }
}
