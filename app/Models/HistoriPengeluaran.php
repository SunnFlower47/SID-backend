<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPengeluaran extends Model
{
    protected $fillable = [
        'nama_pengeluaran',
        'apbdes_id',
        'jumlah',
        'tanggal_pengeluaran',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal_pengeluaran' => 'date',
    ];

    /**
     * Get the APBDes that this expenditure belongs to
     */
    public function apbdes()
    {
        return $this->belongsTo(Apbdes::class);
    }

    /**
     * Get the user who created this expenditure
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
