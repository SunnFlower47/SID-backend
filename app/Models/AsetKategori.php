<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsetKategori extends Model
{
    protected $table = 'aset_kategoris';

    protected $fillable = ['kode', 'nama', 'urutan'];

    // ── Relasi ───────────────────────────────────────────────────────────────

    public function barangs(): HasMany
    {
        return $this->hasMany(AsetBarang::class, 'aset_kategori_id');
    }

    // ── Scope ─────────────────────────────────────────────────────────────────

    /**
     * Urutkan berdasarkan urutan lalu kode.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('kode');
    }
}
