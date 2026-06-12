<?php

namespace App\Actions\Surat;

use App\Models\SuratPengajuan;
use App\Services\Kependudukan\PendudukDomisiliService;
use App\Services\Kependudukan\MutasiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StoreSuratAction
{
    public function execute(array $validated)
    {
        \Illuminate\Support\Facades\Log::info('StoreSuratAction Executing', ['data' => $validated]);
        
        return DB::transaction(function () use ($validated) {
            $status = 'selesai';
            $adminId = Auth::id();

            // 1. Create the letter record FIRST as an archive
            \Illuminate\Support\Facades\Log::info('Creating SuratPengajuan record');
            $result = $this->handleNormalLetter($validated, $status, $adminId);
            $surat = $result['data'];
            
            \Illuminate\Support\Facades\Log::info('SuratPengajuan record created', ['id' => $surat->id ?? 'N/A']);

            // 2. Handle Automations (Mutasi/Domisili) with the Surat ID
            if ($this->isManualDomicile($validated)) {
                \Illuminate\Support\Facades\Log::info('Handling Manual Domicile');
                $this->handleManualDomicile($validated, $surat->id);
            }

            if ($this->isDeathLetter($validated)) {
                \Illuminate\Support\Facades\Log::info('Handling Death Letter');
                $this->handleDeathLetter($validated, $surat->id);
            }
            
            return $result;
        });
    }

    private function isManualDomicile(array $validated): bool
    {
        return ($validated['jenis_surat'] === 'keterangan-domisili') 
            && isset($validated['data_tambahan']) 
            && empty($validated['penduduk_id']);
    }

    private function handleManualDomicile(array $validated, $suratId = null)
    {
        $service = app(PendudukDomisiliService::class);
        $service->create(array_merge($validated['data_tambahan'], [
            'keperluan_domisili' => $validated['keperluan'] ?? 'Keterangan Domisili',
            'tanggal_masuk' => $validated['data_tambahan']['tanggal_masuk'] ?? now(),
            'surat_pengajuan_id' => $suratId, // Link to letter
        ]));
    }

    private function isDeathLetter(array $validated): bool
    {
        return $validated['jenis_surat'] === 'kematian' && !empty($validated['penduduk_id']);
    }

    private function handleDeathLetter(array $validated, $suratId = null)
    {
        $dt = $validated['data_tambahan'] ?? [];
        $mutasiData = [
            'penduduk_id' => $validated['penduduk_id'],
            'tanggal_mutasi' => $dt['tanggal_meninggal'] ?? $validated['tanggal_surat'] ?? now()->format('Y-m-d'),
            'hari_meninggal' => $dt['hari_meninggal'] ?? 'Senin',
            'jam_meninggal' => $dt['jam_meninggal'] ?? '12:00',
            'bertempat_di' => $dt['bertempat_di'] ?? 'RUMAH SAKIT',
            'alasan' => $dt['alasan'] ?? 'Sakit',
            'hari_pemakaman' => $dt['hari_pemakaman'] ?? 'Senin',
            'tanggal_pemakaman' => $dt['tanggal_pemakaman'] ?? now()->format('Y-m-d'),
            'jam_pemakaman' => $dt['jam_pemakaman'] ?? '15:00',
            'lokasi_pemakaman' => $dt['lokasi_pemakaman'] ?? 'TPU Desa',
            'pelapor_nama' => $dt['pelapor_nama'] ?? null,
            'pelapor_umur' => $dt['pelapor_umur'] ?? null,
            'pelapor_pekerjaan' => $dt['pelapor_pekerjaan'] ?? null,
            'pelapor_alamat' => $dt['pelapor_alamat'] ?? null,
            'pelapor_hubungan' => $dt['pelapor_hubungan'] ?? null,
            'surat_pengajuan_id' => $suratId, // Pass the letter ID
        ];

        $mutasiService = app(MutasiService::class);
        $mutasiService->handleKematian($mutasiData);
    }

    private function handleNormalLetter(array $validated, string $status, int $adminId)
    {
        $resi = 'REQ-' . date('ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        $nomorSurat = ($status === 'selesai' || $status === 'approved') 
            ? $this->generateNomorSurat($validated['jenis_surat']) 
            : null;

        $pengajuan = SuratPengajuan::create([
            'jenis_surat' => $validated['jenis_surat'],
            'penduduk_id' => $validated['penduduk_id'],
            'nomor_surat' => $nomorSurat,
            'nomor_resi'  => $resi,
            'keperluan' => $validated['keperluan'],
            'tujuan' => $validated['tujuan'],
            'tanggal_surat' => $validated['tanggal_surat'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'],
            'data_tambahan' => $validated['data_tambahan'] ?? [],
            'status' => $status,
            'admin_id' => $adminId,
            'penandatangan' => $validated['penandatangan'] ?? 'kepala_desa',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'approved_at' => $status === 'selesai' || $status === 'approved' ? now() : null,
            'completed_at' => $status === 'selesai' ? now() : null,
        ]);

        return [
            'type' => 'success',
            'message' => 'Surat berhasil dibuat!',
            'data' => $pengajuan,
            'nomor_resi' => $resi
        ];
    }

    private function generateNomorSurat($suratType)
    {
        // Cari berdasarkan ID atau Slug
        $type = \App\Models\SuratType::where('id', $suratType)
            ->orWhere('id', 'LIKE', $suratType)
            ->first();
            
        $kodeSurat = $type ? $type->kode : 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }
}
