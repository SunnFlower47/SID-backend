<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SuratType extends Model
{
    use HasFactory, LogsActivity;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama',
        'kode',
        'deskripsi',
        'persyaratan',
        'has_template',
        'template_code',
        'file_template',
        'icon',
        'color',
        'is_active',
        'is_public',
        'has_multi_template',
        'form_json'
    ];

    protected $casts = [
        'has_template' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'has_multi_template' => 'boolean',
        'form_json' => 'array'
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['id', 'nama', 'persyaratan', 'has_template', 'template_code', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relasi ke sub-template dokumen.
     */
    public function templates(): HasMany
    {
        return $this->hasMany(SuratTypeTemplate::class, 'surat_type_id')->orderBy('urutan');
    }

    /**
     * Boot model events to automatically manage file storage.
     */
    protected static function booted()
    {
        // Hapus file lama jika diupdate dengan file baru
        static::updating(function ($suratType) {
            if ($suratType->isDirty('file_template')) {
                $oldFile = $suratType->getOriginal('file_template');
                if ($oldFile) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete('templates/surat/' . $oldFile);
                }
            }
        });

        // Hapus file saat jenis surat dihapus
        static::deleted(function ($suratType) {
            if ($suratType->file_template) {
                \Illuminate\Support\Facades\Storage::disk('local')->delete('templates/surat/' . $suratType->file_template);
            }
        });
    }
}
