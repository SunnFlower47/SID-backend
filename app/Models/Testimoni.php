<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Testimoni extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'rt',
        'rw',
        'testimoni',
        'status',
        'rating',
        'kategori',
        'is_anonymous',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'rating' => 'integer',
    ];

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
        if ($this->rt || $this->rw) {
            return 'RT ' . ($this->rt ?? '-') . ' / RW ' . ($this->rw ?? '-');
        }
        return '-';
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
}
