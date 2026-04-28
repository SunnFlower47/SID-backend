<?php

namespace App\Traits;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\Dusun;

trait WilayahResolver
{
    /**
     * Resolve RT, RW, and Dusun IDs from strings
     * Auto-create if not exists to ensure point 2 requirement
     */
    protected function resolveWilayah($rtString, $rwString, $dusunString = null)
    {
        // 1. Sanitize (Senior Advice)
        // Clean "RT 01", "01", "1" -> "001"
        $rtCode = str_pad(preg_replace('/[^0-9]/', '', $rtString), 3, '0', STR_PAD_LEFT);
        $rwCode = str_pad(preg_replace('/[^0-9]/', '', $rwString), 3, '0', STR_PAD_LEFT);
        
        // 2. Sanity Check (Ultimate Edition Standard)
        if ((int)$rtCode > 100) {
            throw new \Exception("Format RT tidak valid: {$rtString} (Maksimal 100)");
        }
        if ((int)$rwCode > 100) {
            throw new \Exception("Format RW tidak valid: {$rwString} (Maksimal 100)");
        }

        // 2. Resolve Dusun
        if (empty($dusunString)) {
            // Predict dusun from RT if not provided (legacy logic)
            $dusunSatu = ['001', '002', '003', '004', '007', '008'];
            $dusunName = in_array($rtCode, $dusunSatu) ? 'Dusun Satu' : 'Dusun Dua';
        } else {
            $dusunName = $dusunString;
        }

        $dusun = Dusun::firstOrCreate(['nama' => $dusunName]);

        // 3. Resolve RW
        $rw = Rw::firstOrCreate(
            ['kode' => $rwCode],
            ['dusun_id' => $dusun->id]
        );

        // 4. Resolve RT
        $rt = Rt::firstOrCreate(
            ['kode' => $rtCode, 'rw_id' => $rw->id],
            ['dusun_id' => $dusun->id]
        );

        return [
            'rt_id' => $rt->id,
            'rw_id' => $rw->id,
            'dusun_id' => $dusun->id
        ];
    }
}
