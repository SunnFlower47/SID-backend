<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class KeputusanKades extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'keputusan_kades';

    protected $fillable = [
        'nomor_keputusan',
        'judul_keputusan',
        'tanggal_ditetapkan',
        'keterangan',
        'file_dokumen',
        'author_id',
    ];

    protected $casts = [
        'tanggal_ditetapkan' => 'date',
    ];

    /**
     * Konfigurasi Spatie Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Keputusan Kades {$eventName}");
    }

    /**
     * Relasi ke User pembuat
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
