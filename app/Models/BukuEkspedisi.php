<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuEkspedisi extends Model
{
    protected $fillable = [
        'tanggal_pengiriman',
        'tanggal_surat',
        'nomor_surat',
        'isi_singkat',
        'tujuan',
        'penerima',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengiriman' => 'date',
        'tanggal_surat' => 'date',
    ];
}
