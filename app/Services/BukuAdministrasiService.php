<?php

namespace App\Services;

use App\Models\KeputusanKades;
use Illuminate\Support\Carbon;

class BukuAdministrasiService
{
    /**
     * Get data based on jenis_buku
     */
    public function getData(string $jenisBuku, array $filters = [], bool $isExport = false)
    {
        switch ($jenisBuku) {
            case 'keputusan-kades':
                return $this->getKeputusanKades($filters, $isExport);
            case 'peraturan-desa':
                return $this->getPeraturanDesa($filters, $isExport);
            case 'inventaris-kekayaan':
                return $this->getInventarisKekayaan($filters, $isExport);
            case 'aparat-pemerintah':
                return $this->getAparatPemerintah($filters, $isExport);
            case 'tanah-kas-desa':
                return $this->getTanahKasDesa($filters, $isExport);
            case 'buku-agenda':
                return $this->getBukuAgenda($filters, $isExport);
            case 'tanah-di-desa':
                return $this->getTanahDiDesa($filters, $isExport);
            default:
                throw new \InvalidArgumentException("Jenis buku tidak dikenal: {$jenisBuku}");
        }
    }

    /**
     * Buku Peraturan di Desa
     */
    private function getPeraturanDesa(array $filters, bool $isExport)
    {
        $query = \App\Models\PeraturanDesa::query()
            ->orderBy('tanggal_ditetapkan', 'desc');

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_ditetapkan', [$startDate, $endDate]);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('judul', 'like', "%{$filters['search']}%")
                  ->orWhere('nomor_peraturan', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Agenda (A4)
     */
    private function getBukuAgenda(array $filters, bool $isExport)
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

    /**
     * Buku Tanah di Desa (A7)
     */
    private function getTanahDiDesa(array $filters, bool $isExport)
    {
        $query = \App\Models\TanahDiDesa::query()->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $query->whereYear('created_at', $filters['tahun']);
        }
        
        if (!empty($filters['search'])) {
            $query->where('nama_pemilik', 'like', "%{$filters['search']}%")
                  ->orWhere('keterangan', 'like', "%{$filters['search']}%");
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Inventaris dan Kekayaan Desa (preview tabel - per page)
     */
    private function getInventarisKekayaan(array $filters, bool $isExport)
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

    /**
     * Buku Inventaris Kekayaan Desa untuk ekspor PDF Permendagri 47/2016
     * Menghitung saldo awal tahun, mutasi, dan saldo akhir tahun per aset.
     */
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

/**
     * Buku Aparat Pemerintah Desa
     */
    private function getAparatPemerintah(array $filters, bool $isExport)
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

    /**
     * Buku Keputusan Kepala Desa
     */
    private function getKeputusanKades(array $filters, bool $isExport)
    {
        $query = KeputusanKades::query()
            ->with('author:id,name')
            ->orderBy('tanggal_ditetapkan', 'desc');

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

    /**
     * Buku Tanah Kas Desa (A6)
     * Filter aset_inventaris dengan kode barang golongan 2 (Tanah)
     */
    private function getTanahKasDesa(array $filters, bool $isExport)
    {
        $query = \App\Models\AsetInventaris::query()
            ->with(['barang', 'mutasis'])          // mutasis wajib untuk saldo_kwantitas accessor
            ->whereHas('barang', fn($q) => $q->where('kode_barang', 'like', '2.%'))
            ->orderBy('tanggal_perolehan', 'asc');

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_barang_override', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%")
                  ->orWhereHas('barang', fn($q2) => $q2->where('nama_barang', 'like', "%{$filters['search']}%"));
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }
}
