<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuMutasiPendudukService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = $this->getQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Mutasi Penduduk (B2) Query Builder
     */
    public function getQuery(array $filters)
    {
        $query = \App\Models\Mutasi::with(['penduduk' => function($q) {
            $q->withTrashed();
        }])->whereIn('jenis_mutasi', ['pindah_masuk', 'pindah_keluar', 'kelahiran', 'kematian']);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('tanggal_mutasi', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['start_date'])) {
            $query->where('tanggal_mutasi', '>=', $filters['start_date']);
        } elseif (!empty($filters['end_date'])) {
            $query->where('tanggal_mutasi', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('penduduk', function ($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('tanggal_mutasi', 'desc');
    }
}
