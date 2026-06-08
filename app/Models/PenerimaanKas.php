<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerimaanKas extends Model
{
    protected $fillable = [
        'tanggal_penerimaan',
        'uraian',
        'apbdes_id',
        'jumlah',
        'no_bukti',
        'penyetor',
        'user_id',
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function apbdes(): BelongsTo
    {
        return $this->belongsTo(Apbdes::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
