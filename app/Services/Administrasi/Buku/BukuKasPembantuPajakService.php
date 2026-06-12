<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuKasPembantuPajakService implements BukuAdministrasiInterface
{
    /**
     * Buku Kas Pembantu Pajak (C.5)
     */
    public function getData(array $filters, bool $isExport)
    {
        $tahun = $filters['tahun'] ?? date('Y');

        $pengeluarans = \App\Models\HistoriPengeluaran::where(function($query) use ($tahun) {
                $query->whereYear('tanggal_pengeluaran', $tahun)
                      ->orWhereYear('tanggal_setor_pajak', $tahun);
            })
            ->where(function($q) {
                $q->where('pajak_ppn', '>', 0)
                  ->orWhere('pajak_pph21', '>', 0)
                  ->orWhere('pajak_pph22', '>', 0)
                  ->orWhere('pajak_pph23', '>', 0);
            })
            ->get();

        $rows = collect();

        foreach ($pengeluarans as $p) {
            // Pemotongan
            if (\Carbon\Carbon::parse($p->tanggal_pengeluaran)->year == $tahun) {
                $rows->push((object)[
                    'tanggal' => $p->tanggal_pengeluaran,
                    'uraian' => 'Pemotongan Pajak: ' . $p->nama_pengeluaran,
                    'pemotongan_ppn' => $p->pajak_ppn,
                    'pemotongan_pph21' => $p->pajak_pph21,
                    'pemotongan_pph22' => $p->pajak_pph22,
                    'pemotongan_pph23' => $p->pajak_pph23,
                    'penyetoran_ppn' => 0,
                    'penyetoran_pph21' => 0,
                    'penyetoran_pph22' => 0,
                    'penyetoran_pph23' => 0,
                ]);
            }

            // Penyetoran
            if ($p->tanggal_setor_pajak && \Carbon\Carbon::parse($p->tanggal_setor_pajak)->year == $tahun) {
                $rows->push((object)[
                    'tanggal' => $p->tanggal_setor_pajak,
                    'uraian' => 'Penyetoran Pajak: ' . $p->nama_pengeluaran,
                    'pemotongan_ppn' => 0,
                    'pemotongan_pph21' => 0,
                    'pemotongan_pph22' => 0,
                    'pemotongan_pph23' => 0,
                    'penyetoran_ppn' => $p->pajak_ppn,
                    'penyetoran_pph21' => $p->pajak_pph21,
                    'penyetoran_pph22' => $p->pajak_pph22,
                    'penyetoran_pph23' => $p->pajak_pph23,
                ]);
            }
        }

        $sorted = $rows->sortBy('tanggal')->values();

        $saldo = 0;
        foreach ($sorted as $r) {
            $pemotongan = $r->pemotongan_ppn + $r->pemotongan_pph21 + $r->pemotongan_pph22 + $r->pemotongan_pph23;
            $penyetoran = $r->penyetoran_ppn + $r->penyetoran_pph21 + $r->penyetoran_pph22 + $r->penyetoran_pph23;
            
            $saldo += $pemotongan - $penyetoran;
            $r->saldo = $saldo;
        }

        return $sorted;
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuKasPembantuPajakService.");
    }

}
