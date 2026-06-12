<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuAparatPemerintahService implements BukuAdministrasiInterface
{
    /**
     * Buku Aparat Pemerintah Desa
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\StrukturDesa::query()
            ->orderBy('urutan', 'asc')
            ->orderBy('tanggal_pengangkatan', 'desc');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_pengangkatan', [$startDate, $endDate]);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('jabatan', 'like', "%{$filters['search']}%")
                  ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuAparatPemerintahService.");
    }

}
