<?php

namespace App\Services;

use App\Models\DesaSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * BsreService — Integrasi API Esign Client Service BSrE (v2.0, Januari 2024)
 *
 * Konfigurasi disimpan per-desa di tabel desa_settings dengan key group 'bsre'.
 * Passphrase (PIN) TIDAK disimpan — selalu diinput langsung oleh pejabat yang menandatangani.
 *
 * Endpoint yang digunakan (API v2):
 *   POST /api/v2/sign/pdf         — Tanda tangan PDF (file sebagai Base64)
 *   POST /api/v2/user/check/status — Cek status sertifikat pejabat
 *   POST /api/sign/verify          — Verifikasi keaslian PDF (sama di v1 & v2)
 */
class BsreService
{
    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct()
    {
        // Ambil konfigurasi kredensial dari .env (via config/services.php)
        $this->baseUrl  = config('services.bsre.url', '');
        $this->username = config('services.bsre.username', '');
        $this->password = config('services.bsre.password', '');
    }

    /**
     * Cek apakah layanan BSrE sudah dikonfigurasi untuk desa ini.
     */
    public function isConfigured(): bool
    {
        return !empty($this->baseUrl) && !empty($this->username) && !empty($this->password);
    }

    /**
     * Cek status sertifikat elektronik pejabat berdasarkan NIK.
     * Hanya pejabat dengan status 'ISSUE' yang dapat melakukan TTE.
     *
     * Endpoint: POST /api/v2/user/check/status
     *
     * @param string $nik NIK pejabat (16 digit)
     * @return array ['success' => bool, 'status' => 'ISSUE'|'EXPIRED'|..., 'nama' => string, 'message' => string]
     */
    public function checkStatus(string $nik): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Konfigurasi BSrE belum diisi. Silakan lengkapi di Pengaturan Desa.'];
        }

        try {
            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(15)
                ->post($this->baseUrl . '/api/v2/user/check/status', [
                    'nik' => $nik,
                ]);

            $data = $response->json();

            if ($response->successful()) {
                $status = $data['status'] ?? 'UNKNOWN';
                return [
                    'success' => true,
                    'status'  => $status,
                    'nama'    => $data['nama'] ?? '',
                    'is_active' => $status === 'ISSUE',
                    'message' => $this->getStatusMessage($status),
                ];
            }

            return [
                'success' => false,
                'status'  => 'ERROR',
                'is_active' => false,
                'message' => $data['message'] ?? 'Gagal memeriksa status sertifikat.',
            ];
        } catch (\Exception $e) {
            Log::error('BSrE checkStatus error: ' . $e->getMessage());
            return [
                'success' => false,
                'status'  => 'CONNECTION_ERROR',
                'is_active' => false,
                'message' => 'Tidak dapat terhubung ke server Esign Client. Pastikan server berjalan.',
            ];
        }
    }

    /**
     * Tandatangani dokumen PDF menggunakan API v2 BSrE.
     * File PDF dikirim dalam format Base64 string (bukan upload multipart).
     *
     * Endpoint: POST /api/v2/sign/pdf
     *
     * @param string $pdfPath     Absolute path ke file PDF yang akan ditandatangani
     * @param string $nik         NIK pejabat penandatangan
     * @param string $passphrase  PIN/Passphrase pejabat (tidak disimpan di sistem)
     * @param string $linkQR      URL tujuan QR Code (halaman verifikasi publik)
     * @param string $namaLokasi  Nama lokasi penandatanganan (opsional)
     * @param string $reason      Alasan/catatan penandatanganan (opsional)
     * @return string             Binary content PDF yang sudah ber-TTE
     *
     * @throws \Exception jika proses TTE gagal
     */
    public function signPdf(
        string $pdfPath,
        string $nik,
        string $passphrase,
        string $linkQR,
        string $namaLokasi = '',
        string $reason = 'Surat telah disetujui dan ditandatangani'
    ): string {
        if (!$this->isConfigured()) {
            throw new \Exception('Konfigurasi BSrE belum diisi. Silakan lengkapi di Pengaturan Desa.');
        }

        if (!file_exists($pdfPath)) {
            throw new \Exception("File PDF tidak ditemukan: {$pdfPath}");
        }

        // Konversi PDF ke Base64 (wajib untuk API v2)
        $pdfBase64 = base64_encode(file_get_contents($pdfPath));

        // Konfigurasi posisi visualisasi QR Code di surat
        // Koordinat disesuaikan untuk posisi pojok kanan bawah pada surat A4
        $signatureProperties = [
            'tampilan' => 'VISIBLE',
            'page'     => 1,         // Halaman ke-1 (bisa diubah ke halaman terakhir)
            'originX'  => 390.0,     // Koordinat X (kanan)
            'originY'  => 60.0,      // Koordinat Y (bawah)
            'width'    => 120.0,
            'height'   => 120.0,
            'reason'   => $reason,
        ];

        // Tambahkan lokasi jika diisi
        if ($namaLokasi) {
            $signatureProperties['location'] = $namaLokasi;
        }

        // Generate QR Code lokal dan konversi ke Base64 (sesuai spesifikasi API v2.2.2)
        // qr_token atau link verifikasi disertakan dalam QR Code
        $qrCodeImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate($linkQR);
            
        $signatureProperties['imageBase64'] = base64_encode($qrCodeImage);

        $payload = [
            'nik'                 => $nik,
            'passphrase'          => $passphrase,
            'signatureProperties' => [$signatureProperties],
            'file'                => [$pdfBase64], // Array of Base64 (support bulk signing)
        ];

        Log::info("BSrE: Memulai proses TTE untuk NIK {$nik}, file: " . basename($pdfPath));

        $response = Http::withBasicAuth($this->username, $this->password)
            ->timeout(120) // TTE bisa memakan waktu hingga 2 menit
            ->post($this->baseUrl . '/api/v2/sign/pdf', $payload);

        if (!$response->successful()) {
            $errorBody = $response->json();
            $errorMsg = $errorBody['message'] ?? $errorBody['error'] ?? ('HTTP ' . $response->status());
            Log::error("BSrE signPdf error: {$errorMsg}", ['nik' => $nik, 'status' => $response->status()]);
            throw new \Exception("TTE gagal: {$errorMsg}");
        }

        // Response sukses API v2: binary PDF langsung (Content-Type: application/pdf)
        // Atau bisa juga JSON dengan field 'file' berupa Base64 tergantung versi Esign Client
        $contentType = $response->header('Content-Type');
        if (str_contains($contentType, 'application/json')) {
            // Beberapa versi Esign Client mengembalikan JSON dengan file Base64
            $jsonData = $response->json();
            if (isset($jsonData['file'])) {
                return base64_decode($jsonData['file']);
            }
            throw new \Exception('TTE gagal: Response tidak mengandung file PDF.');
        }

        // Default: binary PDF langsung
        $signedPdf = $response->body();

        Log::info("BSrE: TTE berhasil untuk NIK {$nik}, ukuran PDF: " . strlen($signedPdf) . ' bytes');

        return $signedPdf;
    }

    /**
     * Verifikasi keaslian dokumen PDF yang sudah ber-TTE.
     * Endpoint: POST /api/v2/verify/pdf
     *
     * @param string $pdfPath Path ke file PDF yang akan diverifikasi
     * @return array Detail hasil verifikasi
     */
    public function verifyPdf(string $pdfPath): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Konfigurasi BSrE belum diisi.'];
        }

        if (!file_exists($pdfPath)) {
            return ['success' => false, 'message' => 'File tidak ditemukan.'];
        }

        try {
            $pdfBase64 = base64_encode(file_get_contents($pdfPath));

            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->post($this->baseUrl . '/api/v2/verify/pdf', [
                    'file' => $pdfBase64
                ]);

            return $response->json() ?? ['success' => false, 'message' => 'Response tidak valid.'];
        } catch (\Exception $e) {
            Log::error('BSrE verifyPdf error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Gagal memverifikasi: ' . $e->getMessage()];
        }
    }

    /**
     * Terjemahkan kode status BSrE ke pesan yang ramah pengguna.
     */
    private function getStatusMessage(string $status): string
    {
        return match ($status) {
            'ISSUE'                    => 'Sertifikat Elektronik aktif dan siap digunakan.',
            'EXPIRED'                  => 'Masa berlaku Sertifikat Elektronik telah habis. Hubungi BSrE untuk pembaruan.',
            'RENEW'                    => 'Sertifikat Elektronik sedang dalam proses pembaruan.',
            'WAITING_FOR_VERIFICATION' => 'Sertifikat Elektronik sedang dalam proses verifikasi.',
            'NEW'                      => 'Sertifikat Elektronik belum diaktivasi. Silakan aktivasi terlebih dahulu.',
            'NO_CERTIFICATE'           => 'Pengguna terdaftar namun belum memiliki Sertifikat Elektronik.',
            'NOT_REGISTERED'           => 'Pengguna belum terdaftar di BSrE. Hubungi Diskominfo setempat.',
            'SUSPEND'                  => 'Akun pengguna dalam kondisi suspend.',
            'REVOKE'                   => 'Sertifikat Elektronik telah dicabut.',
            default                    => "Status tidak dikenali: {$status}",
        };
    }
}
