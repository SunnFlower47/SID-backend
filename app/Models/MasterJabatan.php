<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MasterJabatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'slug',
        'is_struktur',
        'is_kontak',
        'urutan',
    ];

    protected $casts = [
        'is_struktur' => 'boolean',
        'is_kontak' => 'boolean',
        'urutan' => 'integer',
    ];

    /**
     * Boot function to automatically generate slug from name
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nama, '_');
            }
        });
    }

    /**
     * Scope for Struktur Desa
     */
    public function scopeForStruktur($query)
    {
        return $query->where('is_struktur', true)->orderBy('urutan');
    }

    /**
     * Scope for Kontak Desa
     */
    public function scopeForKontak($query)
    {
        return $query->where('is_kontak', true)->orderBy('urutan');
    }
}
