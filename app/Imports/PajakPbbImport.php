<?php

namespace App\Imports;

use App\Models\PajakPbbObjek;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PajakPbbImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // Determine NOP column key dynamically
                $nopKey = null;
                $namaKey = null;

                $rowArray = $row->toArray();
                
                foreach (array_keys($rowArray) as $key) {
                    $keyStr = strtolower((string)$key);
                    if (!$nopKey && (str_contains($keyStr, 'nop') || str_contains($keyStr, 'nomor_objek'))) {
                        $nopKey = $key;
                    }
                    if (!$namaKey && (str_contains($keyStr, 'nama') || str_contains($keyStr, 'wajib_pajak') || str_contains($keyStr, 'wp'))) {
                        $namaKey = $key;
                    }
                }

                $nopRaw = $nopKey && isset($rowArray[$nopKey]) ? $rowArray[$nopKey] : '';
                $nop = preg_replace('/[^0-9]/', '', (string)$nopRaw);
                $nama = $namaKey && isset($rowArray[$namaKey]) ? trim((string)$rowArray[$namaKey]) : '';

                // Skip dummy instruction row
                if (str_contains(strtolower((string)$nopRaw), 'hapus')) continue;

                // Ensure it has exactly 18 digits (standard NOP)
                if (empty($nop) || strlen($nop) !== 18) {
                    continue;
                }

                $objek = PajakPbbObjek::updateOrCreate(
                    ['nop' => $nop],
                    [
                        'nama_wp' => empty($nama) ? null : strtoupper($nama),
                    ]
                );

                // Auto-sync billing data via Mapagbumi
                dispatch(new \App\Jobs\SyncPbbMapagbumiJob($objek));

            } catch (\Exception $e) {
                Log::error('Error importing PBB NOP ' . ($nopRaw ?? 'Unknown') . ': ' . $e->getMessage());
                continue;
            }
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
