<?php

namespace App\Services\Administrasi;

use App\Contracts\BukuAdministrasiInterface;
use App\Services\Administrasi\Buku\BukuKeputusanKadesService;
use App\Services\Administrasi\Buku\BukuPeraturanDesaService;
use App\Services\Administrasi\Buku\BukuInventarisKekayaanService;
use App\Services\Administrasi\Buku\BukuAparatPemerintahService;
use App\Services\Administrasi\Buku\BukuTanahKasDesaService;
use App\Services\Administrasi\Buku\BukuAgendaService;
use App\Services\Administrasi\Buku\BukuTanahDiDesaService;
use App\Services\Administrasi\Buku\BukuIndukPendudukService;
use App\Services\Administrasi\Buku\BukuMutasiPendudukService;
use App\Services\Administrasi\Buku\BukuRekapitulasiPendudukService;
use App\Services\Administrasi\Buku\BukuPendudukSementaraService;
use App\Services\Administrasi\Buku\BukuKtpKkService;
use App\Services\Administrasi\Buku\BukuRkpDesaService;
use App\Services\Administrasi\Buku\BukuKegiatanPembangunanService;
use App\Services\Administrasi\Buku\BukuInventarisPembangunanService;
use App\Services\Administrasi\Buku\BukuApbDesaService;
use App\Services\Administrasi\Buku\BukuRabService;
use App\Services\Administrasi\Buku\BukuKasPembantuKegiatanService;
use App\Services\Administrasi\Buku\BukuKasUmumService;
use App\Services\Administrasi\Buku\BukuKasPembantuPajakService;
use App\Services\Administrasi\Buku\BukuBankDesaService;
use App\Services\Administrasi\Buku\BukuEkspedisiService;
use App\Services\Administrasi\Buku\BukuKaderPemberdayaanService;

class BukuAdministrasiService
{
    /**
     * Map jenis_buku to its corresponding service class.
     * 
     * @var array
     */
    protected $strategies = [
        'keputusan-kades' => BukuKeputusanKadesService::class,
        'peraturan-desa' => BukuPeraturanDesaService::class,
        'inventaris-kekayaan' => BukuInventarisKekayaanService::class,
        'aparat-pemerintah' => BukuAparatPemerintahService::class,
        'tanah-kas-desa' => BukuTanahKasDesaService::class,
        'buku-agenda' => BukuAgendaService::class,
        'tanah-di-desa' => BukuTanahDiDesaService::class,
        'buku-induk-penduduk' => BukuIndukPendudukService::class,
        'buku-mutasi-penduduk' => BukuMutasiPendudukService::class,
        'buku-rekapitulasi-penduduk' => BukuRekapitulasiPendudukService::class,
        'buku-penduduk-sementara' => BukuPendudukSementaraService::class,
        'buku-ktp-kk' => BukuKtpKkService::class,
        'rkp-desa' => BukuRkpDesaService::class,
        'buku-kegiatan-pembangunan' => BukuKegiatanPembangunanService::class,
        'buku-inventaris-pembangunan' => BukuInventarisPembangunanService::class,
        'buku-apb-desa' => BukuApbDesaService::class,
        'buku-rab' => BukuRabService::class,
        'buku-kas-pembantu-kegiatan' => BukuKasPembantuKegiatanService::class,
        'buku-kas-umum' => BukuKasUmumService::class,
        'buku-kas-pembantu-pajak' => BukuKasPembantuPajakService::class,
        'buku-bank-desa' => BukuBankDesaService::class,
        'buku-ekspedisi' => BukuEkspedisiService::class,
        'kader-pemberdayaan' => BukuKaderPemberdayaanService::class,
    ];

    /**
     * Get the service instance for the given jenis_buku.
     *
     * @param string $jenisBuku
     * @return BukuAdministrasiInterface
     * @throws \InvalidArgumentException
     */
    protected function getStrategy(string $jenisBuku): BukuAdministrasiInterface
    {
        if (!isset($this->strategies[$jenisBuku])) {
            throw new \InvalidArgumentException("Jenis buku tidak dikenal: {$jenisBuku}");
        }

        $class = $this->strategies[$jenisBuku];
        return new $class();
    }

    /**
     * Get data based on jenis_buku
     */
    public function getData(string $jenisBuku, array $filters = [], bool $isExport = false)
    {
        return $this->getStrategy($jenisBuku)->getData($filters, $isExport);
    }

    /**
     * Get query based on jenis_buku
     */
    public function getQuery(string $jenisBuku, array $filters = [])
    {
        return $this->getStrategy($jenisBuku)->getQuery($filters);
    }
    
    /**
     * Proxy for Buku Inventaris Kekayaan Desa PDF Export
     */
    public function getInventarisKekayaanPdf(int $tahun): \Illuminate\Support\Collection
    {
        $service = new BukuInventarisKekayaanService();
        return $service->getInventarisKekayaanPdf($tahun);
    }
}
