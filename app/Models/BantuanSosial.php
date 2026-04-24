<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BantuanSosial extends Model
{
    use LogsActivity;
    protected $fillable = [
        'nama_program',
        'jenis_bantuan',
        'deskripsi',
        'nilai_bantuan',
        'periode',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'kriteria_penerima',
        'sumber_dana',
        'kuota_penerima'
    ];

    protected $casts = [
        'kriteria_penerima' => 'array',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_bantuan' => 'decimal:2'
    ];

    /**
     * Mutator untuk kriteria_penerima - konversi string ke array jika perlu
     */
    public function setKriteriaPenerimaAttribute($value)
    {
        if (is_string($value)) {
            // Jika string, konversi ke array dengan memisahkan berdasarkan koma atau newline
            $this->attributes['kriteria_penerima'] = json_encode(array_filter(array_map('trim', preg_split('/[,\n\r]+/', $value))));
        } else {
            $this->attributes['kriteria_penerima'] = json_encode($value);
        }
    }

    /**
     * Relasi ke Penerima Bantuan Sosial
     */
    public function penerima(): HasMany
    {
        return $this->hasMany(PenerimaBantuanSosial::class);
    }

    /**
     * Scope untuk bantuan aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk bantuan berdasarkan jenis
     */
    public function scopeJenisBantuan($query, $jenis)
    {
        return $query->where('jenis_bantuan', $jenis);
    }

    /**
     * Accessor untuk format nilai bantuan
     */
    protected function nilaiBantuanFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->nilai_bantuan, 0, ',', '.')
        );
    }

    /**
     * Accessor untuk status label
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'aktif' => 'Aktif',
                'selesai' => 'Selesai',
                'ditangguhkan' => 'Ditangguhkan',
                default => 'Tidak Diketahui'
            }
        );
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['nama_program', 'jenis_bantuan', 'deskripsi', 'nilai_bantuan', 'periode', 'tanggal_mulai', 'tanggal_selesai', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
