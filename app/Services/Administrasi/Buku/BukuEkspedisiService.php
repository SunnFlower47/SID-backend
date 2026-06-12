<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuEkspedisiService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\BukuEkspedisi::query()->orderBy('tanggal_pengiriman', 'desc');
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_pengiriman', [$startDate, $endDate]);
        }

        return $isExport ? $query->get() : $query->paginate(50)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuEkspedisiService.");
    }

}
