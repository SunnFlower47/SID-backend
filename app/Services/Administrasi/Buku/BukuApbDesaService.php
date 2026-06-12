<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuApbDesaService implements BukuAdministrasiInterface
{
    /**
     * Buku APB Desa (C.1)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\Apbdes::query()
            ->orderBy('kode_rekening', 'asc');

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('kode_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('nama_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('kegiatan', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuApbDesaService.");
    }

}
