<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasWilayahLabels;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Testimoni extends Model
{
    use HasFactory, HasWilayahLabels, LogsActivity;

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'rt_id',
        'rw_id',
        'dusun_id',
        'testimoni',
        'status',
        'rating',
        'kategori',
        'is_anonymous',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'rt_id' => 'integer',
        'rw_id' => 'integer',
        'dusun_id' => 'integer',
        'is_anonymous' => 'boolean',
        'rating' => 'integer',
    ];

    protected $appends = ['rt_label', 'rw_label', 'dusun_label'];

    // =========================================================
    // RELATIONS - WILAYAH MASTER
    // =========================================================

    public function rtMaster(): BelongsTo
    {
        return $this->belongsTo(Rt::class, 'rt_id');
    }

    public function rwMaster(): BelongsTo
    {
        return $this->belongsTo(Rw::class, 'rw_id');
    }

    public function dusunMaster(): BelongsTo
    {
        return $this->belongsTo(Dusun::class, 'dusun_id');
    }

    /**
     * Scope for Eager Loading Wilayah Master (High Performance)
     */
    public function scopeWithWilayah($query)
    {
        return $query->with(['rtMaster', 'rwMaster', 'dusunMaster']);
    }


    // Scope untuk filter status
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope untuk rating
    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }

    // Accessor untuk RT/RW
    public function getRtRwAttribute()
    {
        return "RT {$this->rt_label} / RW {$this->rw_label}";
    }


    // Accessor untuk nama anonim
    public function getDisplayNameAttribute()
    {
        return $this->is_anonymous ? 'Warga Anonim' : $this->nama;
    }

    // Accessor untuk rating stars
    public function getRatingStarsAttribute()
    {
        if (!$this->rating) return '';

        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $this->rating ? '★' : '☆';
        }
        return $stars;
    }

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
