<?php

namespace App\Console\Commands;

use App\Models\KartuKeluarga;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Retroactive scan: temukan KK historis yang tidak punya Kepala Keluarga aktif
 * (sebelum sistem MutasiObserver diterapkan) dan flag sebagai 'bermasalah'.
 *
 * Kriteria KK bermasalah historis:
 *   1. status_kk = 'normal' (belum pernah diflag)
 *   2. anggota_aktif > 0 (masih ada anggota)
 *   3. TIDAK ada anggota aktif (deleted_at IS NULL) dengan kedudukan 'Kepala Keluarga'
 */
class ScanKkBermasalahHistoris extends Command
{
    protected $signature   = 'kk:scan-historis {--dry-run : Hanya tampilkan hasil tanpa menyimpan} {--limit=0 : Batasi jumlah KK yang diproses} {--force : Eksekusi tanpa konfirmasi (untuk web)}';
    protected $description = 'Scan retroaktif KK historis yang tidak punya Kepala Keluarga aktif dan belum terflag.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit  = (int) $this->option('limit');

        $this->info('');
        $this->info('🔍 Scanning KK historis tanpa Kepala Keluarga aktif...');
        $this->info($dryRun ? '   [DRY-RUN — tidak ada perubahan tersimpan]' : '   [MODE LIVE — perubahan akan disimpan]');
        $this->info('');

        // Raw SQL untuk performa: cari NKK yang tidak punya penduduk aktif ber-kedudukan 'Kepala Keluarga'
        $kandidatNkk = DB::select("
            SELECT kk.nkk, kk.nama_kepala_keluarga, kk.anggota_aktif
            FROM kartu_keluargas kk
            WHERE kk.status_kk = 'normal'
              AND kk.anggota_aktif > 0
              AND NOT EXISTS (
                  SELECT 1 FROM penduduks p
                  WHERE p.kartu_keluarga_id = kk.id
                    AND p.kedudukan_keluarga = 'Kepala Keluarga'
                    AND p.deleted_at IS NULL
              )
            ORDER BY kk.nkk
        ");

        $total = count($kandidatNkk);

        if ($total === 0) {
            $this->info('✅ Tidak ada KK historis bermasalah yang ditemukan.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$total} KK historis tanpa Kepala Keluarga aktif:");
        $this->newLine();

        // Tabel preview
        $rows = array_map(fn($r) => [
            $r->nkk,
            $r->nama_kepala_keluarga ?? '-',
            $r->anggota_aktif,
        ], array_slice($kandidatNkk, 0, 30));

        $this->table(['NKK', 'Nama KK Tercatat', 'Anggota Aktif'], $rows);

        if ($total > 30) {
            $this->line("   ... dan " . ($total - 30) . " lainnya tidak ditampilkan.");
        }

        $this->newLine();

        if ($dryRun) {
            $this->info("DRY-RUN selesai. Jalankan tanpa --dry-run untuk menerapkan flag.");
            return self::SUCCESS;
        }

        $force = $this->option('force');
        if (!$force && !$this->confirm("Lanjutkan flag {$total} KK sebagai 'bermasalah'?", false)) {
            $this->info('Dibatalkan.');
            return self::SUCCESS;
        }

        // Apply dengan limit jika ada
        $toProcess = $limit > 0 ? array_slice($kandidatNkk, 0, $limit) : $kandidatNkk;
        $nkkList   = array_column($toProcess, 'nkk');

        $this->info("Memproses " . count($nkkList) . " KK...");
        $bar = $this->output->createProgressBar(count($nkkList));
        $bar->start();

        $flagged = 0;
        foreach (array_chunk($nkkList, 100) as $chunk) {
            // Cari mutasi kematian/pindah terakhir yang paling relevan sebagai penyebab
            foreach ($chunk as $nkk) {
                $mutasiPenyebab = DB::selectOne("
                    SELECT m.id FROM mutasis m
                    JOIN penduduks p ON p.id = m.penduduk_id
                    JOIN kartu_keluargas kk ON kk.id = p.kartu_keluarga_id
                    WHERE kk.nkk = ?
                      AND m.jenis_mutasi IN ('kematian', 'pindah_keluar', 'pisah_kk')
                      AND p.kedudukan_keluarga = 'Kepala Keluarga'
                      AND m.deleted_at IS NULL
                    ORDER BY m.created_at DESC
                    LIMIT 1
                ", [$nkk]);

                KartuKeluarga::where('nkk', $nkk)->update([
                    'status_kk'            => 'bermasalah',
                    'mutasi_penyebab_id'   => $mutasiPenyebab?->id,     // null jika tidak ada (data lama)
                    'kk_bermasalah_sejak'  => now(),
                    'catatan_bermasalah'   => 'Dideteksi retroaktif: tidak ada Kepala Keluarga aktif (data sebelum sistem monitoring diterapkan)',
                ]);

                $flagged++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai! {$flagged} KK berhasil diflag sebagai 'bermasalah'.");
        $this->info("   Buka menu KK Bermasalah untuk menyelesaikannya satu per satu.");

        return self::SUCCESS;
    }
}
