<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BantuanSosialExport;
use App\Exports\PenerimaBantuanSosialExport;
use App\Exports\PengaduanExport;
use App\Exports\UmkmExport;
use App\Exports\SuratPengajuanExport;
use App\Exports\PendudukExport;
use App\Exports\PendudukTemplateExport;
use App\Exports\KartuKeluargaExport;
use App\Imports\BantuanSosialImport;
use App\Imports\UmkmImport;
use App\Imports\PendudukImport;
use App\Models\Dusun;
use App\Models\KartuKeluarga;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\WilayahImportConflict;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ExportImportController extends Controller
{
        public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:export.view');
    }

    /**
     * Export Bantuan Sosial
     */
    public function exportBantuanSosial(Request $request)
    {
        $filters = $request->only(['program', 'jenis', 'tahun']);
        $filename = 'bantuan_sosial_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new BantuanSosialExport($filters), $filename);
    }

    /**
     * Export Penerima Bantuan Sosial
     */
    public function exportPenerimaBantuanSosial(Request $request)
    {
        $filters = $request->only(['program', 'tahun', 'dusun']);
        $filename = 'penerima_bantuan_sosial_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PenerimaBantuanSosialExport($filters), $filename);
    }

    /**
     * Export Pengaduan
     */
    public function exportPengaduan(Request $request)
    {
        $filters = $request->only(['status', 'kategori', 'tahun', 'bulan']);
        $filename = 'pengaduan_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PengaduanExport($filters), $filename);
    }

    /**
     * Export UMKM
     */
    public function exportUmkm(Request $request)
    {
        $filters = $request->only(['jenis_usaha', 'status_usaha', 'is_unggulan', 'is_verified']);
        $filename = 'umkm_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new UmkmExport($filters), $filename);
    }

    /**
     * Export Surat Pengajuan
     */
    public function exportSuratPengajuan(Request $request)
    {
        $filters = $request->only(['jenis_surat', 'status', 'tahun', 'bulan']);
        $filename = 'surat_pengajuan_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new SuratPengajuanExport($filters), $filename);
    }

    /**
     * Export Penduduk
     */
    public function exportPenduduk(Request $request)
    {
        $filename = 'penduduk_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new PendudukExport($request), $filename);
    }

    /**
     * Export Kartu Keluarga
     */
    public function exportKartuKeluarga(Request $request)
    {
        $filters = $request->only(['dusun', 'rt', 'rw']);
        $filename = 'kartu_keluarga_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new KartuKeluargaExport($filters), $filename);
    }


    /**
     * Import Bantuan Sosial
     */
    public function importBantuanSosial(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            Excel::import(new BantuanSosialImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data bantuan sosial berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Import UMKM
     */
    public function importUmkm(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            Excel::import(new UmkmImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data UMKM berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Preview Import Penduduk (valid/invalid summary)
     */
    public function previewPenduduk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $sheets = Excel::toArray([], $request->file('file'));
            $rows = $sheets[0] ?? [];

            if (count($rows) < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'File kosong atau tidak memiliki data baris.'
                ], 422);
            }

            $rawHeader = $rows[0] ?? [];
            $headers = array_map(function ($h) {
                $text = trim((string) $h);
                // normalize: lower, remove punctuation/symbols, collapse spaces
                $text = Str::lower($text);
                $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                return trim($text);
            }, $rawHeader);

            $findHeaderIndex = function (array $headers, array $candidates) {
                foreach ($headers as $idx => $h) {
                    foreach ($candidates as $candidate) {
                        if ($h === $candidate || str_contains($h, $candidate)) {
                            return $idx;
                        }
                    }
                }
                return false;
            };

            $nikIndex = $findHeaderIndex($headers, ['nik', 'nomor induk kependudukan']);
            $namaIndex = $findHeaderIndex($headers, ['nama', 'nama lengkap']);
            $nkkIndex = $findHeaderIndex($headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']);

            if ($nikIndex === false || $namaIndex === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Header wajib tidak ditemukan. Gunakan kolom yang mengandung NIK dan Nama (contoh: NIK, Nama, No. KK, dst).',
                    'detected_headers' => $rawHeader
                ], 422);
            }

            $seenNik = [];
            $validRows = [];
            $invalidRows = [];
            $columnErrorCounts = [
                'nik' => 0,
                'nama' => 0,
                'nkk' => 0,
            ];

            foreach (array_slice($rows, 1) as $i => $row) {
                $rowNumber = $i + 2; // +1 header +1 1-indexed

                $nikRaw = isset($row[$nikIndex]) ? trim((string) $row[$nikIndex]) : '';
                // handle exported format that prefixes apostrophe to force text in Excel
                $nik = preg_replace('/\D+/', '', $nikRaw);
                $nama = isset($row[$namaIndex]) ? trim((string) $row[$namaIndex]) : '';
                $nkk = ($nkkIndex !== false && isset($row[$nkkIndex])) ? trim((string) $row[$nkkIndex]) : null;

                // skip truly empty row
                if ($nik === '' && $nama === '') {
                    continue;
                }

                $errors = [];
                if ($nik === '') {
                    $errors['nik'][] = 'NIK wajib diisi';
                    $columnErrorCounts['nik']++;
                }
                if ($nama === '') {
                    $errors['nama'][] = 'Nama wajib diisi';
                    $columnErrorCounts['nama']++;
                }
                if ($nik !== '' && strlen($nik) > 16) {
                    $errors['nik'][] = 'NIK maksimal 16 karakter';
                    $columnErrorCounts['nik']++;
                }
                if ($nik !== '' && isset($seenNik[$nik])) {
                    $errors['nik'][] = 'NIK duplikat di file';
                    $columnErrorCounts['nik']++;
                }

                if ($nkk !== null && $nkk !== '' && strlen(preg_replace('/\D+/', '', $nkk)) > 16) {
                    $errors['nkk'][] = 'No. KK maksimal 16 digit';
                    $columnErrorCounts['nkk']++;
                }

                if ($nik !== '') {
                    $exists = \App\Models\Penduduk::where('nik', $nik)->exists();
                    if ($exists) {
                        $errors['nik'][] = 'NIK sudah terdaftar di database (duplikat tidak diizinkan)';
                        $columnErrorCounts['nik']++;
                    }
                }

                $preview = [
                    'row' => $rowNumber,
                    'nik' => $nik,
                    'nama' => $nama,
                    'nkk' => $nkk,
                ];

                $fatalErrors = collect($errors)
                    ->except(['nik_info'])
                    ->flatten()
                    ->values()
                    ->all();

                if (empty($fatalErrors)) {
                    if (!empty($errors['nik_info'])) {
                        $preview['info'] = $errors['nik_info'];
                    }
                    $validRows[] = $preview;
                } else {
                    $preview['errors_by_column'] = $errors;
                    $preview['errors'] = $fatalErrors;
                    $invalidRows[] = $preview;
                }

                if ($nik !== '') {
                    $seenNik[$nik] = true;
                }
            }

            $invalidLimit = 200;
            $validLimit = 50;

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_data_rows' => count($validRows) + count($invalidRows),
                    'valid_rows' => count($validRows),
                    'invalid_rows' => count($invalidRows),
                    'column_error_counts' => $columnErrorCounts,
                ],
                'preview' => [
                    'valid' => array_slice($validRows, 0, $validLimit),
                    'invalid' => array_slice($invalidRows, 0, $invalidLimit),
                    'valid_shown' => min(count($validRows), $validLimit),
                    'invalid_shown' => min(count($invalidRows), $invalidLimit),
                    'valid_total' => count($validRows),
                    'invalid_total' => count($invalidRows),
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download laporan baris invalid dari file preview penduduk
     */
    public function downloadPendudukInvalidReport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            $sheets = Excel::toArray([], $request->file('file'));
            $rows = $sheets[0] ?? [];

            if (count($rows) < 2) {
                return redirect()->back()->with('error', 'File kosong atau tidak memiliki data baris.');
            }

            $rawHeader = $rows[0] ?? [];
            $headers = array_map(function ($h) {
                $text = trim((string) $h);
                $text = Str::lower($text);
                $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                return trim($text);
            }, $rawHeader);

            $findHeaderIndex = function (array $headers, array $candidates) {
                foreach ($headers as $idx => $h) {
                    foreach ($candidates as $candidate) {
                        if ($h === $candidate || str_contains($h, $candidate)) {
                            return $idx;
                        }
                    }
                }
                return false;
            };

            $nikIndex = $findHeaderIndex($headers, ['nik', 'nomor induk kependudukan']);
            $namaIndex = $findHeaderIndex($headers, ['nama', 'nama lengkap']);
            $nkkIndex = $findHeaderIndex($headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']);

            if ($nikIndex === false || $namaIndex === false) {
                return redirect()->back()->with('error', 'Header wajib tidak ditemukan. Gunakan kolom NIK dan Nama.');
            }

            $seenNik = [];
            $invalidRows = [];

            foreach (array_slice($rows, 1) as $i => $row) {
                $rowNumber = $i + 2;

                $nikRaw = isset($row[$nikIndex]) ? trim((string) $row[$nikIndex]) : '';
                $nik = preg_replace('/\D+/', '', $nikRaw);
                $nama = isset($row[$namaIndex]) ? trim((string) $row[$namaIndex]) : '';
                $nkk = ($nkkIndex !== false && isset($row[$nkkIndex])) ? trim((string) $row[$nkkIndex]) : null;

                if ($nik === '' && $nama === '') continue;

                $errors = [];
                if ($nik === '') $errors['nik'][] = 'NIK wajib diisi';
                if ($nama === '') $errors['nama'][] = 'Nama wajib diisi';
                if ($nik !== '' && strlen($nik) > 16) $errors['nik'][] = 'NIK maksimal 16 karakter';
                if ($nik !== '' && isset($seenNik[$nik])) $errors['nik'][] = 'NIK duplikat di file';
                if ($nkk !== null && $nkk !== '' && strlen(preg_replace('/\D+/', '', $nkk)) > 16) $errors['nkk'][] = 'No. KK maksimal 16 digit';
                if ($nik !== '' && \App\Models\Penduduk::where('nik', $nik)->exists()) $errors['nik'][] = 'NIK sudah terdaftar di database';

                if (!empty($errors)) {
                    $invalidRows[] = [
                        'baris' => $rowNumber,
                        'nik' => $nik,
                        'nama' => $nama,
                        'nkk' => $nkk,
                        'error_nik' => isset($errors['nik']) ? implode(' | ', $errors['nik']) : '',
                        'error_nama' => isset($errors['nama']) ? implode(' | ', $errors['nama']) : '',
                        'error_nkk' => isset($errors['nkk']) ? implode(' | ', $errors['nkk']) : '',
                    ];
                }

                if ($nik !== '') $seenNik[$nik] = true;
            }

            if (empty($invalidRows)) {
                return redirect()->back()->with('success', 'Tidak ada baris invalid. Semua data valid.');
            }

            $filename = 'invalid_rows_penduduk_' . now()->format('Ymd_His') . '.xlsx';

            return Excel::download(new class($invalidRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private array $rows) {}
                public function headings(): array
                {
                    return ['Baris', 'NIK', 'Nama', 'No. KK', 'Error NIK', 'Error Nama', 'Error No. KK'];
                }
                public function array(): array
                {
                    return array_map(function ($r) {
                        return [
                            $r['baris'] ?? '',
                            $r['nik'] ?? '',
                            $r['nama'] ?? '',
                            $r['nkk'] ?? '',
                            $r['error_nik'] ?? '',
                            $r['error_nama'] ?? '',
                            $r['error_nkk'] ?? '',
                        ];
                    }, $this->rows);
                }
            }, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat laporan invalid: ' . $e->getMessage());
        }
    }

    /**
     * Import Penduduk
     */
    public function importPenduduk(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        $batchId = 'webimp-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6));

        try {
            $sheets = Excel::toArray([], $request->file('file'));
            $rows = $sheets[0] ?? [];

            if (count($rows) < 2) {
                return redirect()->back()->with('error', 'File kosong atau tidak memiliki data baris.');
            }

            $rawHeader = $rows[0] ?? [];
            $headers = array_map(function ($h) {
                $text = trim((string) $h);
                $text = Str::lower($text);
                $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
                $text = preg_replace('/\s+/', ' ', $text);
                return trim($text);
            }, $rawHeader);

            $findHeaderIndex = function (array $headers, array $candidates) {
                foreach ($headers as $idx => $h) {
                    foreach ($candidates as $candidate) {
                        if ($h === $candidate || str_contains($h, $candidate)) {
                            return $idx;
                        }
                    }
                }
                return false;
            };

            $nikIndex = $findHeaderIndex($headers, ['nik', 'nomor induk kependudukan']);
            $namaIndex = $findHeaderIndex($headers, ['nama', 'nama lengkap']);
            $nkkIndex = $findHeaderIndex($headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']);
            $jkIndex = $findHeaderIndex($headers, ['jenis kelamin', 'jenis_kelamin']);
            $alamatIndex = $findHeaderIndex($headers, ['alamat']);
            $rtIndex = $findHeaderIndex($headers, ['rt']);
            $rwIndex = $findHeaderIndex($headers, ['rw']);
            $dusunIndex = $findHeaderIndex($headers, ['dusun']);
            $ttlIndex = $findHeaderIndex($headers, ['tanggal lahir', 'tanggal_lahir', 'tgl lahir']);
            $tempatLahirIndex = $findHeaderIndex($headers, ['tempat lahir', 'tempat_lahir']);
            $agamaIndex = $findHeaderIndex($headers, ['agama']);
            $statusPerkawinanIndex = $findHeaderIndex($headers, ['status perkawinan', 'status_perkawinan']);
            $kedudukanKeluargaIndex = $findHeaderIndex($headers, ['kedudukan keluarga', 'kedudukan_keluarga', 'kedudukan dalam keluarga']);
            $pendidikanIndex = $findHeaderIndex($headers, ['pendidikan']);
            $pekerjaanIndex = $findHeaderIndex($headers, ['pekerjaan']);
            $namaAyahIndex = $findHeaderIndex($headers, ['nama ayah', 'nama_ayah']);
            $namaIbuIndex = $findHeaderIndex($headers, ['nama ibu', 'nama_ibu']);
            $keteranganIndex = $findHeaderIndex($headers, ['keterangan', 'catatan']);

            if ($nikIndex === false || $namaIndex === false) {
                return redirect()->back()->with('error', 'Header wajib NIK dan Nama tidak ditemukan.');
            }

            $summary = [
                'imported' => 0,
                'issues' => 0,
                'issue_wilayah' => 0,
                'issue_nik' => 0,
                'issue_required' => 0,
            ];

            DB::beginTransaction();

            foreach (array_slice($rows, 1) as $i => $row) {
                $rowNumber = $i + 2;
                $payloadRaw = [];
                foreach ($rawHeader as $idx => $head) {
                    $payloadRaw[(string) $head] = $row[$idx] ?? null;
                }

                $nik = preg_replace('/\D+/', '', trim((string)($row[$nikIndex] ?? '')));
                $nama = trim((string)($row[$namaIndex] ?? ''));
                $nkk = $nkkIndex !== false ? preg_replace('/\D+/', '', trim((string)($row[$nkkIndex] ?? ''))) : '';

                $rwRaw = $rwIndex !== false ? (string)($row[$rwIndex] ?? '') : '001';
                $rtRaw = $rtIndex !== false ? (string)($row[$rtIndex] ?? '') : '001';
                $dusunRaw = $dusunIndex !== false ? (string)($row[$dusunIndex] ?? '') : '';

                if (!$nik || !$nama) {
                    $summary['issues']++;
                    $summary['issue_required']++;
                    $this->storeWebImportIssue($batchId, $request->file('file')->getClientOriginalName(), 'required_field_missing', 'NIK atau nama kosong/tidak valid', $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, [], $payloadRaw);
                    continue;
                }

                if (strlen($nik) !== 16) {
                    $summary['issues']++;
                    $summary['issue_required']++;
                    $this->storeWebImportIssue($batchId, $request->file('file')->getClientOriginalName(), 'required_field_missing', 'NIK harus tepat 16 digit', $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, [], $payloadRaw);
                    continue;
                }

                if (!$nkk || strlen($nkk) !== 16) {
                    $summary['issues']++;
                    $summary['issue_required']++;
                    $this->storeWebImportIssue($batchId, $request->file('file')->getClientOriginalName(), 'required_field_missing', 'NKK harus tepat 16 digit', $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, [], $payloadRaw);
                    continue;
                }

                $wilayah = $this->resolveWilayahForWebImport($rwRaw, $rtRaw, $dusunRaw);
                if (($wilayah['status'] ?? '') === 'conflict') {
                    $summary['issues']++;
                    $summary['issue_wilayah']++;
                    $this->storeWebImportIssue($batchId, $request->file('file')->getClientOriginalName(), 'wilayah_conflict', (string)($wilayah['reason'] ?? 'Konflik wilayah'), $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $wilayah, $payloadRaw);
                    continue;
                }

                $existingNik = Penduduk::where('nik', $nik)->first();
                if ($existingNik && trim((string)$existingNik->nkk) !== trim((string)$nkk)) {
                    $summary['issues']++;
                    $summary['issue_nik']++;
                    $this->storeWebImportIssue($batchId, $request->file('file')->getClientOriginalName(), 'nik_conflict', 'NIK sudah ada dengan NKK berbeda', $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, ['existing_nkk' => $existingNik->nkk], $payloadRaw);
                    continue;
                }

                $kartuKeluargaId = $this->upsertKartuKeluargaAndGetId($nkk, [
                    'alamat' => $alamatIndex !== false ? (trim((string)($row[$alamatIndex] ?? '')) ?: 'Alamat tidak diketahui') : 'Alamat tidak diketahui',
                    'rt' => $wilayah['rt_kode'],
                    'rw' => $wilayah['rw_kode'],
                    'dusun' => $wilayah['dusun_nama'],
                ]);

                $tempatLahir = $tempatLahirIndex !== false ? trim((string)($row[$tempatLahirIndex] ?? '')) : '';
                $agama = $agamaIndex !== false ? trim((string)($row[$agamaIndex] ?? '')) : '';
                $statusPerkawinan = $statusPerkawinanIndex !== false ? trim((string)($row[$statusPerkawinanIndex] ?? '')) : '';
                $kedudukanKeluarga = $kedudukanKeluargaIndex !== false ? trim((string)($row[$kedudukanKeluargaIndex] ?? '')) : '';
                $pendidikan = $pendidikanIndex !== false ? trim((string)($row[$pendidikanIndex] ?? '')) : '';
                $pekerjaan = $pekerjaanIndex !== false ? trim((string)($row[$pekerjaanIndex] ?? '')) : '';
                $namaAyah = $namaAyahIndex !== false ? trim((string)($row[$namaAyahIndex] ?? '')) : '';
                $namaIbu = $namaIbuIndex !== false ? trim((string)($row[$namaIbuIndex] ?? '')) : '';
                $keterangan = $keteranganIndex !== false ? trim((string)($row[$keteranganIndex] ?? '')) : '';

                $payload = [
                    'kartu_keluarga_id' => $kartuKeluargaId,
                    'nkk' => $nkk,
                    'nik' => $nik,
                    'nama' => $nama,
                    'jenis_kelamin' => $this->mapJenisKelaminSimple((string)($jkIndex !== false ? ($row[$jkIndex] ?? '') : '')),
                    'tempat_lahir' => $tempatLahir !== '' ? $tempatLahir : 'Tidak diketahui',
                    'tanggal_lahir' => $this->parseDateSimple((string)($ttlIndex !== false ? ($row[$ttlIndex] ?? '') : '')),
                    'agama' => $agama !== '' ? $agama : 'Islam',
                    'status_perkawinan' => $statusPerkawinan !== '' ? $statusPerkawinan : null,
                    'kedudukan_keluarga' => $kedudukanKeluarga !== '' ? $kedudukanKeluarga : null,
                    'pendidikan' => $pendidikan !== '' ? $pendidikan : 'Tidak/Belum Sekolah',
                    'pekerjaan' => $pekerjaan !== '' ? $pekerjaan : '-',
                    'nama_ayah' => $namaAyah !== '' ? $namaAyah : null,
                    'nama_ibu' => $namaIbu !== '' ? $namaIbu : null,
                    'keterangan' => $keterangan !== '' ? $keterangan : null,
                    'alamat' => $alamatIndex !== false ? (trim((string)($row[$alamatIndex] ?? '')) ?: 'Alamat tidak diketahui') : 'Alamat tidak diketahui',
                    'rt' => $wilayah['rt_kode'],
                    'rw' => $wilayah['rw_kode'],
                    'dusun' => $wilayah['dusun_nama'],
                ];

                $existingAny = Penduduk::withTrashed()->where('nik', $nik)->first();
                if ($existingAny) {
                    if (method_exists($existingAny, 'trashed') && $existingAny->trashed()) {
                        $existingAny->restore();
                    }
                    $existingAny->update($payload);
                } else {
                    Penduduk::create($payload);
                }

                $summary['imported']++;
            }

            DB::commit();

            $queueUrl = route('settings.wilayah.import-conflicts.index', ['batch_id' => $batchId]);
            $msg = "Import selesai. Imported: {$summary['imported']} | Issues: {$summary['issues']} (wilayah: {$summary['issue_wilayah']}, nik: {$summary['issue_nik']}, required: {$summary['issue_required']}). Batch: {$batchId}. Lihat queue: {$queueUrl}";

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Download Template Import
     */
    public function downloadTemplate($type)
    {
        $templates = [
            'bantuan_sosial' => 'template_bantuan_sosial.xlsx',
            'umkm' => 'template_umkm.xlsx',
            'penduduk' => 'template_penduduk.xlsx',
        ];

        if (!isset($templates[$type])) {
            return redirect()->back()->with('error', 'Template tidak ditemukan!');
        }

        $templatePath = storage_path('app/templates/' . $templates[$type]);

        // Untuk penduduk: pakai template dinamis biar format rapi konsisten seperti export
        if ($type === 'penduduk') {
            return Excel::download(new PendudukTemplateExport, 'template_penduduk.xlsx');
        }

        if (file_exists($templatePath)) {
            return response()->download($templatePath);
        }

        return redirect()->back()->with('error', 'File template tidak ditemukan!');
    }

    private function upsertKartuKeluargaAndGetId(string $nkk, array $attrs = []): int
    {
        $kk = KartuKeluarga::firstOrCreate(
            ['nkk' => $nkk],
            [
                'alamat' => $attrs['alamat'] ?? null,
                'rt' => $attrs['rt'] ?? null,
                'rw' => $attrs['rw'] ?? null,
                'dusun' => $attrs['dusun'] ?? null,
            ]
        );

        return (int)$kk->id;
    }

    private function normalizeKodeWilayah(?string $value, string $default = '001'): string
    {
        $clean = preg_replace('/\D+/', '', (string)$value);
        if (!$clean) {
            return $default;
        }
        return str_pad(substr($clean, 0, 3), 3, '0', STR_PAD_LEFT);
    }

    private function resolveWilayahForWebImport(string $rwRaw, string $rtRaw, ?string $dusunRaw = null): array
    {
        $rwKode = $this->normalizeKodeWilayah($rwRaw, '001');
        $rtKode = $this->normalizeKodeWilayah($rtRaw, '001');

        $rw = Rw::where('kode', $rwKode)->first();
        $rtAny = Rt::where('kode', $rtKode)->first();

        if ($rw && $rtAny && (int)$rtAny->rw_id !== (int)$rw->id) {
            return ['status' => 'conflict', 'reason' => "RT {$rtKode} sudah terdaftar di RW lain"];
        }

        if (!$rw) {
            $rw = Rw::create([
                'kode' => $rwKode,
                'nama' => "RW {$rwKode}",
                'is_active' => true,
                'is_auto_generated' => true,
                'needs_review' => true,
            ]);
        }

        $rt = Rt::where('kode', $rtKode)->where('rw_id', $rw->id)->first();
        if (!$rt) {
            $dusun = null;
            $dusunName = trim((string)$dusunRaw);
            if ($dusunName !== '') {
                $dusun = Dusun::firstOrCreate(
                    ['nama' => $dusunName],
                    [
                        'kode' => strtoupper(substr(preg_replace('/\s+/', '', $dusunName), 0, 12)),
                        'is_active' => true,
                        'is_auto_generated' => true,
                        'needs_review' => true,
                    ]
                );
            }

            $rt = Rt::create([
                'kode' => $rtKode,
                'rw_id' => $rw->id,
                'dusun_id' => $dusun?->id,
                'nama' => "RT {$rtKode}",
                'is_active' => true,
                'is_auto_generated' => true,
                'needs_review' => true,
            ]);
        }

        return [
            'status' => 'ok',
            'rw_kode' => $rw->kode,
            'rt_kode' => $rt->kode,
            'dusun_nama' => optional($rt->dusun)->nama,
        ];
    }

    private function storeWebImportIssue(
        string $batchId,
        string $sourceFile,
        string $issueType,
        string $reason,
        int $rowNumber,
        string $nik,
        string $nama,
        string $nkk,
        string $rwRaw,
        string $rtRaw,
        string $dusunRaw,
        array $meta = [],
        array $payloadRaw = []
    ): void {
        WilayahImportConflict::create([
            'batch_id' => $batchId,
            'source_file' => $sourceFile,
            'sheet_name' => 'Sheet1',
            'row_number' => $rowNumber,
            'nik' => $nik ?: null,
            'nama' => $nama ?: null,
            'nkk' => $nkk ?: null,
            'rw_raw' => $rwRaw ?: null,
            'rt_raw' => $rtRaw ?: null,
            'dusun_raw' => $dusunRaw ?: null,
            'reason' => $reason,
            'issue_type' => $issueType,
            'status' => 'pending',
            'meta' => $meta,
            'payload_raw' => $payloadRaw,
        ]);
    }

    private function mapJenisKelaminSimple(string $value): string
    {
        $v = strtolower(trim($value));
        if (in_array($v, ['p', 'perempuan', 'female', 'wanita', 'pr'])) return 'P';
        return 'L';
    }

    private function parseDateSimple(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') return null;

        try {
            if (is_numeric($value)) {
                $excelEpoch = new \DateTime('1900-01-01');
                $excelEpoch->add(new \DateInterval('P' . (intval($value) - 2) . 'D'));
                return $excelEpoch->format('Y-m-d');
            }
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Show Export/Import Page
     */
    public function index()
    {
        return view('export-import.index');
    }
}
