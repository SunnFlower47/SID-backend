<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Berita extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'excerpt',
        'gambar',
        'kategori',
        'status',
        'author_id',
        'published_at',
        'featured'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    /**
     * Get the author that owns the berita.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope for published berita
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured berita
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Get excerpt or generate from content
     */
    public function getExcerptAttribute($value)
    {
        if ($value) {
            return $value;
        }

        return \Str::limit(strip_tags($this->konten), 150);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->gambar) {
            // Jika path berupa full URL, langsung kembalikan
            if (filter_var($this->gambar, FILTER_VALIDATE_URL)) {
                return $this->gambar;
            }

            // Cek di disk default
            if (\Illuminate\Support\Facades\Storage::exists($this->gambar)) {
                return \Illuminate\Support\Facades\Storage::url($this->gambar);
            }

            // Jika tidak ada di default disk, cek di disk s3
            if (config('filesystems.disks.s3') && \Illuminate\Support\Facades\Storage::disk('s3')->exists($this->gambar)) {
                return \Illuminate\Support\Facades\Storage::disk('s3')->url($this->gambar);
            }

            // Jika tidak ada di s3 disk, cek di disk public (local)
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->gambar)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->gambar);
            }

            // Fallback default
            return \Illuminate\Support\Facades\Storage::url($this->gambar);
        }

        return null;
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

