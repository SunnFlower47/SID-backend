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

    protected $appends = ['formatted_nop'];

    public function getFormattedNopAttribute()
    {
        $value = $this->nop;
        if ($value && strlen($value) === 18) {
            return substr($value, 0, 2) . '.' . substr($value, 2, 2) . '.' . substr($value, 4, 3) . '.' . substr($value, 7, 3) . '.' . substr($value, 10, 3) . '-' . substr($value, 13, 4) . '.' . substr($value, 17, 1);
        }
        return $value;
    }

    public function tagihans()
    {
        return $this->hasMany(PajakPbbTagihan::class);
    }
}
