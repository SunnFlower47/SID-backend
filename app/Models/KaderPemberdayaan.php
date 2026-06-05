<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KaderPemberdayaan extends Model
{
    protected $fillable = [
        'nama',
        'umur',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'bidang',
        'alamat',
        'keterangan',
    ];
}
