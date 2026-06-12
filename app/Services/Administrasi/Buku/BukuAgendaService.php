<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuAgendaService implements BukuAdministrasiInterface
{
    /**
     * Buku Agenda (A4)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\BukuAgenda::query()
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal', [$startDate, $endDate]);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nomor_surat', 'like', "%{$filters['search']}%")
                  ->orWhere('pengirim_penerima', 'like', "%{$filters['search']}%")
                  ->orWhere('isi_singkat', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuAgendaService.");
    }

}
