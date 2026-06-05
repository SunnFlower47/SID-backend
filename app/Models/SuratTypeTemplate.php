<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SuratTypeTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'surat_type_id',
        'kode',
        'nama',
        'deskripsi',
        'file_template',
        'form_json',
        'urutan',
        'is_active',
        'gender_filter',
    ];

    protected $casts = [
        'form_json' => 'array',
        'is_active' => 'boolean',
        'urutan'    => 'integer',
    ];

    public function suratType(): BelongsTo
    {
        return $this->belongsTo(SuratType::class, 'surat_type_id');
    }

    /**
     * Auto-delete file dari storage saat template diupdate atau dihapus.
     */
    protected static function booted(): void
    {
        static::updating(function (SuratTypeTemplate $template) {
            if ($template->isDirty('file_template')) {
                $oldFile = $template->getOriginal('file_template');
                if ($oldFile) {
                    Storage::disk('local')->delete('templates/surat/' . $oldFile);
                }
            }
        });

        static::deleted(function (SuratTypeTemplate $template) {
            if ($template->file_template) {
                Storage::disk('local')->delete('templates/surat/' . $template->file_template);
            }
        });
    }
}
