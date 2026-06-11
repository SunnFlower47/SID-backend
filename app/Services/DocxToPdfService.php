<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * DocxToPdfService — Konversi file .docx ke .pdf menggunakan LibreOffice Headless.
 *
 * Diperlukan sebagai jembatan sebelum mengirim file ke BSrE,
 * karena API BSrE hanya menerima dokumen berformat PDF.
 *
 * Syarat: LibreOffice harus terinstall di server (VPS/Dedicated).
 * Install di Ubuntu/Debian: sudo apt-get install -y libreoffice
 * Install di CentOS/RHEL:   sudo yum install -y libreoffice
 */
class DocxToPdfService
{
    /**
     * Konversi file .docx ke .pdf menggunakan LibreOffice Headless.
     *
     * @param string $docxPath Absolute path ke file .docx sumber
     * @param string|null $outputDir Direktori tujuan (default: direktori yang sama dengan sumber)
     * @return string Absolute path ke file .pdf hasil konversi
     *
     * @throws \Exception jika konversi gagal atau LibreOffice tidak ditemukan
     */
    public function convert(string $docxPath, string $outputDir = null): string
    {
        if (!file_exists($docxPath)) {
            throw new \Exception("File .docx tidak ditemukan: {$docxPath}");
        }

        $outputDir = $outputDir ?? dirname($docxPath);

        // Pastikan direktori output ada
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Cari path LibreOffice yang terinstall
        $libreofficePath = $this->getLibreOfficePath();

        // Jalankan LibreOffice Headless untuk konversi
        // --headless: tanpa GUI
        // --convert-to pdf: target format PDF
        // --outdir: direktori output
        $command = [
            $libreofficePath,
            '--headless',
            '--norestore',
            '--nofirststartwizard',
            '--convert-to', 'pdf',
            '--outdir', $outputDir,
            $docxPath,
        ];

        Log::info('DocxToPdf: Mulai konversi', ['file' => basename($docxPath), 'cmd' => implode(' ', $command)]);

        $process = new Process($command);
        $process->setTimeout(60); // Timeout 60 detik

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                $errorOutput = $process->getErrorOutput();
                Log::error('DocxToPdf: Konversi gagal', ['error' => $errorOutput]);
                throw new \Exception("Konversi .docx ke .pdf gagal: " . $errorOutput);
            }
        } catch (ProcessFailedException $e) {
            Log::error('DocxToPdf ProcessFailedException: ' . $e->getMessage());
            throw new \Exception("LibreOffice tidak dapat dijalankan: " . $e->getMessage());
        }

        // Tentukan nama file output (LibreOffice mengganti ekstensi .docx menjadi .pdf)
        $pdfFileName = pathinfo($docxPath, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = rtrim($outputDir, '/\\') . DIRECTORY_SEPARATOR . $pdfFileName;

        if (!file_exists($pdfPath)) {
            throw new \Exception("File PDF hasil konversi tidak ditemukan di: {$pdfPath}");
        }

        Log::info('DocxToPdf: Konversi berhasil', ['pdf' => $pdfPath, 'size' => filesize($pdfPath)]);

        return $pdfPath;
    }

    /**
     * Cek apakah LibreOffice tersedia di server.
     */
    public function isAvailable(): bool
    {
        try {
            $path = $this->getLibreOfficePath();
            return !empty($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Cari path eksekutor LibreOffice di sistem.
     *
     * @throws \Exception jika LibreOffice tidak ditemukan
     */
    private function getLibreOfficePath(): string
    {
        // Cek dari konfigurasi desa (bisa dikustomisasi per-deployment)
        $configPath = \App\Models\DesaSetting::getValue('libreoffice_path', '');
        if ($configPath && file_exists($configPath)) {
            return $configPath;
        }

        // Deteksi otomatis berdasarkan OS
        $candidates = [];

        if (PHP_OS_FAMILY === 'Windows') {
            $candidates = [
                'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
                'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
            ];
        } else {
            // Linux / Unix (CentOS, Ubuntu, dll)
            $candidates = [
                '/usr/bin/libreoffice',
                '/usr/bin/soffice',
                '/usr/local/bin/libreoffice',
                '/usr/local/bin/soffice',
                '/opt/libreoffice/program/soffice',
            ];
        }

        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        // Coba deteksi via `which` command (Linux)
        if (PHP_OS_FAMILY !== 'Windows') {
            $process = new Process(['which', 'libreoffice']);
            $process->run();
            if ($process->isSuccessful()) {
                $path = trim($process->getOutput());
                if ($path) return $path;
            }

            $process = new Process(['which', 'soffice']);
            $process->run();
            if ($process->isSuccessful()) {
                $path = trim($process->getOutput());
                if ($path) return $path;
            }
        }

        throw new \Exception(
            'LibreOffice tidak ditemukan di server. ' .
            'Install dengan: sudo apt-get install -y libreoffice ' .
            'atau isi path manual di Pengaturan Desa > Konfigurasi BSrE.'
        );
    }
}
