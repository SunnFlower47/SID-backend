<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ImportConflict extends Model
{
    use LogsActivity;
    protected $fillable = [
        'batch_id',
        'source_file',
        'sheet_name',
        'row_number',
        'nik',
        'nama',
        'nkk',
        'rw_raw',
        'rt_raw',
        'dusun_raw',
        'reason',
        'issue_type',
        'status',
        'meta',
        'payload_raw',
        'payload_fixed',
        'resolution_action',
        'resolved_by',
        'resolved_at',
        'reprocessed_at',
        'reprocess_status',
        'reprocess_message',
    ];

    protected $casts = [
        'meta' => 'array',
        'payload_raw' => 'array',
        'payload_fixed' => 'array',
        'resolved_at' => 'datetime',
        'reprocessed_at' => 'datetime',
    ];

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
