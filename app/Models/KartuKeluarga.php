<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KartuKeluarga extends Model
{
    protected $fillable = [
        'nkk',
        'nama_kepala_keluarga',
        'nik_kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'jumlah_anggota',
        'anggota_aktif',
        'anggota_mutasi',
        'anggota_meninggal',
        'anggota_pindah',
        'anggota_pisah_kk',
    ];

    /**
     * Get the penduduks for the kartu keluarga.
     */
    public function penduduks(): HasMany
    {
        return $this->hasMany(Penduduk::class, 'nkk', 'nkk');
    }

    /**
     * Get the active penduduks (not deleted, not moved out/dead if specified logic applies)
     * Note: Since we have 'status' counters, this relation is for detailed listing.
     */
    public function anggotaAktif(): HasMany
    {
        return $this->penduduks()->whereDoesntHave('mutasis', function($q) {
            $q->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar', 'pisah_kk']);
        });
    }
}
