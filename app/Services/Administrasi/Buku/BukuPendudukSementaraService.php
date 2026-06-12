<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuPendudukSementaraService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = $this->getQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Penduduk Sementara (B.4)
     */
    public function getQuery(array $filters)
    {
        $query = \App\Models\PendudukDomisili::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('tanggal_masuk', [$filters['start_date'], $filters['end_date']]);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('asal_daerah', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('tanggal_masuk', 'asc');
    }
}
