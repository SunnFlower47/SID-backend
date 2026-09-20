<?php

namespace App\Services\Pelayanan;

use App\Models\SuratPengajuan;
use App\Models\SuratType;
use App\Models\DesaSetting;
use App\Services\Pelayanan\SuratService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SuratPengajuanService
{
    public function __construct(protected SuratService $suratService) {}

    /**
     * Build query for the index listing with filters applied.
     */
    public function getFilteredQuery(Request $request)
    {
        $query = SuratPengajuan::with(['penduduk' => function ($q) {
            $q->withTrashed();
        }, 'admin']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_surat')) {
            $query->where('jenis_surat', $request->jenis_surat);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhereHas('penduduk', function ($pq) use ($search) {
                      $pq->where('nama', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Build the unified data array used by both PDF generation and preview.
     * Single source of truth — eliminates the copy-paste duplication between
     * generatePdf() and preview() that previously existed in the controller.
     */
    public function buildSuratData(SuratPengajuan $suratPengajuan): array
    {
        $suratPengajuan->loadMissing(['penduduk.kartuKeluarga']);
        $desaSettings = DesaSetting::getDesaInfo();

        $penandatangan = $suratPengajuan->penandatangan ?? 'kepala_desa';
        $signerData = ($penandatangan === 'sekretaris_desa')
            ? DesaSetting::getSekretarisInfo()
            : DesaSetting::getKepalaDesaInfo();

        $penduduk = $suratPengajuan->penduduk;

        $data = [
            // Identitas Desa
            'desa'           => $desaSettings['nama_desa'] ?? 'Cibatu',
            'kecamatan'      => $desaSettings['kecamatan'] ?? 'Cisaat',
            'kabupaten'      => $desaSettings['kabupaten'] ?? 'Sukabumi',
            'provinsi'       => $desaSettings['provinsi'] ?? 'Jawa Barat',
            'nama_desa'      => $desaSettings['nama_desa'] ?? 'Cibatu',
            'nama_kecamatan' => $desaSettings['kecamatan'] ?? 'Cisaat',
            'nama_kabupaten' => $desaSettings['kabupaten'] ?? 'Sukabumi',
            'alamat_desa'    => DesaSetting::getValue('alamat_lengkap', 'Jl. Cibatu Km. 15, Desa Cibatu'),

            // Relasi dan signer
            'pengajuan'      => $suratPengajuan,
            'penduduk'       => $penduduk,
            'desa_info'      => $desaSettings,
            'kepala_desa'    => $signerData,
            'is_sekdes'      => ($penandatangan === 'sekretaris_desa'),

            // Data surat
            'nomor_surat'         => $suratPengajuan->nomor_surat,
            'tanggal_surat'       => $suratPengajuan->tanggal_surat,
            'tanggal_lahir'       => $penduduk?->tanggal_lahir,
            'keperluan'           => $suratPengajuan->keperluan,
            'tujuan'              => $suratPengajuan->tujuan,
            'keterangan_tambahan' => $suratPengajuan->keterangan_tambahan,
            'data_tambahan'       => $suratPengajuan->data_tambahan ?? [],

            // Format tambahan
            'jenis_kelamin' => match ($penduduk?->jenis_kelamin) {
                'L' => 'Laki-laki',
                'P' => 'Perempuan',
                default => $penduduk?->jenis_kelamin,
            },
            'umur' => $penduduk?->usia,
            'bulan_romawi' => DesaSetting::intToRoman(
                Carbon::parse($suratPengajuan->tanggal_surat)->format('n')
            ),
            'tahun_surat' => Carbon::parse($suratPengajuan->tanggal_surat)->format('Y'),
        ];

        // Generate QR Code if token exists
        if ($suratPengajuan->qr_token) {
            $baseUrl = DesaSetting::getValue('website', url('/'));
            $linkVerifikasi = rtrim($baseUrl, '/') . '/verifikasi/surat/' . $suratPengajuan->qr_token;
            $data['link_verifikasi'] = $linkVerifikasi;

            $qrDir = storage_path('app/private/qr_codes');
            if (!file_exists($qrDir)) {
                mkdir($qrDir, 0755, true);
            }
            $qrPath = $qrDir . '/' . $suratPengajuan->qr_token . '.png';
            if (!file_exists($qrPath)) {
                $options = new \chillerlan\QRCode\QROptions([
                    'version'         => 5,
                    'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                    'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
                    'scale'           => 5,
                    'outputBase64'    => false,
                ]);
                $qrcode = new \chillerlan\QRCode\QRCode($options);
                $qrcode->render($linkVerifikasi, $qrPath);
            }

            $data['qr_code'] = [
                'type' => 'image',
                'path' => $qrPath,
                'width' => 90,
                'height' => 90,
                'ratio' => true
            ];
            $data['qr_code_sm'] = [
                'type' => 'image',
                'path' => $qrPath,
                'width' => 60,
                'height' => 60,
                'ratio' => true
            ];
            $data['qr_code_xs'] = [
                'type' => 'image',
                'path' => $qrPath,
                'width' => 45,
                'height' => 45,
                'ratio' => true
            ];
        } else {
            $data['link_verifikasi'] = 'Menunggu Persetujuan Admin';
            $data['qr_code'] = '[ QR Code akan muncul di sini setelah disetujui ]';
            $data['qr_code_sm'] = '[ QR Code akan muncul di sini setelah disetujui ]';
            $data['qr_code_xs'] = '[ QR Code akan muncul di sini setelah disetujui ]';
        }

        // Auto-map semua field penduduk, translate ID relasi ke label teks
        if ($penduduk) {
            foreach ($penduduk->toArray() as $key => $value) {
                if ($key === 'rt_id')    $data['rt']    = $penduduk->rt_label    ?? $value;
                elseif ($key === 'rw_id')    $data['rw']    = $penduduk->rw_label    ?? $value;
                elseif ($key === 'dusun_id') $data['dusun'] = $penduduk->dusun_label ?? $value;
                elseif ($key === 'alamat' && is_string($value)) $data[$key] = ucwords(strtolower($value));
                else $data[$key] = $value;
            }
        }

        // Merge data_tambahan (data spesifik per jenis surat)
        $dataTambahan = $suratPengajuan->data_tambahan;
        if ($dataTambahan) {
            if (is_string($dataTambahan)) {
                $dataTambahan = json_decode($dataTambahan, true);
            }
            if (is_array($dataTambahan) && !empty($dataTambahan)) {
                $flattened = \Illuminate\Support\Arr::dot($dataTambahan);
                foreach ($flattened as $key => $value) {
                    $newKey = str_replace('.', '_', $key);
                    
                    if (str_contains($newKey, 'alamat') && is_string($value)) {
                        $data[$newKey] = ucwords(strtolower($value));
                    } else {
                        $data[$newKey] = $value;
                    }
                }
            }
        }

        // Backwards/Forwards compatibility for Domisili templates
        // We ensure BOTH standard (nama) and dm_ prefixed (dm_nama) are available 
        // so any existing templates won't break, and new templates can be simplified.
        if (in_array($suratPengajuan->jenis_surat, ['keterangan-domisili', 'domisili'])) {
            $domisiliMapping = ['nik', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'status_perkawinan', 'kewarganegaraan', 'pekerjaan', 'asal_daerah', 'alamat_asal', 'alamat_tinggal', 'rt', 'rw', 'dusun', 'tanggal_masuk', 'tanggal_berlaku', 'perpanjangan_ke', 'keperluan'];
            
            // Map IDs to labels for standard keys if they exist in dataTambahan but not labels
            if (isset($data['rt_id']) && !isset($data['rt'])) {
                $rt = \App\Models\Rt::find($data['rt_id']);
                if ($rt) $data['rt'] = $rt->kode;
            }
            if (isset($data['rw_id']) && !isset($data['rw'])) {
                $rw = \App\Models\Rw::find($data['rw_id']);
                if ($rw) $data['rw'] = $rw->kode;
            }
            if (isset($data['dusun_id']) && !isset($data['dusun'])) {
                $dusun = \App\Models\Dusun::find($data['dusun_id']);
                if ($dusun) $data['dusun'] = $dusun->nama;
            }

            foreach ($domisiliMapping as $field) {
                // If it has standard field, copy to dm_
                if (isset($data[$field]) && !isset($data["dm_{$field}"])) {
                    $data["dm_{$field}"] = $data[$field];
                }
                // If it has dm_ field, copy to standard
                if (isset($data["dm_{$field}"]) && !isset($data[$field])) {
                    $data[$field] = $data["dm_{$field}"];
                }
            }
        }

        return $data;
    }

    /**
     * Format tanggal untuk template Word (format panjang Indonesia).
     */
    public function formatDataForWord(array $data, SuratPengajuan $suratPengajuan): array
    {
        $data['tanggal_surat'] = Carbon::parse($suratPengajuan->tanggal_surat)->isoFormat('D MMMM Y');

        $tglLahir = $data['tanggal_lahir'] ?? $suratPengajuan->penduduk?->tanggal_lahir;
        if ($tglLahir) {
            $data['tanggal_lahir'] = Carbon::parse($tglLahir)->isoFormat('D MMMM Y');
        }

        // Format tanggal khusus domisili
        if (isset($data['dm_tanggal_lahir'])) {
            $data['dm_tanggal_lahir'] = Carbon::parse($data['dm_tanggal_lahir'])->isoFormat('D MMMM Y');
        }
        if (isset($data['dm_tanggal_masuk'])) {
            $data['dm_tanggal_masuk'] = Carbon::parse($data['dm_tanggal_masuk'])->isoFormat('D MMMM Y');
        }
        if (isset($data['dm_tanggal_berlaku'])) {
            $data['dm_tanggal_berlaku'] = Carbon::parse($data['dm_tanggal_berlaku'])->isoFormat('D MMMM Y');
        }

        // Format tanggal kematian & pemakaman
        if (isset($data['kematian_tanggal'])) {
            $data['kematian_tanggal'] = Carbon::parse($data['kematian_tanggal'])->isoFormat('D MMMM Y');
        }
        if (isset($data['pemakaman_tanggal'])) {
            $data['pemakaman_tanggal'] = Carbon::parse($data['pemakaman_tanggal'])->isoFormat('D MMMM Y');
        }

        return $data;
    }

    /**
     * Generate dokumen PDF atau Word dan kembalikan Response untuk diunduh.
     * Memisahkan logika output dari controller sepenuhnya.
     */
    public function generateDocument(SuratPengajuan $suratPengajuan)
    {
        // Pengaman TTE: Jika surat sudah ditandatangani secara elektronik (TTE),
        // langsung unduh file PDF resmi yang sudah ber-TTE agar tidak merender ulang draft mentah.
        if ($suratPengajuan->is_tte && !empty($suratPengajuan->signed_pdf_path)) {
            $disk = \Illuminate\Support\Facades\Storage::disk('local');
            if ($disk->exists($suratPengajuan->signed_pdf_path)) {
                $fileName = 'Surat-TTE-' . ($suratPengajuan->nomor_surat
                    ? \Illuminate\Support\Str::slug($suratPengajuan->nomor_surat)
                    : $suratPengajuan->id) . '.pdf';
                return $disk->download($suratPengajuan->signed_pdf_path, $fileName);
            }
        }

        $suratType = SuratType::find($suratPengajuan->jenis_surat);

        if (!$suratType || !$suratType->is_active) {
            throw new \RuntimeException('Tipe surat ini sedang dinonaktifkan. Silakan aktifkan di Manajemen Tipe Surat.');
        }

        $data = $this->buildSuratData($suratPengajuan);

        // Jalur 1: Template Word (.docx)
        if ($suratType->file_template) {
            $data = $this->formatDataForWord($data, $suratPengajuan);
            $filename = str_replace(['/', '\\'], '-', $suratPengajuan->nomor_surat) . '.docx';
            $outputPath = $this->suratService->generate(
                $suratType->file_template,
                $data,
                $filename,
                $suratPengajuan->penandatangan ?? 'kepala_desa'
            );
            return response()->download($outputPath);
        }

        // Jalur 2: Blade Template (PDF)
        $templateName = $this->resolveTemplateName($suratPengajuan->jenis_surat);

        if (!$templateName || !View::exists("surat.templates.{$templateName}")) {
            throw new \RuntimeException('Template surat (Word/PDF) belum disiapkan untuk jenis ini.');
        }

        $pdf = Pdf::loadView("surat.templates.{$templateName}", $data);

        $landscapeTemplates = ['kematian', 'keterangan-domisili'];
        $orientation = in_array($templateName, $landscapeTemplates) ? 'landscape' : 'portrait';
        $pdf->setPaper([0, 0, 609.4488, 935.433], $orientation); // F4

        $filename = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $suratPengajuan->nomor_surat) . '.pdf';
        return $pdf->stream($filename);
    }

    /**
     * Update status pengajuan beserta timestamp terkait.
     */
    public function updateStatus(SuratPengajuan $suratPengajuan, array $validated, \Illuminate\Http\Request $request = null): void
    {
        $oldStatus = $suratPengajuan->status;
        
        $updateData = [
            'status'              => $validated['status'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? $suratPengajuan->keterangan_tambahan,
        ];

        if (in_array($validated['status'], ['selesai', 'diproses'])) {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = Auth::id();
            
            if (empty($suratPengajuan->qr_token)) {
                $updateData['qr_token'] = (string) \Illuminate\Support\Str::uuid();
            }

            if (empty($suratPengajuan->nomor_surat)) {
                $updateData['nomor_surat'] = $this->generateNomorSurat($suratPengajuan->jenis_surat);
            }
        }

        if ($validated['status'] === 'selesai') {
            $updateData['completed_at'] = now();
            
            // Handle optional file_balasan_admin upload
            if ($request && $request->hasFile('file_balasan_admin')) {
                $file = $request->file('file_balasan_admin');
                $filename = time() . '_balasan_' . $suratPengajuan->nomor_pengajuan . '.' . $file->getClientOriginalExtension();
                $updateData['file_balasan_admin'] = $file->storeAs('surat-balasan', $filename);
            }
        }

        $suratPengajuan->update($updateData);

        // Send Email if status changed, status is not pending, and email exists
        if ($oldStatus !== $validated['status'] && $validated['status'] !== 'pending' && $suratPengajuan->email_pengaju) {
            try {
                \Illuminate\Support\Facades\Mail::to($suratPengajuan->email_pengaju)->send(new \App\Mail\SuratStatusChangedMail($suratPengajuan));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email notifikasi status surat: ' . $e->getMessage());
            }
        }
    }

    private function generateNomorSurat($suratType)
    {
        $type = \App\Models\SuratType::where('id', $suratType)
            ->orWhere('id', 'LIKE', $suratType)
            ->first();
            
        $kodeSurat = $type ? $type->kode : 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }

    /**
     * Resolve nama template Blade berdasarkan jenis surat.
     */
    public function resolveTemplateName($suratTypeId): ?string
    {
        $type = SuratType::find($suratTypeId);
        return $type?->template_code;
    }

    /**
     * Generate banyak dokumen Word dari sub-template yang dipilih.
     * Jika 1 file → download .docx langsung.
     * Jika >1 file → buat ZIP → download ZIP.
     */
    public function generateMultiDocument(SuratPengajuan $suratPengajuan, array $templateIds)
    {
        $subTemplates = \App\Models\SuratTypeTemplate::whereIn('id', $templateIds)
            ->where('is_active', true)
            ->whereNotNull('file_template')
            ->orderBy('urutan')
            ->get();

        if ($subTemplates->isEmpty()) {
            throw new \RuntimeException('Tidak ada sub-template aktif yang dipilih atau file template belum diupload.');
        }

        // Build data surat (sama untuk semua sub-template)
        $data = $this->buildSuratData($suratPengajuan);
        $data = $this->formatDataForWord($data, $suratPengajuan);

        $generatedFiles = [];
        $nomorSafe = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $suratPengajuan->nomor_surat);

        try {
            foreach ($subTemplates as $subTemplate) {
                $filename = "{$subTemplate->kode}_{$nomorSafe}.docx";
                $outputPath = $this->suratService->generate(
                    $subTemplate->file_template,
                    $data,
                    $filename,
                    $suratPengajuan->penandatangan ?? 'kepala_desa'
                );
                $generatedFiles[] = [
                    'path'     => $outputPath,
                    'filename' => $filename,
                ];
            }

            // Jika hanya 1 file, langsung download .docx
            if (count($generatedFiles) === 1) {
                return response()->download(
                    $generatedFiles[0]['path'],
                    $generatedFiles[0]['filename']
                )->deleteFileAfterSend(false);
            }

            // Jika >1 file, buat ZIP
            $zipFilename = "surat_{$nomorSafe}.zip";
            $zipPath = storage_path('app/private/generated_surat/' . $zipFilename);

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Gagal membuat file ZIP.');
            }

            foreach ($generatedFiles as $file) {
                $zip->addFile($file['path'], $file['filename']);
            }
            $zip->close();

            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Cleanup generated files on error
            foreach ($generatedFiles as $file) {
                if (file_exists($file['path'])) {
                    @unlink($file['path']);
                }
            }
            throw $e;
        }
    }
}
