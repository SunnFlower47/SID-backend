<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Pengaduan extends Model
{
    protected $fillable = [
        'nama_pelapor',
        'nik_pelapor',
        'telepon',
        'email',
        'alamat',
        'kategori',
        'judul',
        'deskripsi',
        'lokasi',
        'foto',
        'prioritas',
        'status',
        'tanggapan',
        'tanggal_tanggapan',
        'user_id'
    ];

    protected $casts = [
        'foto' => 'array',
        'tanggal_tanggapan' => 'datetime'
    ];

    /**
     * Relasi ke User (Admin yang menangani)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope untuk pengaduan berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk pengaduan berdasarkan prioritas
     */
    public function scopeByPrioritas($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    /**
     * Accessor untuk status label
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'baru' => 'Baru',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
                default => 'Tidak Diketahui'
            }
        );
    }

    /**
     * Accessor untuk prioritas label
     */
    protected function prioritasLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->prioritas) {
                'rendah' => 'Rendah',
                'sedang' => 'Sedang',
                'tinggi' => 'Tinggi',
                'darurat' => 'Darurat',
                default => 'Tidak Diketahui'
            }
        );
    }

    /**
     * Accessor untuk prioritas color
     */
    protected function prioritasColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->prioritas) {
                'rendah' => 'success',
                'sedang' => 'warning',
                'tinggi' => 'danger',
                'darurat' => 'dark',
                default => 'secondary'
            }
        );
    }
}
