<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyekDesa extends Model
{

    protected $fillable = [
        'nama_proyek',
        'deskripsi',
        'jenis',
        'anggaran',
        'realisasi',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'progress',
        'lokasi',
        'penanggung_jawab',
        'kontraktor',
        'dokumentasi',
        'catatan',
        'apbdes_id',
    ];

    protected $casts = [
        'anggaran' => 'decimal:2',
        'realisasi' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'progress' => 'integer',
        'dokumentasi' => 'array',
    ];

    /**
     * Get the jenis label
     */
    public function getJenisLabelAttribute()
    {
        $labels = [
            'infrastruktur' => 'Infrastruktur',
            'sosial' => 'Sosial',
            'ekonomi' => 'Ekonomi',
            'lingkungan' => 'Lingkungan',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis] ?? $this->jenis;
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'perencanaan' => 'Perencanaan',
            'pelaksanaan' => 'Pelaksanaan',
            'selesai' => 'Selesai',
            'tertunda' => 'Tertunda',
            'dibatalkan' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get the status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'perencanaan' => 'info',
            'pelaksanaan' => 'warning',
            'selesai' => 'success',
            'tertunda' => 'secondary',
            'dibatalkan' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get the percentage of realization
     */
    public function getPersentaseRealisasiAttribute()
    {
        if ($this->anggaran == 0) return 0;
        return round(($this->realisasi / $this->anggaran) * 100, 2);
    }

    /**
     * Get the progress bar color
     */
    public function getProgressColorAttribute()
    {
        if ($this->progress < 25) return 'danger';
        if ($this->progress < 50) return 'warning';
        if ($this->progress < 75) return 'info';
        return 'success';
    }

    /**
     * Scope for specific jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Scope for specific status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for active projects
     */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['perencanaan', 'pelaksanaan']);
    }

    /**
     * Get the APBDes that this project is linked to
     */
    public function apbdes()
    {
        return $this->belongsTo(Apbdes::class);
    }
}
