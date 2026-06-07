<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PajakPbbTagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'pajak_pbb_objek_id',
        'tahun',
        'pbb_terhutang',
        'jatuh_tempo',
        'status',
        'tanggal_bayar',
        'denda',
    ];

    public function objek()
    {
        return $this->belongsTo(PajakPbbObjek::class, 'pajak_pbb_objek_id');
    }
}
