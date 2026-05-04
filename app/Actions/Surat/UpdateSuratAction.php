<?php

namespace App\Actions\Surat;

use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\DB;

class UpdateSuratAction
{
    public function execute(SuratPengajuan $suratPengajuan, array $validated)
    {
        return DB::transaction(function () use ($suratPengajuan, $validated) {
            $updateData = $validated;
            $updateData['data_tambahan'] = $validated['data_tambahan'] ?? [];

            // If letter type changed, regenerate number
            if ($suratPengajuan->jenis_surat != $validated['jenis_surat']) {
                $updateData['nomor_surat'] = $this->generateNomorSurat($validated['jenis_surat']);
            }

            $suratPengajuan->update($updateData);

            return [
                'type' => 'success',
                'message' => 'Surat pengajuan berhasil diperbarui.'
            ];
        });
    }

    private function generateNomorSurat($suratType)
    {
        $type = \App\Models\SuratType::find($suratType);
        $kodeSurat = $type ? $type->kode : 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }
}
