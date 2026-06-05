<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TanahDiDesaMutasi extends Model
{
    protected $fillable = [
        'tanah_di_desa_id',
        'pemilik_lama',
        'pemilik_baru',
        'tanggal_mutasi',
        'keterangan',
        'created_by',
    ];

    public function tanahDiDesa()
    {
        return $this->belongsTo(TanahDiDesa::class, 'tanah_di_desa_id');
    }
}
