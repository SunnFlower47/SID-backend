<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\DesaSetting;
use App\Models\SuratPengajuan;
use App\Services\BsreService;
use App\Services\DocxToPdfService;
use App\Services\Pelayanan\SuratService;
use App\Services\Pelayanan\SuratPengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * TteController — Menangani Tanda Tangan Elektronik (TTE) via BSrE
 *
 * Alur:
 * 1. Admin cek status sertifikat Kepala Desa (GET /tte/{id}/status)
 * 2. Admin input passphrase, tekan tombol "Tanda Tangani" (POST /tte/{id}/sign)
 * 3. Backend: generate .docx → konversi ke PDF → kirim ke BSrE → simpan PDF TTE
 * 4. Surat siap didownload dengan TTE resmi (GET /tte/{id}/download)
 */
class TteController extends Controller
{
    public function __construct(
        protected BsreService $bsreService,
        protected DocxToPdfService $docxToPdfService,
        protected SuratService $suratService,
        protected SuratPengajuanService $suratPengajuanService,
    ) {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Cek status sertifikat elektronik pejabat (JSON response untuk AJAX).
     * Digunakan oleh frontend untuk menampilkan status sebelum tombol TTE aktif.
     *
     * GET /admin/tte/{suratPengajuan}/status
     */
    public function checkStatus(Request $request, SuratPengajuan $suratPengajuan)
    {
        // Ambil NIK pejabat yang akan digunakan dari request atau dari settings desa
        $nik = $request->query('nik') ?? DesaSetting::getValue('bsre_nik_pejabat', '');

        if (empty($nik)) {
            return response()->json([
                'success'   => false,
                'is_active' => false,
                'message'   => 'NIK pejabat belum dikonfigurasi. Silakan isi di Pengaturan Desa.',
            ]);
        }

        $result = $this->bsreService->checkStatus($nik);

        return response()->json($result);
    }

    /**
     * Proses Tanda Tangan Elektronik pada surat.
     * Menerima passphrase dari form (tidak disimpan ke database).
     *
     * POST /admin/tte/{suratPengajuan}/sign
     */
    public function sign(Request $request, SuratPengajuan $suratPengajuan)
    {
        $request->validate([
            'passphrase' => ['required', 'string', 'min:4'],
            'nik'        => ['required', 'string', 'size:16'],
        ], [
            'passphrase.required' => 'Passphrase (PIN) wajib diisi.',
            'passphrase.min'      => 'Passphrase minimal 4 karakter.',
            'nik.required'        => 'NIK pejabat penandatangan wajib diisi.',
            'nik.size'            => 'NIK harus 16 digit.',
        ]);

        // Hanya surat yang sudah selesai yang bisa di-TTE
        if (!in_array($suratPengajuan->status, ['selesai', 'diproses'])) {
            return back()->withErrors(['tte' => 'Hanya surat dengan status Diproses atau Selesai yang dapat ditandatangani.']);
        }

        // Jangan re-TTE surat yang sudah ber-TTE
        if ($suratPengajuan->is_tte) {
            return back()->withErrors(['tte' => 'Surat ini sudah memiliki Tanda Tangan Elektronik.']);
        }

        try {
            // Step 1: Generate file Word (.docx) dari template
            Log::info("TTE: Generating .docx untuk surat #{$suratPengajuan->id}");
            $docxPath = $this->generateDocxFile($suratPengajuan);

            // Step 2: Konversi .docx ke .pdf via LibreOffice Headless
            Log::info("TTE: Konversi .docx ke .pdf untuk surat #{$suratPengajuan->id}");
            $pdfDir  = storage_path('app/private/surat-pdf');
            $pdfPath = $this->docxToPdfService->convert($docxPath, $pdfDir);

            // Step 3: Buat URL verifikasi publik (menggunakan qr_token yang sudah ada)
            $qrToken = $suratPengajuan->qr_token ?? Str::uuid()->toString();
            $linkQR  = route('verifikasi-surat', ['token' => $qrToken]);

            // Step 4: Ambil informasi lokasi dari settings desa
            $namaLokasi = DesaSetting::getValue('nama_desa', '');

            // Step 5: Kirim ke BSrE untuk ditandatangani
            Log::info("TTE: Mengirim ke BSrE untuk surat #{$suratPengajuan->id}");
            $signedPdfBinary = $this->bsreService->signPdf(
                pdfPath:    $pdfPath,
                nik:        $request->nik,
                passphrase: $request->passphrase,
                linkQR:     $linkQR,
                namaLokasi: $namaLokasi,
                reason:     'Surat diterbitkan dan ditandatangani oleh ' . DesaSetting::getValue('nama_desa', 'Pemerintah Desa'),
            );

            // Step 6: Simpan PDF yang sudah ber-TTE ke storage privat
            $signedFileName = 'tte_' . $suratPengajuan->id . '_' . time() . '.pdf';
            $signedFilePath = 'surat-tte/' . $signedFileName;
            Storage::disk('local')->put($signedFilePath, $signedPdfBinary);

            // Step 7: Update record surat di database
            $suratPengajuan->update([
                'is_tte'          => true,
                'tte_at'          => now(),
                'signed_pdf_path' => $signedFilePath,
                'tte_signer_nik'  => $request->nik,
                'tte_signer_name' => DesaSetting::getValue('nama_kepala_desa', ''),
                'qr_token'        => $qrToken,
                'status'          => 'selesai', // Otomatis set ke selesai setelah TTE
            ]);

            // Step 8: Hapus file sementara (.docx dan .pdf sebelum TTE)
            @unlink($docxPath);
            @unlink($pdfPath);

            Log::info("TTE: Berhasil untuk surat #{$suratPengajuan->id}");

            return back()->with('success', 'Tanda Tangan Elektronik berhasil dibubuhkan. Surat siap diunduh.');
        } catch (\Exception $e) {
            Log::error("TTE Error untuk surat #{$suratPengajuan->id}: " . $e->getMessage());
            return back()->withErrors(['tte' => 'Proses TTE gagal: ' . $e->getMessage()]);
        }
    }

    /**
     * Download PDF yang sudah ber-TTE.
     *
     * GET /admin/tte/{suratPengajuan}/download
     */
    public function download(SuratPengajuan $suratPengajuan)
    {
        if (!$suratPengajuan->is_tte || !$suratPengajuan->signed_pdf_path) {
            abort(404, 'File TTE tidak ditemukan.');
        }

        if (!Storage::disk('local')->exists($suratPengajuan->signed_pdf_path)) {
            abort(404, 'File PDF TTE tidak ada di server. Mungkin sudah terhapus.');
        }

        $fileName = 'Surat-TTE-' . ($suratPengajuan->nomor_surat
            ? Str::slug($suratPengajuan->nomor_surat)
            : $suratPengajuan->id) . '.pdf';

        return Storage::disk('local')->download($suratPengajuan->signed_pdf_path, $fileName);
    }

    /**
     * Verifikasi keaslian PDF TTE yang diupload (opsional, untuk admin).
     *
     * POST /admin/tte/{suratPengajuan}/verify
     */
    public function verify(Request $request, SuratPengajuan $suratPengajuan)
    {
        if (!$suratPengajuan->is_tte || !$suratPengajuan->signed_pdf_path) {
            return response()->json(['success' => false, 'message' => 'Surat belum ber-TTE.']);
        }

        $pdfPath = Storage::disk('local')->path($suratPengajuan->signed_pdf_path);
        $result  = $this->bsreService->verifyPdf($pdfPath);

        return response()->json($result);
    }

    /**
     * Generate file .docx dari template surat.
     * Memanggil SuratPengajuanService untuk mendapatkan data surat,
     * lalu menggunakan SuratService untuk render template ke .docx.
     *
     * @return string Absolute path ke file .docx yang dihasilkan
     */
    private function generateDocxFile(SuratPengajuan $suratPengajuan): string
    {
        // Siapkan data variabel untuk template menggunakan method yang sudah ada
        $rawData  = $this->suratPengajuanService->buildSuratData($suratPengajuan);
        $data     = $this->suratPengajuanService->formatDataForWord($rawData, $suratPengajuan);

        // Resolve nama file template Word
        $templateName = $this->suratPengajuanService->resolveTemplateName($suratPengajuan->jenis_surat);

        if (!$templateName) {
            throw new \Exception('Template surat tidak ditemukan untuk jenis: ' . $suratPengajuan->jenis_surat);
        }

        $outputName = 'tte_temp_' . $suratPengajuan->id . '_' . time() . '.docx';

        return $this->suratService->generate($templateName, $data, $outputName);
    }
}

