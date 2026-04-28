<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;

class SyncWilayahIds extends Command
{
    protected $signature = 'sync:wilayah-ids {--force : Force the sync even if columns are already filled}';
    protected $description = 'Sync string-based wilayah data (rt, rw, dusun) to the new ID columns (rt_id, rw_id, dusun_id)';

    public function handle()
    {
        $this->info('Starting Wilayah ID Sync...');

        $tables = [
            'penduduks' => ['rt', 'rw', 'dusun'],
            'kartu_keluargas' => ['rt', 'rw', 'dusun'],
            'fasilitas_desas' => ['rt', 'rw', 'dusun'],
            'struktur_desas' => ['rt', 'rw', 'dusun'],
            'kontak_desas' => ['rt', 'rw', 'dusun'],
            'umkms' => ['rt', 'rw', 'dusun'],
            'testimonis' => ['rt', 'rw', 'dusun']
        ];

        // Cache Master Data for performance
        $masterRts = DB::table('rts')->get()->groupBy('kode');
        $masterRws = DB::table('rws')->get()->groupBy('kode');
        $masterDusuns = DB::table('dusuns')->get()->groupBy('nama');

        // Dusun Mapping Dictionary for consistency
        $dusunMapping = [
            'Dusun Satu' => 'Dusun 1',
            'Dusun 1'    => 'Dusun 1',
            'Dusun Dua'  => 'Dusun 2',
            'Dusun 2'    => 'Dusun 2',
            'Dusun Tiga' => 'Dusun 3',
            'Dusun 3'    => 'Dusun 3',
        ];

        foreach ($tables as $tableName => $columns) {
            if (!DB::getSchemaBuilder()->hasTable($tableName)) {
                $this->warn("Table {$tableName} not found, skipping.");
                continue;
            }

            $this->info("Processing table: {$tableName}");

            DB::table($tableName)->orderBy('id')->chunkById(1000, function ($rows) use ($tableName, $masterRts, $masterRws, $masterDusuns, $dusunMapping) {
                DB::transaction(function () use ($rows, $tableName, $masterRts, $masterRws, $masterDusuns, $dusunMapping) {
                    foreach ($rows as $row) {
                        $updates = [];

                        // 1. Process RW
                        $rawRw = $this->sanitizeCode($row->rw ?? '');
                        if ($rawRw && isset($masterRws[$rawRw])) {
                            $updates['rw_id'] = $masterRws[$rawRw]->first()->id;
                        }

                        // 2. Process RT
                        $rawRt = $this->sanitizeCode($row->rt ?? '');
                        if ($rawRt && isset($masterRts[$rawRt])) {
                            $updates['rt_id'] = $masterRts[$rawRt]->first()->id;
                        }

                        // 3. Process Dusun
                        $rawDusun = trim($row->dusun ?? '');
                        $mappedDusunName = $dusunMapping[$rawDusun] ?? $rawDusun;
                        
                        if ($mappedDusunName && isset($masterDusuns[$mappedDusunName])) {
                            $updates['dusun_id'] = $masterDusuns[$mappedDusunName]->first()->id;
                        }

                        if (!empty($updates)) {
                            DB::table($tableName)->where('id', $row->id)->update($updates);
                        }
                    }
                });
            });

            $this->info("Completed table: {$tableName}");
        }


        $this->info('Wilayah ID Sync finished successfully!');
        return 0;
    }

    private function sanitizeCode($code)
    {
        if (empty($code)) return null;
        // Regex Cleaning (Only numbers)
        $clean = preg_replace('/[^0-9]/', '', $code);
        if (empty($clean)) return null;
        // Pad to 3 digits
        return str_pad($clean, 3, '0', STR_PAD_LEFT);
    }
}
