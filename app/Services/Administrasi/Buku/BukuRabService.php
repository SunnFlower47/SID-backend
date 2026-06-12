<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuRabService implements BukuAdministrasiInterface
{
    /**
     * Buku Rencana Anggaran Biaya (C.2)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\Apbdes::query()
            ->with('rincians')
            ->orderBy('kode_rekening', 'asc');

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('kode_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('nama_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('kegiatan', 'like', "%{$filters['search']}%")
                  ->orWhereHas('rincians', function($qr) use ($filters) {
                      $qr->where('uraian', 'like', "%{$filters['search']}%");
                  });
            });
        }

        // Normally RAB is mostly Belanja, but we can show all or filter if needed
        // $query->where('jenis', 'belanja');

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuRabService.");
    }

}
