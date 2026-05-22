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
        'jenis',        // 'tambah' | 'kurang'
        'kwantitas',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'   => 'date',
        'kwantitas' => 'float',
        'nilai'     => 'float',
        'tahun'     => 'integer',
        'semester'  => 'integer',
    ];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(AsetInventaris::class, 'aset_inventaris_id');
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
