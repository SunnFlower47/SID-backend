<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dusun;
use App\Models\Rw;
use App\Models\Rt;

class WilayahSeeder extends Seeder
{
    /**
     * Seeder wilayah dengan data default/contoh.
     * Gunakan ini jika ingin reset ke struktur wilayah awal yang statis.
     */
    public function run(): void
    {
        $wilayah = [
            'Dusun I' => [
                '001' => ['001', '002', '003'],
                '002' => ['001', '002'],
            ],
            'Dusun II' => [
                '003' => ['001', '002', '003', '004'],
                '004' => ['001', '002'],
            ],
            'Dusun III' => [
                '005' => ['001', '002', '003'],
                '006' => ['001', '002'],
            ],
        ];

        foreach ($wilayah as $dusunNama => $rws) {
            $dusun = Dusun::updateOrCreate(
                ['nama' => $dusunNama],
                ['is_active' => true]
            );

            foreach ($rws as $rwKode => $rts) {
                $rw = Rw::updateOrCreate(
                    ['kode' => $rwKode],
                    ['nama' => "RW {$rwKode}", 'is_active' => true]
                );

                foreach ($rts as $rtKode) {
                    Rt::updateOrCreate(
                        [
                            'kode' => $rtKode,
                            'rw_id' => $rw->id
                        ],
                        [
                            'nama' => "RT {$rtKode}",
                            'dusun_id' => $dusun->id,
                            'is_active' => true
                        ]
                    );
                }
            }
        }
        
        echo "Wilayah (Default) Seeded Successfully!\n";
    }
}
