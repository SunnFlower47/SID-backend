<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'form_json'
    ];

    protected $casts = [
        'has_template' => 'boolean',
        'is_active' => 'boolean',
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
     * Get the sub-templates associated with this letter type.
     */
    public function templates()
    {
        return $this->hasMany(SuratTypeTemplate::class, 'surat_type_id')->orderBy('urutan');
    }
}
