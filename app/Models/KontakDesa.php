<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontakDesa extends Model
{

    protected $fillable = [
        'nama',
        'jenis',
        'jabatan',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'no_telepon',
        'no_hp',
        'email',
        'website',
        'facebook',
        'instagram',
        'youtube',
        'whatsapp',
        'jam_operasional',
        'deskripsi',
        'foto',
        'status_aktif',
        'urutan',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Get the jenis label
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'kantor_desa' => 'Kantor Desa',
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'puskesmas' => 'Puskesmas',
            'posyandu' => 'Posyandu',
            'sekolah' => 'Sekolah',
            'masjid' => 'Masjid',
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

    /**
     * Get the primary contact
     */
    public function getKontakUtamaAttribute()
    {
        if ($this->no_hp) return $this->no_hp;
        if ($this->no_telepon) return $this->no_telepon;
        if ($this->whatsapp) return $this->whatsapp;
        return null;
    }

    /**
     * Scope for active contacts
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope for specific jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope for ordering
     */
    public function scopeByOrder($query)
    {
        return $query->orderBy('urutan')->orderBy('jenis')->orderBy('nama');
    }
}
