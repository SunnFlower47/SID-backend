<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Exception;

class SuratService
{
    /**
     * Generate Surat dari Template Word
     *
     * @param string $templateName Nama file template di storage/app/templates/surat/
     * @param array $data Data yang akan disuntikkan ke template [key => value]
     * @param string $outputName Nama file hasil output
     * @param string $penandatangan 'kepala_desa' atau 'sekretaris_desa'
     * @return string Path file hasil output
     */
    public function generate($templateName, array $data, $outputName = null, $penandatangan = 'kepala_desa')
    {
        try {
            $templatePath = storage_path('app/templates/surat/' . $templateName);

            if (!file_exists($templatePath)) {
                throw new \Exception("Template surat tidak ditemukan: {$templateName}");
            }

            $templateProcessor = new TemplateProcessor($templatePath);

            // 1. Tambahkan Data Global (Desa & Penandatangan)
            $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
            $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
            
            $namaDesa = 'Cibatu'; // Nanti bisa ambil dari config/settings

            if ($penandatangan === 'kepala_desa' && $kades) {
                $data['ttd_atas'] = "Kepala Desa " . $namaDesa;
                $data['ttd_bawah'] = strtoupper($kades->nama);
            } else if ($penandatangan === 'sekretaris_desa' && $sekdes) {
                $data['ttd_atas'] = "a.n. Kepala Desa " . $namaDesa;
                $data['ttd_bawah'] = strtoupper($sekdes->nama);
            } else {
                // Fallback jika tidak ada data yang pas
                $data['ttd_atas'] = "Kepala Desa " . $namaDesa;
                $data['ttd_bawah'] = strtoupper($kades->nama ?? '....................');
            }

            // 2. Tambahkan Tanggal Otomatis jika belum ada
            if (!isset($data['tanggal_surat'])) {
                $data['tanggal_surat'] = \Carbon\Carbon::now()->isoFormat('D MMMM Y');
            }

            // 3. Suntikkan semua data ke Word
            foreach ($data as $key => $value) {
                // Handle image values (logo/foto)
                if (is_array($value) && isset($value['type']) && $value['type'] === 'image') {
                    $templateProcessor->setImageValue($key, [
                        'path' => $value['path'],
                        'width' => $value['width'] ?? 100,
                        'height' => $value['height'] ?? 100,
                        'ratio' => $value['ratio'] ?? true,
                    ]);
                } else {
                    // Cek jika ada block (misal: ${block_wali} ... ${/block_wali})
                    // Ini logic sederhana untuk deleteBlock jika value-nya false/empty
                    if (str_starts_with($key, 'block_')) {
                        if (!$value) {
                            $templateProcessor->deleteBlock($key);
                        } else {
                            $templateProcessor->cloneBlock($key, 1, true, false);
                        }
                    } else {
                        $templateProcessor->setValue($key, (string)$value);
                    }
                }
            }

            $outputName = $outputName ?? 'surat_' . time() . '.docx';
            $outputPath = storage_path('app/public/generated_surat/' . $outputName);

            // Pastikan folder output ada
            $outputDir = dirname($outputPath);
            if (!file_exists($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            $templateProcessor->saveAs($outputPath);

            return $outputPath;
        } catch (Exception $e) {
            throw new Exception("Gagal membuat surat: " . $e->getMessage());
        }
    }
}
