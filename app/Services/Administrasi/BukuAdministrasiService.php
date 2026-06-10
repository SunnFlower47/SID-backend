<?php

namespace App\Services\Administrasi;

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
            case 'buku-induk-penduduk':
                return $this->getBukuIndukPenduduk($filters, $isExport);
            case 'buku-mutasi-penduduk':
                return $this->getBukuMutasiPenduduk($filters, $isExport);
            case 'buku-rekapitulasi-penduduk':
                return $this->getBukuRekapitulasiPenduduk($filters, $isExport);
            case 'buku-penduduk-sementara':
                return $this->getBukuPendudukSementara($filters, $isExport);
            case 'buku-ktp-kk':
                return $this->getBukuKtpKk($filters, $isExport);
            case 'rkp-desa':
                return $this->getRkpDesa($filters, $isExport);
            case 'buku-kegiatan-pembangunan':
                return $this->getBukuKegiatanPembangunan($filters, $isExport);
            case 'buku-inventaris-pembangunan':
                return $this->getBukuInventarisPembangunan($filters, $isExport);
            case 'buku-apb-desa':
                return $this->getBukuApbDesa($filters, $isExport);
            case 'buku-rab':
                return $this->getBukuRab($filters, $isExport);
            case 'buku-kas-pembantu-kegiatan':
                return $this->getBukuKasPembantuKegiatan($filters, $isExport);
            case 'buku-kas-umum':
                return $this->getBukuKasUmum($filters, $isExport);
            case 'buku-kas-pembantu-pajak':
                return $this->getBukuKasPembantuPajak($filters, $isExport);
            case 'buku-bank-desa':
                return $this->getBukuBankDesa($filters, $isExport);
            case 'buku-ekspedisi':
                return $this->getBukuEkspedisi($filters, $isExport);
            case 'kader-pemberdayaan':
                return $this->getKaderPemberdayaan($filters, $isExport);
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

        if (!empty($filters['tahun'])) {
            $query->whereYear('tanggal_ditetapkan', $filters['tahun']);
        }

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

    public function getQuery(string $jenisBuku, array $filters = [])
    {
        if ($jenisBuku === 'buku-induk-penduduk') {
            return $this->getBukuIndukPendudukQuery($filters);
        }
        if ($jenisBuku === 'buku-mutasi-penduduk') {
            return $this->getBukuMutasiPendudukQuery($filters);
        }
        if ($jenisBuku === 'buku-penduduk-sementara') {
            return $this->getBukuPendudukSementaraQuery($filters);
        }
        if ($jenisBuku === 'buku-ktp-kk') {
            return $this->getBukuKtpKkQuery($filters);
        }
        throw new \InvalidArgumentException("Metode query untuk jenis buku {$jenisBuku} belum didukung.");
    }

    /**
     * Buku Mutasi Penduduk (B2) Query Builder
     */
    private function getBukuMutasiPendudukQuery(array $filters)
    {
        $query = \App\Models\Mutasi::with(['penduduk' => function($q) {
            $q->withTrashed();
        }])->whereIn('jenis_mutasi', ['pindah_masuk', 'pindah_keluar', 'kelahiran', 'kematian']);

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('tanggal_mutasi', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['start_date'])) {
            $query->where('tanggal_mutasi', '>=', $filters['start_date']);
        } elseif (!empty($filters['end_date'])) {
            $query->where('tanggal_mutasi', '<=', $filters['end_date']);
        }

        if (!empty($filters['search'])) {
            $query->whereHas('penduduk', function ($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('nik', 'like', "%{$filters['search']}%");
            });
        }

        return $query->orderBy('tanggal_mutasi', 'desc');
    }

    /**
     * Buku Induk Penduduk (B1) Query Builder
     */
    private function getBukuIndukPendudukQuery(array $filters)
    {
        $query = \App\Models\Penduduk::withWilayah()->with('kartuKeluarga');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nama', 'like', "%{$filters['search']}%")
                  ->orWhere('nik', 'like', "%{$filters['search']}%")
                  ->orWhereHas('kartuKeluarga', fn($sq) => $sq->where('nkk', 'like', "%{$filters['search']}%"));
            });
        }

        if (!empty($filters['rt_id']) && $filters['rt_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt_id', $filters['rt_id']));
        }
        if (!empty($filters['rw_id']) && $filters['rw_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw_id', $filters['rw_id']));
        }
        if (!empty($filters['jenis_kelamin']) && $filters['jenis_kelamin'] !== 'all') {
            $query->where('jenis_kelamin', $filters['jenis_kelamin']);
        }
        if (!empty($filters['dusun_id']) && $filters['dusun_id'] !== 'all') {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('dusun_id', $filters['dusun_id']));
        }
        if (!empty($filters['filter_umur']) && $filters['filter_umur'] !== 'all') {
            $filterUmur = $filters['filter_umur'];
            $today = \Carbon\Carbon::now();

            switch ($filterUmur) {
                case 'bayi':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(2));
                    break;
                case 'balita':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(5))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(2));
                    break;
                case 'anak':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(12))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(5));
                    break;
                case 'remaja':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(18))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(12));
                    break;
                case 'dewasa_muda':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(30))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(18));
                    break;
                case 'dewasa':
                    $query->where('tanggal_lahir', '>=', $today->copy()->subYears(60))
                          ->where('tanggal_lahir', '<', $today->copy()->subYears(30));
                    break;
                case 'lansia':
                    $query->where('tanggal_lahir', '<=', $today->copy()->subYears(60));
                    break;
            }
        }

        // Ordered by KK so families are grouped together
        $query->orderBy('kartu_keluarga_id')
              ->orderByRaw("CASE
                  WHEN kedudukan_keluarga = 'Kepala Keluarga' THEN 1
                  WHEN kedudukan_keluarga = 'Istri' THEN 2
                  WHEN kedudukan_keluarga = 'Anak' THEN 3
                  ELSE 9
              END")
              ->orderBy('tanggal_lahir', 'asc');
              
        return $query;
    }

    /**
     * Buku Induk Penduduk (B1)
     */
    private function getBukuIndukPenduduk(array $filters, bool $isExport)
    {
        $query = $this->getBukuIndukPendudukQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    private function getBukuMutasiPenduduk(array $filters, bool $isExport)
    {
        $query = $this->getBukuMutasiPendudukQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Rekapitulasi Jumlah Penduduk (B.3)
     */
    private function getBukuRekapitulasiPenduduk(array $filters, bool $isExport)
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

    /**
     * Buku Penduduk Sementara (B.4)
     */
    private function getBukuPendudukSementaraQuery(array $filters)
    {
        $query = \App\Models\PendudukDomisili::query();

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('tanggal_masuk', [$filters['start_date'], $filters['end_date']]);
        }
        
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('asal_daerah', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('tanggal_masuk', 'asc');
    }

    private function getBukuPendudukSementara(array $filters, bool $isExport)
    {
        $query = $this->getBukuPendudukSementaraQuery($filters);
        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku KTP dan KK (B.5)
     */
    private function getBukuKtpKkQuery(array $filters)
    {
        $query = \App\Models\Penduduk::with(['kartuKeluarga']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('kartuKeluarga', function($qKK) use ($search) {
                      $qKK->where('nkk', 'like', "%{$search}%");
                  });
            });
        }

        // Default sort by KK
        $query->leftJoin('kartu_keluargas', 'penduduks.kartu_keluarga_id', '=', 'kartu_keluargas.id')
              ->orderBy('kartu_keluargas.nkk')
              ->orderBy('penduduks.kedudukan_keluarga') // sorting by role in family generally
              ->select('penduduks.*'); // Select only penduduks to avoid id collision

        return $query;
    }

    private function getBukuKtpKk(array $filters, bool $isExport)
    {
        $query = $this->getBukuKtpKkQuery($filters);
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

        if (!empty($filters['tahun'])) {
            $query->whereYear('tanggal_ditetapkan', $filters['tahun']);
        }

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

    /**
     * Buku Rencana Kerja Pembangunan (RKP) Desa (C.7)
     */
    private function getRkpDesa(array $filters, bool $isExport)
    {
        $query = \App\Models\ProyekDesa::query()
            ->with('apbdes')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $tahun = $filters['tahun'];
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        } else {
            // Default to current year if no year filter is provided
            $tahun = date('Y');
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_proyek', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Kegiatan Pembangunan (C.8)
     */
    private function getBukuKegiatanPembangunan(array $filters, bool $isExport)
    {
        $query = \App\Models\ProyekDesa::query()
            ->with('apbdes')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $tahun = $filters['tahun'];
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        } else {
            // Default to current year
            $tahun = date('Y');
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_proyek', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Inventaris Hasil Pembangunan (C.9)
     */
    private function getBukuInventarisPembangunan(array $filters, bool $isExport)
    {
        $query = \App\Models\ProyekDesa::query()
            ->with('apbdes')
            ->where('status', 'selesai')
            ->orderBy('created_at', 'desc');

        if (!empty($filters['tahun'])) {
            $tahun = $filters['tahun'];
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        } else {
            // Default to current year
            $tahun = date('Y');
            $query->where(function($q) use ($tahun) {
                $q->whereHas('apbdes', function($sq) use ($tahun) {
                    $sq->where('tahun', $tahun);
                })->orWhereYear('tanggal_mulai', $tahun);
            });
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_proyek', 'like', "%{$filters['search']}%")
                  ->orWhere('lokasi', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Rencana Anggaran Biaya (C.2)
     */
    private function getBukuRab(array $filters, bool $isExport)
    {
        $query = \App\Models\Apbdes::query()
            ->with('rincians')
            ->orderBy('kode_rekening', 'asc');

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('kode_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('nama_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('kegiatan', 'like', "%{$filters['search']}%")
                  ->orWhereHas('rincians', function($qr) use ($filters) {
                      $qr->where('uraian', 'like', "%{$filters['search']}%");
                  });
            });
        }

        // Normally RAB is mostly Belanja, but we can show all or filter if needed
        // $query->where('jenis', 'belanja');

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku APB Desa (C.1)
     */
    private function getBukuApbDesa(array $filters, bool $isExport)
    {
        $query = \App\Models\Apbdes::query()
            ->orderBy('kode_rekening', 'asc');

        if (!empty($filters['tahun'])) {
            $query->where('tahun', $filters['tahun']);
        }

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('kode_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('nama_rekening', 'like', "%{$filters['search']}%")
                  ->orWhere('kegiatan', 'like', "%{$filters['search']}%");
            });
        }

        return $isExport ? $query->get() : $query->paginate(20)->withQueryString();
    }

    /**
     * Buku Kas Pembantu Kegiatan (C.3)
     */
    private function getBukuKasPembantuKegiatan(array $filters, bool $isExport)
    {
        $query = \App\Models\HistoriPengeluaran::query()
            ->with(['apbdes'])
            ->orderBy('tanggal_pengeluaran', 'asc')
            ->orderBy('id', 'asc');

        if (!empty($filters['apbdes_id'])) {
            $query->where('apbdes_id', $filters['apbdes_id']);
        } else {
            // Default to no data or require user to select an activity first
            // Usually we require filtering by apbdes_id for C.3
            if (!$isExport) {
                // If not export, return empty paginator if no kegiatan selected
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
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

    /**
     * Buku Kas Pembantu Pajak (C.5)
     */
    private function getBukuKasPembantuPajak(array $filters, bool $isExport)
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

    private function getBukuBankDesa(array $filters, bool $isExport)
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

    private function getBukuEkspedisi(array $filters, bool $isExport)
    {
        $query = \App\Models\BukuEkspedisi::query()->orderBy('tanggal_pengiriman', 'desc');
        
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $startDate = \Carbon\Carbon::parse($filters['start_date'])->startOfDay();
            $endDate = \Carbon\Carbon::parse($filters['end_date'])->endOfDay();
            $query->whereBetween('tanggal_pengiriman', [$startDate, $endDate]);
        }

        return $isExport ? $query->get() : $query->paginate(50)->withQueryString();
    }

    private function getKaderPemberdayaan(array $filters, bool $isExport)
    {
        $query = \App\Models\KaderPemberdayaan::query()->orderBy('nama', 'asc');
        return $isExport ? $query->get() : $query->paginate(50)->withQueryString();
    }


    private function getBukuKasUmum(array $filters, bool $isExport)
    {
        $tahun = $filters['tahun'] ?? date('Y');
        
        $penerimaan = \App\Models\PenerimaanKas::whereYear('tanggal_penerimaan', $tahun)->get()->map(function($item) {
            return (object) [
                'tanggal' => $item->tanggal_penerimaan,
                'kode_rekening' => $item->apbdes ? $item->apbdes->kode_rekening : '',
                'uraian' => $item->uraian,
                'penerimaan' => $item->jumlah,
                'pengeluaran' => 0,
                'no_bukti' => $item->no_bukti,
            ];
        });

        $pengeluaran = \App\Models\HistoriPengeluaran::whereYear('tanggal_pengeluaran', $tahun)
            ->whereIn('jenis_transaksi', ['spp', 'kwitansi', 'pencairan_panjar'])
            ->get()->map(function($item) {
            return (object) [
                'tanggal' => $item->tanggal_pengeluaran,
                'kode_rekening' => $item->apbdes ? $item->apbdes->kode_rekening : '',
                'uraian' => $item->nama_pengeluaran,
                'penerimaan' => 0,
                'pengeluaran' => $item->jumlah,
                'no_bukti' => $item->no_spp,
            ];
        });

        $combined = $penerimaan->concat($pengeluaran)->sortBy('tanggal')->values();
        
        $saldo = 0;
        foreach ($combined as $item) {
            $saldo += $item->penerimaan - $item->pengeluaran;
            $item->saldo = $saldo;
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
}
