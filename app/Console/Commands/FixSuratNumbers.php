<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Surat;
use App\Models\DesaSetting;

class FixSuratNumbers extends Command
{
    protected $signature = 'surat:fix-numbers';
    protected $description = 'Fix nomor surat yang sudah ada dengan kode yang benar';

    public function handle()
    {
        $this->info('Memperbaiki nomor surat...');

        $surats = Surat::orderBy('created_at')->get();
        $counters = [];

        foreach ($surats as $surat) {
            $year = $surat->created_at->year;
            $type = $surat->jenis_surat;

            // Initialize counter for this year and type
            if (!isset($counters[$year][$type])) {
                $counters[$year][$type] = 0;
            }

            $counters[$year][$type]++;

            // Get correct kode surat
            $suratSettings = DesaSetting::getSuratSettings();
            $kodeSurat = $suratSettings["kode_surat_{$type}"] ?? 'SK';

            // Generate new nomor surat
            $format = DesaSetting::getValue('format_nomor_surat', '{kode_surat}/{nomor_urut}/{bulan}/{tahun}');
            $nomorUrut = str_pad($counters[$year][$type], 3, '0', STR_PAD_LEFT);
            $bulan = $surat->created_at->format('m');
            $tahun = $surat->created_at->format('Y');

            $newNomorSurat = str_replace(
                ['{kode_surat}', '{nomor_urut}', '{bulan}', '{tahun}'],
                [$kodeSurat, $nomorUrut, $bulan, $tahun],
                $format
            );

            // Update nomor surat
            $surat->update(['nomor_surat' => $newNomorSurat]);

            $this->line("Updated: {$surat->id} - {$surat->jenis_surat} - {$newNomorSurat}");
        }

        $this->info('Selesai memperbaiki nomor surat!');
    }
}
