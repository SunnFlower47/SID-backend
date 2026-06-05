<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanahDiDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nop',
        'nama_pemilik',
        'tempat_lahir_berdiri',
        'tanggal_lahir_berdiri',
        'status_kepemilikan',
        'tanggal_perolehan',
        'no_sertifikat',
        'tanggal_penerbitan_sertifikat',
        'no_buku_c',
        'no_persil',
        'no_kelas',
        'luas_sawah',
        'luas_tegalan',
        'luas_kebun',
        'luas_perumahan',
        'luas_industri',
        'luas_fasilitas_umum',
        'luas_lain_lain',
        'lokasi_tanah',
        'batas_utara',
        'batas_timur',
        'batas_selatan',
        'batas_barat',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'luas_tanah' => 'decimal:2',
        'status_hm' => 'decimal:2',
        'status_hgb' => 'decimal:2',
        'status_hp' => 'decimal:2',
        'status_hgu' => 'decimal:2',
        'status_hpl' => 'decimal:2',
        'status_ma' => 'decimal:2',
        'status_tn' => 'decimal:2',
        'status_td' => 'decimal:2',
        'belum_bersertifikat' => 'decimal:2',
        'penggunaan_perumahan' => 'decimal:2',
        'penggunaan_perdagangan' => 'decimal:2',
        'penggunaan_perkantoran' => 'decimal:2',
        'penggunaan_industri' => 'decimal:2',
        'penggunaan_fasilitas_umum' => 'decimal:2',
        'penggunaan_sawah' => 'decimal:2',
        'penggunaan_tegalan' => 'decimal:2',
        'penggunaan_perkebunan' => 'decimal:2',
        'penggunaan_peternakan' => 'decimal:2',
        'penggunaan_hutan' => 'decimal:2',
        'penggunaan_kosong' => 'decimal:2',
        'penggunaan_lain' => 'decimal:2',
    ];
}
