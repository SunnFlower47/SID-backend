<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiBank extends Model
{
    protected $fillable = [
        'tanggal_mutasi',
        'jenis_mutasi',
        'uraian',
        'jumlah',
        'no_bukti',
        'user_id',
    ];

    protected $casts = [
        'tanggal_mutasi' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
