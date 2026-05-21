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
            'bulan_romawi' => DesaSetting::intToRoman(
                Carbon::parse($suratPengajuan->tanggal_surat)->format('n')
            ),
            'tahun_surat' => Carbon::parse($suratPengajuan->tanggal_surat)->format('Y'),
        ];

        // Auto-map semua field penduduk, translate ID relasi ke label teks
        if ($penduduk) {
            foreach ($penduduk->toArray() as $key => $value) {
                if ($key === 'rt_id')    $data['rt']    = $penduduk->rt_label    ?? $value;
                elseif ($key === 'rw_id')    $data['rw']    = $penduduk->rw_label    ?? $value;
                elseif ($key === 'dusun_id') $data['dusun'] = $penduduk->dusun_label ?? $value;
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
                foreach ($dataTambahan as $key => $value) {
                    $data[$key] = $value;
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
        $data['tanggal_lahir'] = $suratPengajuan->penduduk?->tanggal_lahir?->isoFormat('D MMMM Y');
        return $data;
    }

    /**
     * Generate dokumen PDF atau Word dan kembalikan Response untuk diunduh.
     * Memisahkan logika output dari controller sepenuhnya.
     */
    public function generateDocument(SuratPengajuan $suratPengajuan)
    {
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
    public function updateStatus(SuratPengajuan $suratPengajuan, array $validated): void
    {
        $updateData = [
            'status'              => $validated['status'],
            'keterangan_tambahan' => $validated['keterangan_tambahan'] ?? $suratPengajuan->keterangan_tambahan,
        ];

        if (in_array($validated['status'], ['selesai', 'diproses'])) {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = auth::id();
        }

        if ($validated['status'] === 'selesai') {
            $updateData['completed_at'] = now();
        }

        $suratPengajuan->update($updateData);
    }

    /**
     * Resolve nama template Blade berdasarkan jenis surat.
     */
    public function resolveTemplateName($suratTypeId): ?string
    {
        $type = SuratType::find($suratTypeId);
        return $type?->template_code;
    }
}
