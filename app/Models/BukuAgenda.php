<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuAgenda extends Model
{
    protected $fillable = [
        'tanggal',
        'jenis_surat',
        'nomor_surat',
        'tanggal_surat',
        'pengirim_penerima',
        'isi_singkat',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_surat' => 'date',
    ];
}
