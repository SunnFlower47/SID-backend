<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncKartuKeluarga extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'desa:sync-kk {--force : Force update IDs even if already set}';

    /**
     * The console command description.
     */
    protected $description = 'Clean and sync kartu_keluarga_id in penduduks table based on NKK string';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Elite Data Cleansing & Sync (Fase 2)...');
        
        $conflicts = [];
        $stats = [
            'total' => 0,
            'fixed' => 0,
            'orphan' => 0,
            'mismatch' => 0,
            'already_correct' => 0,
        ];

        $penduduks = Penduduk::withTrashed()->whereNotNull('nkk')->get();
        $stats['total'] = $penduduks->count();

        $bar = $this->output->createProgressBar($stats['total']);
        $bar->start();

        foreach ($penduduks as $p) {
            $nkkString = trim((string)$p->nkk);
            
            if (empty($nkkString)) {
                $bar->advance();
                continue;
            }

            // 1. Find the correct KK record by NKK string
            $kk = KartuKeluarga::withTrashed()->where('nkk', $nkkString)->first();

            if (!$kk) {
                // ORPHAN: NKK exists in Penduduk but NO record in KartuKeluarga
                $stats['orphan']++;
                $conflicts[] = [
                    'nik' => $p->nik,
                    'nama' => $p->nama,
                    'nkk_string' => $nkkString,
                    'issue' => 'ORPHAN (KK Record Missing)',
                    'current_id' => $p->kartu_keluarga_id ?? 'NULL',
                    'correct_id' => 'NOT FOUND',
                ];
                $bar->advance();
                continue;
            }

            // 2. Check if current ID is "ngaco" or correct
            if ($p->kartu_keluarga_id == $kk->id) {
                $stats['already_correct']++;
            } else {
                // MISMATCH / NGACO: ID doesn't match the NKK string's record
                if ($p->kartu_keluarga_id) {
                    $stats['mismatch']++;
                    $conflicts[] = [
                        'nik' => $p->nik,
                        'nama' => $p->nama,
                        'nkk_string' => $nkkString,
                        'issue' => 'ID MISMATCH (Data Ngaco)',
                        'current_id' => $p->kartu_keluarga_id,
                        'correct_id' => $kk->id,
                    ];
                }

                // FIX: Perform the update
                $p->kartu_keluarga_id = $kk->id;
                $p->saveQuietly(); // Use saveQuietly to avoid triggering observers during sync
                $stats['fixed']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 3. Generate Report
        $this->info("=== Sync Results ===");
        $this->info("Total Residents: {$stats['total']}");
        $this->info("Fixed (Data Ngaco Cleaned): {$stats['fixed']}");
        $this->info("Orphans (No KK Record Found): {$stats['orphan']}");
        $this->info("Already Correct: {$stats['already_correct']}");

        if (!empty($conflicts)) {
            $filename = 'sync-conflicts-' . now()->format('Y-m-d-His') . '.csv';
            $path = 'public/logs/' . $filename;
            
            $header = ['NIK', 'Nama', 'NKK String', 'Issue', 'Current ID', 'Correct ID'];
            
            $handle = fopen('php://temp', 'r+');
            fputcsv($handle, $header);
            foreach ($conflicts as $row) {
                fputcsv($handle, $row);
            }
            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            Storage::disk('local')->put($path, $content);
            
            $this->warn("!!! FOUND " . count($conflicts) . " CONFLICTS !!!");
            $this->warn("Conflict Log saved to: " . storage_path('app/' . $path));
            $this->warn("Please review this file before proceeding to Fase 3.");
        } else {
            $this->info("Great! No conflicts found.");
        }

        return 0;
    }
}
