<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surat extends Model
{
    protected $fillable = [
        'jenis_surat',
        'penduduk_id',
        'nomor_surat',
        'keperluan',
        'tujuan',
        'tanggal_surat',
        'tahun',
        'keterangan_tambahan',
        'data_tambahan',
        'status',
        'keterangan',
        'created_by'
    ];

    protected $casts = [
        'data_tambahan' => 'array',
        'tanggal_surat' => 'date'
    ];

    /**
     * Get the penduduk that owns the surat.
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    /**
     * Get the user that created the surat.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
