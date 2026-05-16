<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportConflict extends Model
{
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
}
