<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Umkm extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'nama_usaha',
        'nama_pemilik',
        'nik_pemilik',
        'alamat_usaha',
        'rt',
        'rw',
        'dusun',
        'no_telepon',
        'email',
        'jenis_usaha',
        'deskripsi_usaha',
        'modal_awal',
        'omset_bulanan',
        'jumlah_karyawan',
        'status_usaha',
        'tanggal_berdiri',
        'produk_unggulan',
        'foto_usaha',
        'latitude',
        'longitude',
        'is_unggulan',
        'is_verified',
    ];

    protected $casts = [
        'produk_unggulan' => 'array',
        'foto_usaha' => 'array',
        'modal_awal' => 'decimal:2',
        'omset_bulanan' => 'decimal:2',
        'tanggal_berdiri' => 'date',
        'is_unggulan' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // Accessors
    public function getJenisUsahaLabelAttribute()
    {
        $labels = [
            'makanan' => 'Makanan',
            'minuman' => 'Minuman',
            'kerajinan' => 'Kerajinan',
            'jasa' => 'Jasa',
            'perdagangan' => 'Perdagangan',
            'pertanian' => 'Pertanian',
            'peternakan' => 'Peternakan',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis_usaha] ?? $this->jenis_usaha;
    }

    public function getStatusUsahaLabelAttribute()
    {
        $labels = [
            'aktif' => 'Aktif',
            'tutup' => 'Tutup',
            'pindah' => 'Pindah',
        ];

        return $labels[$this->status_usaha] ?? $this->status_usaha;
    }

    public function getAlamatLengkapAttribute()
    {
        $alamat = $this->alamat_usaha;
        if ($this->rt) $alamat .= ', RT ' . $this->rt;
        if ($this->rw) $alamat .= ', RW ' . $this->rw;
        if ($this->dusun) $alamat .= ', Dusun ' . $this->dusun;
        return $alamat;
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status_usaha', 'aktif');
    }

    public function scopeUnggulan($query)
    {
        return $query->where('is_unggulan', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeJenisUsaha($query, $jenis)
    {
        return $query->where('jenis_usaha', $jenis);
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_usaha', 'nama_pemilik', 'nik_pemilik', 'alamat_usaha', 'rt', 'rw', 'dusun', 'no_telepon', 'jenis_usaha', 'status_usaha', 'jumlah_karyawan', 'is_unggulan', 'is_verified'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
