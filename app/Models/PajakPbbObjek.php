<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PajakPbbObjek extends Model
{
    use HasFactory;

    protected $fillable = [
        'nop',
        'nama_wp',
        'alamat_wp',
        'alamat_objek',
        'luas_bumi',
        'luas_bangunan',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function tagihans()
    {
        return $this->hasMany(PajakPbbTagihan::class);
    }
}
