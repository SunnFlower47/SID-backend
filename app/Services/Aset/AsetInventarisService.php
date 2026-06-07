<?php

namespace App\Services\Aset;

use App\Models\AsetInventaris;
use App\Models\AsetMutasi;

class AsetInventarisService
{
    /**
     * Hitung saldo suatu aset SEBELUM periode tertentu (= saldo awal periode itu).
     * Semua mutasi di tahun sebelumnya + semester sebelumnya pada tahun yang sama.
     */
    public static function saldoSebelumPeriode(int $inventarisId, int $tahun, int $semester): array
    {
        $mutasis = AsetMutasi::where('aset_inventaris_id', $inventarisId)
            ->where(function ($q) use ($tahun, $semester) {
                $q->where('tahun', '<', $tahun)
                  ->orWhere(function ($q2) use ($tahun, $semester) {
                      $q2->where('tahun', $tahun)
                         ->where('semester', '<', $semester);
                  });
            })
            ->get();

        return [
            'kwantitas' => (float) ($mutasis->where('jenis', 'tambah')->sum('kwantitas')
                                  - $mutasis->where('jenis', 'kurang')->sum('kwantitas')),
            'nilai'     => (float) ($mutasis->where('jenis', 'tambah')->sum('nilai')
                                  - $mutasis->where('jenis', 'kurang')->sum('nilai')),
        ];
    }

    /**
     * Ambil seluruh inventaris dikelompokkan per kategori,
     * dengan kalkulasi saldo awal, mutasi, saldo akhir untuk periode (tahun/semester).
     *
     * Aturan tampil: aset ditampilkan jika punya saldo awal > 0
     * ATAU ada mutasi di periode ini. Aset yang belum ada sama sekali di/sebelum
     * periode ini tidak ditampilkan.
     */
    public static function getGroupedByKategori(int $tahun, int $semester): array
    {
        $inventaris = AsetInventaris::with([
            'barang.kategori',
            'mutasis' => fn($q) => $q->orderBy('tanggal'),
        ])->get();

        if ($inventaris->isEmpty()) return [];

        $byKategori = $inventaris->groupBy(fn($inv) => $inv->barang?->aset_kategori_id ?? 0);
        $result     = [];

        foreach ($byKategori as $kategoriId => $items) {
            $kategori = $items->first()->barang?->kategori;
            if (!$kategori) continue;

            $rows     = [];
            $subtotal = self::emptySubtotal();

            foreach ($items as $inv) {
                $saldoAwal = self::saldoSebelumPeriode($inv->id, $tahun, $semester);

                $mutasiPeriode = $inv->mutasis
                    ->where('tahun', $tahun)
                    ->where('semester', $semester);

                $tambahQty   = (float) $mutasiPeriode->where('jenis', 'tambah')->sum('kwantitas');
                $tambahNilai = (float) $mutasiPeriode->where('jenis', 'tambah')->sum('nilai');
                $kurangQty   = (float) $mutasiPeriode->where('jenis', 'kurang')->sum('kwantitas');
                $kurangNilai = (float) $mutasiPeriode->where('jenis', 'kurang')->sum('nilai');

                // Hanya tampilkan aset yang sudah aktif di/sebelum periode ini
                if ($saldoAwal['kwantitas'] == 0 && $tambahQty == 0 && $kurangQty == 0) {
                    continue;
                }

                $akhirQty   = $saldoAwal['kwantitas'] + $tambahQty - $kurangQty;
                $akhirNilai = $saldoAwal['nilai']     + $tambahNilai - $kurangNilai;

                $rows[] = [
                    'id'                      => $inv->id,
                    'nup'                     => $inv->nup,
                    'barang'                  => $inv->barang,
                    'nama_display'            => $inv->nama_display,
                    'satuan'                  => $inv->satuan,
                    'kondisi'                 => $inv->kondisi,
                    'lokasi'                  => $inv->lokasi,
                    'asal_usul'               => $inv->asal_usul,
                    'tanggal_perolehan'       => $inv->tanggal_perolehan,
                    'saldo_awal_kwantitas'    => $saldoAwal['kwantitas'],
                    'saldo_awal_nilai'        => $saldoAwal['nilai'],
                    'mutasi_tambah_kwantitas' => $tambahQty,
                    'mutasi_tambah_nilai'     => $tambahNilai,
                    'mutasi_kurang_kwantitas' => $kurangQty,
                    'mutasi_kurang_nilai'     => $kurangNilai,
                    'saldo_akhir_kwantitas'   => $akhirQty,
                    'saldo_akhir_nilai'       => $akhirNilai,
                    'mutasi_detail'           => $mutasiPeriode->values(),
                ];

                $subtotal['saldo_awal_kwantitas']    += $saldoAwal['kwantitas'];
                $subtotal['saldo_awal_nilai']         += $saldoAwal['nilai'];
                $subtotal['mutasi_tambah_kwantitas']  += $tambahQty;
                $subtotal['mutasi_tambah_nilai']      += $tambahNilai;
                $subtotal['mutasi_kurang_kwantitas']  += $kurangQty;
                $subtotal['mutasi_kurang_nilai']      += $kurangNilai;
                $subtotal['saldo_akhir_kwantitas']    += $akhirQty;
                $subtotal['saldo_akhir_nilai']        += $akhirNilai;
            }

            if (!empty($rows)) {
                $result[] = [
                    'kategori' => $kategori,
                    'items'    => $rows,
                    'subtotal' => $subtotal,
                ];
            }
        }

        // Urutkan berdasarkan kode kategori
        usort($result, fn($a, $b) => strcmp($a['kategori']->kode, $b['kategori']->kode));

        return $result;
    }

    /**
     * Grand total dari semua kategori.
     */
    public static function getGrandTotal(array $grouped): array
    {
        $gt = self::emptySubtotal();
        foreach ($grouped as $g) {
            foreach (array_keys($gt) as $key) {
                $gt[$key] += $g['subtotal'][$key];
            }
        }
        return $gt;
    }

    /**
     * Daftar tahun yang punya mutasi (untuk dropdown filter).
     * Selalu include tahun berjalan.
     */
    public static function getTahunList(): array
    {
        $tahunDb  = AsetMutasi::distinct()->orderByDesc('tahun')->pluck('tahun')->toArray();
        $current  = (int) now()->year;
        if (!in_array($current, $tahunDb)) {
            array_unshift($tahunDb, $current);
        }
        rsort($tahunDb);
        return $tahunDb ?: [$current];
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private static function emptySubtotal(): array
    {
        return [
            'saldo_awal_kwantitas'    => 0,
            'saldo_awal_nilai'        => 0,
            'mutasi_tambah_kwantitas' => 0,
            'mutasi_tambah_nilai'     => 0,
            'mutasi_kurang_kwantitas' => 0,
            'mutasi_kurang_nilai'     => 0,
            'saldo_akhir_kwantitas'   => 0,
            'saldo_akhir_nilai'       => 0,
        ];
    }
}
