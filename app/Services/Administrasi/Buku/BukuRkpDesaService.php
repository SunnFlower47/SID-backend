<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuRkpDesaService implements BukuAdministrasiInterface
{
    /**
     * Buku Rencana Kerja Pembangunan (RKP) Desa (C.7)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\ProyekDesa::query()
            ->with('apbdes')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $tahun = $filters['tahun'];
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        } else {
            // Default to current year if no year filter is provided
            $tahun = date('Y');
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_proyek', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuRkpDesaService.");
    }

}
