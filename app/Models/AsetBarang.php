<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AsetBarang extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'aset_kategori_id',
        'kode_barang',
        'nama_barang',
        'satuan_default',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(AsetKategori::class, 'aset_kategori_id');
    }

    public function inventaris(): HasMany
    {
        return $this->hasMany(AsetInventaris::class);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('kode_barang');
    }

    /**
     * Cek apakah kode ini adalah header grup (sub-kelompok = 00)
     */
    public function getIsHeaderAttribute(): bool
    {
        $parts = explode('.', $this->kode_barang);
        return isset($parts[3]) && $parts[3] === '00';
    }
}
