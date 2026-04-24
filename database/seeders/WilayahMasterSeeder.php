<?php

namespace Database\Seeders;

use App\Models\Dusun;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Seeder;

class WilayahMasterSeeder extends Seeder
{
    /**
     * Seed master wilayah dari data penduduk existing.
     */
    public function run(): void
    {
        $rows = Penduduk::query()
            ->select(['rt', 'rw', 'dusun'])
            ->whereNotNull('rt')
            ->whereNotNull('rw')
            ->get();

        foreach ($rows as $row) {
            $rtKode = $this->normalizeKode($row->rt, 3);
            $rwKode = $this->normalizeKode($row->rw, 3);
            $dusunNama = $this->normalizeDusun($row->dusun);

            if (!$rtKode || !$rwKode) {
                continue;
            }

            $dusun = null;
            if ($dusunNama) {
                $dusun = Dusun::firstOrCreate(
                    ['nama' => $dusunNama],
                    [
                        'kode' => null,
                        'is_active' => true,
                        'is_auto_generated' => true,
                        'needs_review' => false,
                    ]
                );
            }

            $rw = Rw::firstOrCreate(
                ['kode' => $rwKode],
                [
                    'nama' => "RW {$rwKode}",
                    'is_active' => true,
                    'is_auto_generated' => true,
                    'needs_review' => false,
                ]
            );

            // RT unik per RW
            $rt = Rt::firstOrCreate(
                [
                    'kode' => $rtKode,
                    'rw_id' => $rw->id,
                ],
                [
                    'nama' => "RT {$rtKode}",
                    'dusun_id' => $dusun?->id,
                    'is_active' => true,
                    'is_auto_generated' => true,
                    'needs_review' => false,
                ]
            );

            // Backfill dusun jika RT sudah ada tapi dusun belum terisi
            if (!$rt->dusun_id && $dusun) {
                $rt->dusun_id = $dusun->id;
                $rt->save();
            }
        }
    }

    private function normalizeKode(?string $value, int $length = 3): ?string
    {
        if ($value === null) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $value);
        if ($digits === '') {
            return null;
        }

        return str_pad(substr($digits, -$length), $length, '0', STR_PAD_LEFT);
    }

    private function normalizeDusun(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        return preg_replace('/\s+/', ' ', $trimmed);
    }
}
