<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKasPembantuKegiatanService implements BukuAdministrasiInterface
{
    /**
     * Buku Kas Pembantu Kegiatan (C.3)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\HistoriPengeluaran::query()
            ->with(['apbdes'])
            ->orderBy('tanggal_pengeluaran', 'asc')
            ->orderBy('id', 'asc');

        if (!empty($filters['apbdes_id'])) {
            $query->where('apbdes_id', $filters['apbdes_id']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
        } elseif (!empty($filters['tahun'])) {
            $query->whereYear('tanggal_pengeluaran', $filters['tahun']);
        }

        $items = $isExport ? $query->get() : $query->paginate(50)->withQueryString();

        // Calculate cumulative saldo
        $saldo = 0;
        foreach ($items as $item) {
            $penerimaan = $item->jenis_transaksi === 'pencairan_panjar' ? $item->jumlah : 0;
            $pengeluaran = $item->jenis_transaksi !== 'pencairan_panjar' ? $item->jumlah : 0;
            $saldo = $saldo + $penerimaan - $pengeluaran;
            
            $item->penerimaan = $penerimaan;
            $item->pengeluaran = $pengeluaran;
            $item->saldo = $saldo;
        }

        return $items;
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuKasPembantuKegiatanService.");
    }

}
