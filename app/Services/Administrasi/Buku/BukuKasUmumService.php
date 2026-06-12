<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKasUmumService implements BukuAdministrasiInterface
{
    public function getData(array $filters, bool $isExport)
    {
        $tahun = $filters['tahun'] ?? date('Y');
        $search = $filters['search'] ?? null;

        // 1. Ambil data Penerimaan
        $penerimaanQuery = \App\Models\PenerimaanKas::with('apbdes')
            ->whereYear('tanggal_penerimaan', $tahun);
            
        if ($search) {
            $penerimaanQuery->where(function ($q) use ($search) {
                $q->where('uraian', 'like', "%{$search}%")
                  ->orWhere('no_bukti', 'like', "%{$search}%");
            });
        }
        
        $penerimaan = $penerimaanQuery->get()->map(function($item) {
            return (object) [
                'id' => 'masuk_' . $item->id,
                'tanggal' => $item->tanggal_penerimaan,
                'kode_rekening' => $item->apbdes ? $item->apbdes->kode_rekening : '',
                'nama_rekening' => $item->apbdes ? $item->apbdes->nama_rekening : '',
                'uraian' => $item->uraian,
                'penerimaan' => $item->jumlah,
                'pengeluaran' => 0,
                'no_bukti' => $item->no_bukti,
                // Extra fields for frontend
                'tanggal_pengeluaran' => $item->tanggal_penerimaan,
                'nama_pengeluaran' => $item->uraian,
                'nama_penerima' => $item->penyetor ?? '',
                'keterangan' => 'Penerimaan Kas',
                'jenis_bukti' => 'Penerimaan',
                'spj_status' => 'sudah',
                'apbdes' => $item->apbdes,
                'jumlah' => $item->jumlah, // Untuk display UI
            ];
        });

        // 2. Ambil data Pengeluaran
        $pengeluaranQuery = \App\Models\HistoriPengeluaran::with('apbdes')
            ->whereYear('tanggal_pengeluaran', $tahun);
            
        if ($search) {
            $pengeluaranQuery->where(function ($q) use ($search) {
                $q->where('nama_pengeluaran', 'like', "%{$search}%")
                  ->orWhere('no_bukti', 'like', "%{$search}%")
                  ->orWhere('nama_penerima', 'like', "%{$search}%");
            });
        }

        $pengeluaran = $pengeluaranQuery->get()->map(function($item) {
            return (object) [
                'id' => 'keluar_' . $item->id,
                'tanggal' => $item->tanggal_pengeluaran,
                'kode_rekening' => $item->apbdes ? $item->apbdes->kode_rekening : '',
                'nama_rekening' => $item->apbdes ? $item->apbdes->nama_rekening : '',
                'uraian' => $item->nama_pengeluaran,
                'penerimaan' => 0,
                'pengeluaran' => $item->jumlah,
                'no_bukti' => $item->no_bukti,
                // Extra fields for frontend
                'tanggal_pengeluaran' => $item->tanggal_pengeluaran,
                'nama_pengeluaran' => $item->nama_pengeluaran,
                'nama_penerima' => $item->nama_penerima,
                'keterangan' => $item->keterangan,
                'jenis_bukti' => $item->jenis_bukti,
                'spj_status' => $item->spj_status,
                'apbdes' => $item->apbdes,
                'jumlah' => $item->jumlah, // Untuk display UI
            ];
        });

        $combined = $penerimaan->concat($pengeluaran)->sortBy('tanggal')->values();
        
        $saldo = 0;
        $kumulatifPengeluaran = 0;
        foreach ($combined as $item) {
            $saldo += $item->penerimaan - $item->pengeluaran;
            $kumulatifPengeluaran += $item->pengeluaran;
            
            $item->saldo = $saldo;
            $item->kumulatif_pengeluaran = $kumulatifPengeluaran;
            $item->saldo_akumulasi = $saldo; // Alias
        }

        if (!$isExport) {
            $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
            $perPage = 50;
            $items = $combined->slice(($page - 1) * $perPage, $perPage)->values();
            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items, $combined->count(), $perPage, $page,
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
            );
        }

        return $combined;
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuKasUmumService.");
    }

}
