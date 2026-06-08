<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HistoriPengeluaran extends Model
{
    protected $fillable = [
        'nama_pengeluaran',
        'apbdes_id',
        'jumlah',
        'tanggal_pengeluaran',
        'keterangan',
        'user_id',
        // Dokumen pendukung (Permendagri 20/2018)
        'no_bukti',
        'jenis_bukti',
        'file_bukti',
        'nama_file_bukti',
        'spj_status',
        'pajak_ppn',
        'pajak_pph21',
        'pajak_pph22',
        'pajak_pph23',
        'tanggal_setor_pajak',
    ];

    protected $casts = [
        'jumlah'              => 'decimal:2',
        'tanggal_pengeluaran' => 'date',
        'pajak_ppn'           => 'decimal:2',
        'pajak_pph21'         => 'decimal:2',
        'pajak_pph22'         => 'decimal:2',
        'pajak_pph23'         => 'decimal:2',
        'tanggal_setor_pajak' => 'date',
    ];

    /**
     * Jenis bukti yang tersedia
     */
    const JENIS_BUKTI = [
        'kwitansi' => 'Kwitansi',
        'nota'     => 'Nota/Faktur',
        'spj'      => 'SPJ (Surat Pertanggungjawaban)',
        'transfer' => 'Bukti Transfer',
        'lainnya'  => 'Lainnya',
    ];

    /**
     * Generate nomor bukti otomatis: BKT-{tahun}-{sequence}
     */
    public static function generateNoBukti(int $tahun): string
    {
        $last = static::whereYear('tanggal_pengeluaran', $tahun)
            ->whereNotNull('no_bukti')
            ->orderByDesc('id')
            ->value('no_bukti');

        $seq = 1;
        if ($last && preg_match('/BKT-\d{4}-(\d+)/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return sprintf('BKT-%d-%04d', $tahun, $seq);
    }

    /**
     * Get the full URL for the bukti file
     */
    public function getFileBuktiUrlAttribute(): ?string
    {
        return $this->file_bukti
            ? Storage::url($this->file_bukti)
            : null;
    }

    /**
     * Get human-readable jenis bukti label
     */
    public function getJenisBuktiLabelAttribute(): string
    {
        return self::JENIS_BUKTI[$this->jenis_bukti] ?? $this->jenis_bukti;
    }

    /**
     * Check if this expenditure has a document attached
     */
    public function getHasDokumenAttribute(): bool
    {
        return !empty($this->file_bukti);
    }

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
