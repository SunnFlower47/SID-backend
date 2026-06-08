<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApbdesRincian extends Model
{
    protected $fillable = [
        'apbdes_id',
        'uraian',
        'volume',
        'satuan',
        'harga_satuan',
        'jumlah',
        'keterangan',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];

    public function apbdes(): BelongsTo
    {
        return $this->belongsTo(Apbdes::class);
    }
}
