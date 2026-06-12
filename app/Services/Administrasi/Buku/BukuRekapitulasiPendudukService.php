<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuRekapitulasiPendudukService implements BukuAdministrasiInterface
{
    /**
     * Buku Rekapitulasi Jumlah Penduduk (B.3)
     */
    public function getData(array $filters, bool $isExport)
    {
        $bulan = $filters['bulan'] ?? \Carbon\Carbon::now()->month;
        $tahun = $filters['tahun'] ?? \Carbon\Carbon::now()->year;

        $startOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endOfMonth = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $dusuns = \App\Models\Dusun::orderBy('nama')->get();
        $rekap = [];

        foreach ($dusuns as $d) {
            $rekap[$d->id] = [
                'dusun_id' => $d->id,
                'nama_dusun' => $d->nama,
                'awal_wni_l' => 0, 'awal_wni_p' => 0, 'awal_wna_l' => 0, 'awal_wna_p' => 0,
                'tambah_lahir_l' => 0, 'tambah_lahir_p' => 0, 'tambah_datang_l' => 0, 'tambah_datang_p' => 0,
                'kurang_mati_l' => 0, 'kurang_mati_p' => 0, 'kurang_pindah_l' => 0, 'kurang_pindah_p' => 0,
                'akhir_wni_l' => 0, 'akhir_wni_p' => 0, 'akhir_wna_l' => 0, 'akhir_wna_p' => 0,
            ];
        }

        $rekap['unknown'] = [
            'dusun_id' => 'unknown',
            'nama_dusun' => 'Tanpa Dusun / Belum Diketahui',
            'awal_wni_l' => 0, 'awal_wni_p' => 0, 'awal_wna_l' => 0, 'awal_wna_p' => 0,
            'tambah_lahir_l' => 0, 'tambah_lahir_p' => 0, 'tambah_datang_l' => 0, 'tambah_datang_p' => 0,
            'kurang_mati_l' => 0, 'kurang_mati_p' => 0, 'kurang_pindah_l' => 0, 'kurang_pindah_p' => 0,
            'akhir_wni_l' => 0, 'akhir_wni_p' => 0, 'akhir_wna_l' => 0, 'akhir_wna_p' => 0,
        ];

        $penduduks = \App\Models\Penduduk::withTrashed()
            ->with(['mutasis' => function($q) use ($endOfMonth) {
                $q->where('tanggal_mutasi', '<=', $endOfMonth);
            }, 'kartuKeluarga'])
            ->get();

        foreach ($penduduks as $p) {
            $dusun_id = $p->kartuKeluarga && $p->kartuKeluarga->dusun_id ? $p->kartuKeluarga->dusun_id : 'unknown';
            if (!isset($rekap[$dusun_id])) continue;
            
            $isL = strtoupper($p->jenis_kelamin) == 'LAKI-LAKI' || strtoupper($p->jenis_kelamin) == 'L';
            $isWni = true; 
            if (isset($p->warganegara) && strtoupper($p->warganegara) == 'WNA') {
                $isWni = false;
            }

            $entryDate = $p->created_at ? clone $p->created_at : \Carbon\Carbon::now();
            $entryMutasi = $p->mutasis->whereIn('jenis_mutasi', ['kelahiran', 'pindah_masuk'])->sortBy('tanggal_mutasi')->first();
            if ($entryMutasi && \Carbon\Carbon::parse($entryMutasi->tanggal_mutasi)->lt($entryDate)) {
                $entryDate = \Carbon\Carbon::parse($entryMutasi->tanggal_mutasi);
            }
            
            $exitMutasi = $p->mutasis->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])->sortByDesc('tanggal_mutasi')->first();

            $activeAtEnd = false;
            if ($entryDate->lte($endOfMonth)) {
                if (!$exitMutasi || \Carbon\Carbon::parse($exitMutasi->tanggal_mutasi)->gt($endOfMonth)) {
                    $activeAtEnd = true;
                }
            }

            $lahir = $p->mutasis->where('jenis_mutasi', 'kelahiran')->where('tanggal_mutasi', '>=', $startOfMonth)->first();
            $datang = $p->mutasis->where('jenis_mutasi', 'pindah_masuk')->where('tanggal_mutasi', '>=', $startOfMonth)->first();
            $mati = $p->mutasis->where('jenis_mutasi', 'kematian')->where('tanggal_mutasi', '>=', $startOfMonth)->first();
            $pindah = $p->mutasis->where('jenis_mutasi', 'pindah_keluar')->where('tanggal_mutasi', '>=', $startOfMonth)->first();

            $tambah_lahir = $lahir ? 1 : 0;
            $tambah_datang = (!$lahir && $datang) ? 1 : 0;
            
            if (!$tambah_lahir && !$tambah_datang && $entryDate->between($startOfMonth, $endOfMonth)) {
                $tambah_datang = 1;
            }

            $kurang_mati = $mati ? 1 : 0;
            $kurang_pindah = (!$mati && $pindah) ? 1 : 0;

            $akhir = $activeAtEnd ? 1 : 0;
            $awal = $akhir - ($tambah_lahir + $tambah_datang) + ($kurang_mati + $kurang_pindah);
            
            if ($isWni) {
                if ($isL) {
                    $rekap[$dusun_id]['akhir_wni_l'] += $akhir;
                    $rekap[$dusun_id]['awal_wni_l'] += $awal;
                    $rekap[$dusun_id]['tambah_lahir_l'] += $tambah_lahir;
                    $rekap[$dusun_id]['tambah_datang_l'] += $tambah_datang;
                    $rekap[$dusun_id]['kurang_mati_l'] += $kurang_mati;
                    $rekap[$dusun_id]['kurang_pindah_l'] += $kurang_pindah;
                } else {
                    $rekap[$dusun_id]['akhir_wni_p'] += $akhir;
                    $rekap[$dusun_id]['awal_wni_p'] += $awal;
                    $rekap[$dusun_id]['tambah_lahir_p'] += $tambah_lahir;
                    $rekap[$dusun_id]['tambah_datang_p'] += $tambah_datang;
                    $rekap[$dusun_id]['kurang_mati_p'] += $kurang_mati;
                    $rekap[$dusun_id]['kurang_pindah_p'] += $kurang_pindah;
                }
            } else {
                if ($isL) {
                    $rekap[$dusun_id]['akhir_wna_l'] += $akhir;
                    $rekap[$dusun_id]['awal_wna_l'] += $awal;
                    $rekap[$dusun_id]['tambah_lahir_l'] += $tambah_lahir;
                    $rekap[$dusun_id]['tambah_datang_l'] += $tambah_datang;
                    $rekap[$dusun_id]['kurang_mati_l'] += $kurang_mati;
                    $rekap[$dusun_id]['kurang_pindah_l'] += $kurang_pindah;
                } else {
                    $rekap[$dusun_id]['akhir_wna_p'] += $akhir;
                    $rekap[$dusun_id]['awal_wna_p'] += $awal;
                    $rekap[$dusun_id]['tambah_lahir_p'] += $tambah_lahir;
                    $rekap[$dusun_id]['tambah_datang_p'] += $tambah_datang;
                    $rekap[$dusun_id]['kurang_mati_p'] += $kurang_mati;
                    $rekap[$dusun_id]['kurang_pindah_p'] += $kurang_pindah;
                }
            }
        }

        if ($rekap['unknown']['awal_wni_l'] == 0 && $rekap['unknown']['akhir_wni_l'] == 0 
            && $rekap['unknown']['awal_wni_p'] == 0 && $rekap['unknown']['akhir_wni_p'] == 0) {
            unset($rekap['unknown']);
        }

        // Return length-aware paginator wrapper to match the view's expectation, or simply a generic array wrapper
        // But the frontend expects a pagination object `data` array usually.
        // Let's just return a manual simple paginator format
        $values = array_values($rekap);
        
        if ($isExport) {
            return $values;
        }

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $values,
            count($values),
            count($values) > 0 ? count($values) : 1,
            1,
            ['path' => \Illuminate\Support\Facades\Request::url(), 'query' => \Illuminate\Support\Facades\Request::query()]
        );
    }

    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuRekapitulasiPendudukService.");
    }

}
