<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKeputusanKadesService implements BukuAdministrasiInterface
{
    /**
     * Buku Keputusan Kepala Desa
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\KeputusanKades::query()
            ->with('author:id,name')
            ->orderBy('tanggal_ditetapkan', 'desc');

        if (!empty($filters['tahun'])) {
            $query->whereYear('tanggal_ditetapkan', $filters['tahun']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_ditetapkan', [$startDate, $endDate]);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('judul_keputusan', 'like', "%{$filters['search']}%")
                  ->orWhere('nomor_keputusan', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuKeputusanKadesService.");
    }

}
