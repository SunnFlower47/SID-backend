<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturDesa extends Model
{

    protected $fillable = [
        'nama',
        'jabatan',
        'kategori',
        'nik',
        'no_hp',
        'email',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'tugas_wewenang',
        'tanggal_pengangkatan',
        'tanggal_berakhir',
        'foto',
        'status_aktif',
        'urutan',
    ];

    protected $casts = [
        'tanggal_pengangkatan' => 'date',
        'tanggal_berakhir' => 'date',
        'status_aktif' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Get the kategori label
     */
    public function getKategoriLabelAttribute()
    {
        $labels = [
            'kepala_desa' => 'Kepala Desa',
            'sekretaris' => 'Sekretaris Desa',
            'bendahara' => 'Bendahara Desa',
            'kaur_keuangan' => 'KAUR Keuangan',
            'kaur_perencanaan' => 'KAUR Perencanaan',
            'kaur_umum' => 'KAUR Umum',
            'kasi_pemerintahan' => 'Kasi Pemerintahan',
            'kasi_kesejahteraan' => 'Kasi Kesejahteraan',
            'kasi_pelayanan' => 'Kasi Pelayanan',
            'kepala_dusun' => 'Kepala Dusun',
            'ketua_rw' => 'Ketua RW',
            'ketua_rt' => 'Ketua RT',
            'ketua_bumdes' => 'Ketua BUMDes',
            'staf_kaur' => 'Staf KAUR',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->kategori] ?? $this->kategori;
    }

    /**
     * Get the full address
     */
    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->alamat ?? '';
        if ($this->rt) $alamat .= ', RT ' . $this->rt;
        if ($this->rw) $alamat .= '/RW ' . $this->rw;
        if ($this->dusun) $alamat .= ', Dusun ' . $this->dusun;
        return $alamat ?: 'Alamat tidak tersedia';
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status_aktif ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Scope for active positions
     */
    public function scopeAktif($query)
    {
        return $query->where('status_aktif', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope for ordering by hierarchy
     */
    public function scopeByHierarchy($query)
    {
        return $query->orderBy('urutan')->orderBy('kategori')->orderBy('nama');
    }
}
