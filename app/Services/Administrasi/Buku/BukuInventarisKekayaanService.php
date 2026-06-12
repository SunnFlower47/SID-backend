<?php

namespace App\Services\Administrasi\Buku;

use App\Contracts\BukuAdministrasiInterface;
use Illuminate\Support\Carbon;

class BukuInventarisKekayaanService implements BukuAdministrasiInterface
{
    /**
     * Buku Inventaris dan Kekayaan Desa (preview tabel - per page)
     */
    public function getData(array $filters, bool $isExport)
    {
        $query = \App\Models\AsetInventaris::query()
            ->with(['barang'])
            ->orderBy('tanggal_perolehan', 'desc');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->whereHas('barang', function($q2) use ($filters) {
                    $q2->where('nama_barang', 'like', "%{$filters['search']}%")
                      ->orWhere('kode_barang', 'like', "%{$filters['search']}%");
                })->orWhere('nama_barang_override', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    public function getInventarisKekayaanPdf(int $tahun): \Illuminate\Support\Collection
    {
        $inventaris = \App\Models\AsetInventaris::with(['barang', 'mutasis'])
            ->get();

        return $inventaris->map(function ($item) use ($tahun) {
            $mutasiAll   = $item->mutasis;

            // Saldo awal = akumulasi semua mutasi SEBELUM tahun ini
            $saldoAwalTambah = $mutasiAll->where('tahun', '<', $tahun)->where('jenis', 'tambah')->sum('kwantitas');
            $saldoAwalKurang = $mutasiAll->where('tahun', '<', $tahun)->where('jenis', 'kurang')->sum('kwantitas');
            $saldoAwal       = $saldoAwalTambah - $saldoAwalKurang;

            // Mutasi DALAM tahun ini
            $mutasiTahunIni = $mutasiAll->where('tahun', $tahun);
            $tambahTahunIni = $mutasiTahunIni->where('jenis', 'tambah')->sum('kwantitas');
            $kurangTahunIni = $mutasiTahunIni->where('jenis', 'kurang')->sum('kwantitas');

            // Penghapusan per alasan (kolom 10, 11, 12 Permendagri)
            $mutasiKurang = $mutasiTahunIni->where('jenis', 'kurang');
            $hapusRusak        = $mutasiKurang->where('alasan_kurang', 'rusak')->sum('kwantitas');
            $hapusDijual       = $mutasiKurang->where('alasan_kurang', 'dijual')->sum('kwantitas');
            $hapusDisumbangkan = $mutasiKurang->where('alasan_kurang', 'disumbangkan')->sum('kwantitas');
            $tglPenghapusan    = $mutasiKurang->sortByDesc('tanggal')->first()?->tanggal;

            // Saldo akhir
            $saldoAkhir = $saldoAwal + $tambahTahunIni - $kurangTahunIni;

            // Mapping asal_usul → 5 kolom Permendagri
            $asal = $item->asal_usul;

            return [
                'id'                  => $item->id,
                'nama_barang'         => $item->nama_barang_override ?: ($item->barang->nama_barang ?? '-'),
                'kode_barang'         => $item->barang->kode_barang ?? '-',
                'lokasi'              => $item->lokasi,
                'kondisi'             => $item->kondisi,
                'keterangan'          => $item->keterangan,
                // Kolom 3-7: Asal Barang
                'asal_dibeli'         => in_array($asal, ['APBDes']) ? $saldoAwal : 0,
                'asal_bantuan_pusat'  => $asal === 'Bantuan Pemerintah' ? $saldoAwal : 0,
                'asal_bantuan_prov'   => $asal === 'Bantuan Provinsi' ? $saldoAwal : 0,
                'asal_bantuan_kab'    => $asal === 'Bantuan Kabupaten' ? $saldoAwal : 0,
                'asal_sumbangan'      => in_array($asal, ['Hibah', 'Aset Asli Desa']) ? $saldoAwal : 0,
                // Kolom 8-9: Kondisi Awal Tahun
                'awal_baik'           => in_array($item->kondisi, ['baik']) ? $saldoAwal : 0,
                'awal_rusak'          => in_array($item->kondisi, ['rusak_ringan', 'rusak_berat']) ? $saldoAwal : 0,
                // Kolom 10-13: Penghapusan
                'hapus_rusak'         => $hapusRusak,
                'hapus_dijual'        => $hapusDijual,
                'hapus_disumbangkan'  => $hapusDisumbangkan,
                'tgl_penghapusan'     => $tglPenghapusan ? $tglPenghapusan->format('d/m/Y') : '-',
                // Kolom 14-15: Kondisi Akhir Tahun
                'akhir_baik'          => in_array($item->kondisi, ['baik']) ? $saldoAkhir : 0,
                'akhir_rusak'         => in_array($item->kondisi, ['rusak_ringan', 'rusak_berat']) ? $saldoAkhir : 0,
            ];
        })->filter(fn($row) => $row['awal_baik'] + $row['awal_rusak'] + $row['akhir_baik'] + $row['akhir_rusak'] > 0
            || $row['hapus_rusak'] + $row['hapus_dijual'] + $row['hapus_disumbangkan'] > 0
        )->values();
    }
    public function getQuery(array $filters)
    {
        throw new \Exception("Method getQuery() not implemented for BukuInventarisKekayaanService.");
    }
}
