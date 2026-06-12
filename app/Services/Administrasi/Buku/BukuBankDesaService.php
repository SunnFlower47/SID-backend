<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuBankDesaService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\MutasiBank::query()->orderBy('tanggal_mutasi', 'asc')->orderBy('id', 'asc');
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_mutasi', [$startDate, $endDate]);
        }

        $items = $isExport ? $query->get() : $query->paginate(50)->withQueryString();

        $saldo = 0;
        foreach ($items as $item) {
            $penerimaan = $item->jenis_mutasi === 'masuk' ? $item->jumlah : 0;
            $pengeluaran = $item->jenis_mutasi === 'keluar' ? $item->jumlah : 0;
            $saldo += $penerimaan - $pengeluaran;
            $item->pemasukan = $penerimaan;
            $item->pengeluaran = $pengeluaran;
            $item->saldo = $saldo;
        }

        return $items;
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuBankDesaService.");
    }

}
