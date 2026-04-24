<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasDesa extends Model
{

    protected $fillable = [
        'nama',
        'jenis',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'latitude',
        'longitude',
        'deskripsi',
        'kontak',
        'jam_operasional',
        'status_aktif',
        'foto',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'status_aktif' => 'boolean',
    ];

    /**
     * Get the jenis label
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'sekolah' => 'Sekolah',
            'posyandu' => 'Posyandu',
            'masjid' => 'Masjid',
            'gereja' => 'Gereja',
            'puskesmas' => 'Puskesmas',
            'pos_ronda' => 'Pos Ronda',
            'balai_desa' => 'Balai Desa',
            'lapangan' => 'Lapangan',
            'pasar' => 'Pasar',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis] ?? $this->jenis;
    }

    /**
     * Get the full address
     */
    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->alamat;
        if ($this->rt) $alamat .= ', RT ' . $this->rt;
        if ($this->rw) $alamat .= '/RW ' . $this->rw;
        if ($this->dusun) $alamat .= ', Dusun ' . $this->dusun;
        return $alamat;
    }
}
