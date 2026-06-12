<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKtpKkService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = $this->getQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku KTP dan KK (B.5)
     */
    public function getQuery(array $filters)
    {
        $query = \App\Models\Penduduk::with(['kartuKeluarga']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('kartuKeluarga', function($qKK) use ($search) {
                      $qKK->where('nkk', 'like', "%{$search}%");
                  });
            });
        }

        // Default sort by KK
        $query->leftJoin('kartu_keluargas', 'penduduks.kartu_keluarga_id', '=', 'kartu_keluargas.id')
              ->orderBy('kartu_keluargas.nkk')
              ->orderBy('penduduks.kedudukan_keluarga') // sorting by role in family generally
              ->select('penduduks.*'); // Select only penduduks to avoid id collision

        return $query;
    }
}
