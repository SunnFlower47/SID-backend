<?php

namespace App\Models\Tenant\Sekretariat;

use Illuminate\Database\Eloquent\Model;

class AnggotaBpd extends Model
{
    protected $fillable = [
        'nik',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'pendidikan_terakhir',
        'jabatan',
        'no_keputusan_pengangkatan',
        'tanggal_keputusan_pengangkatan',
        'no_keputusan_pemberhentian',
        'tanggal_keputusan_pemberhentian',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'no_hp',
        'status',
        'keterangan',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_keputusan_pengangkatan' => 'date',
        'tanggal_keputusan_pemberhentian' => 'date',
        'status' => 'string',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Purna Tugas / Tidak Aktif',
            default => 'Aktif',
        };
    }
}
