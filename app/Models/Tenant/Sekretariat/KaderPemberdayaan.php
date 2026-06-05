<?php

namespace App\Models\Tenant\Sekretariat;

use Illuminate\Database\Eloquent\Model;

class KaderPemberdayaan extends Model
{
    protected $fillable = [
        'nik',
        'nama',
        'umur',
        'jenis_kelamin',
        'no_hp',
        'email',
        'pendidikan_terakhir',
        'bidang',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'keterangan',
        'status',
        'foto',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            default => 'Aktif',
        };
    }
}

