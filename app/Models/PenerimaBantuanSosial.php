<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PenerimaBantuanSosial extends Model
{
    use LogsActivity;
    protected $fillable = [
        'bantuan_sosial_id',
        'penduduk_id',
        'nomor_kartu',
        'nilai_diterima',
        'tanggal_penerimaan',
        'status_penerimaan',
        'keterangan',
        'data_tambahan'
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date',
        'nilai_diterima' => 'decimal:2',
        'data_tambahan' => 'array'
    ];

    /**
     * Relasi ke Bantuan Sosial
     */
    public function bantuanSosial(): BelongsTo
    {
        return $this->belongsTo(BantuanSosial::class);
    }

    /**
     * Relasi ke Penduduk
     */
    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class);
    }

    /**
     * Scope untuk penerima aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status_penerimaan', 'aktif');
    }

    /**
     * Accessor untuk format nilai diterima
     */
    protected function nilaiDiterimaFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => 'Rp ' . number_format((float) $this->nilai_diterima, 0, ',', '.')
        );
    }

    /**
     * Accessor untuk status penerimaan label
     */
    protected function statusPenerimaanLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status_penerimaan) {
                'aktif' => 'Aktif',
                'ditangguhkan' => 'Ditangguhkan',
                'dihentikan' => 'Dihentikan',
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
            ->logOnly(['bantuan_sosial_id', 'penduduk_id', 'nomor_kartu', 'nilai_diterima', 'tanggal_penerimaan', 'status_penerimaan', 'keterangan'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
