<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuTanahDiDesaService implements BukuAdministrasiInterface
{
    /**
     * Buku Tanah di Desa (A7)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\TanahDiDesa::query()->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $query->whereYear('created_at', $filters['tahun']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('nama_pemilik', 'like', "%{$filters['search']}%")
                  ->orWhere('keterangan', 'like', "%{$filters['search']}%");
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuTanahDiDesaService.");
    }

}
