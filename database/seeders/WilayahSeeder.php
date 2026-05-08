<?php

namespace Database\Seeders;

use App\Models\Dusun;
use App\Models\Rt;
use App\Models\Rw;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Dusun
        $dusuns = [
            ['nama' => 'Dusun I (Cibatu Hilir)', 'kode' => 'DSN01'],
            ['nama' => 'Dusun II (Cibatu Girang)', 'kode' => 'DSN02'],
        ];

        foreach ($dusuns as $d) {
            Dusun::updateOrCreate(['kode' => $d['kode']], $d);
        }

        $dsn1 = Dusun::where('kode', 'DSN01')->first();
        $dsn2 = Dusun::where('kode', 'DSN02')->first();

        // 2. Create RW 001 - 004
        for ($i = 1; $i <= 4; $i++) {
            $kodeRw = str_pad($i, 3, '0', STR_PAD_LEFT);
            $rw = Rw::updateOrCreate(['kode' => $kodeRw], [
                'nama' => "RW {$kodeRw}",
                'is_active' => true
            ]);

            // 3. Create RT 001 - 004 for each RW
            for ($j = 1; $j <= 4; $j++) {
                $kodeRt = str_pad($j, 3, '0', STR_PAD_LEFT);
                Rt::updateOrCreate([
                    'kode' => $kodeRt,
                    'rw_id' => $rw->id
                ], [
                    'dusun_id' => ($i <= 2) ? $dsn1->id : $dsn2->id,
                    'nama' => "RT {$kodeRt}",
                    'is_active' => true
                ]);
            }
        }
    }
}
