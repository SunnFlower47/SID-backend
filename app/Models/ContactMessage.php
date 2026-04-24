<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ContactMessage extends Model
{
    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'subjek',
        'pesan',
        'status',
        'ip_address',
        'user_agent',
        'read_at',
        'replied_at',
        'admin_reply'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
    ];

    /**
     * Scope untuk pesan yang belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    /**
     * Scope untuk pesan yang sudah dibaca
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope untuk pesan yang sudah dijawab
     */
    public function scopeReplied($query)
    {
        return $query->where('status', 'replied');
    }

    /**
     * Scope untuk pesan yang diarsipkan
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Accessor untuk status label
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'unread' => 'Belum Dibaca',
                'read' => 'Sudah Dibaca',
                'replied' => 'Sudah Dijawab',
                'archived' => 'Diarsipkan',
                default => 'Tidak Diketahui'
            }
        );
    }

    /**
     * Accessor untuk status color
     */
    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'unread' => 'danger',
                'read' => 'warning',
                'replied' => 'success',
                'archived' => 'secondary',
                default => 'light'
            }
        );
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now()
        ]);
    }

    /**
     * Mark as replied
     */
    public function markAsReplied($adminReply = null)
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
            'admin_reply' => $adminReply
        ]);
    }

    /**
     * Archive message
     */
    public function archive()
    {
        $this->update(['status' => 'archived']);
    }
}
