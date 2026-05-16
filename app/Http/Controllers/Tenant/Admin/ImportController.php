<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BantuanSosialImport;
use App\Imports\UmkmImport;
use App\Imports\PendudukImport;
use App\Exports\PendudukTemplateExport;
use App\Models\Dusun;
use App\Models\KartuKeluarga;
use App\Models\Mutasi;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\ImportConflict;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin_sistem');
    }

    /**
     * Display the import form.
     */
    public function index()
    {
        Gate::authorize('kependudukan');
        return view('import.index');
    }

    /**
     * Process Excel import (Basic).
     */
    public function excel(Request $request)
    {
        Gate::authorize('kependudukan');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            // Import data from Excel
            Excel::import(new PendudukImport, $request->file('file'));

            DB::commit();

            return redirect()->route('import.index')
                ->with('success', 'Data berhasil diimport dari Excel!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Excel Error: ' . $e->getMessage());
            return redirect()->route('import.index')
                ->with('error', 'Terjadi kesalahan saat mengimport data: ' . $e->getMessage());
        }
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
            $alamatIndex = $findHeaderIndex($headers, ['alamat', 'domisili']);
            $rtIndex = $findHeaderIndex($headers, ['rt']);
            $rwIndex = $findHeaderIndex($headers, ['rw']);
            $dusunIndex = $findHeaderIndex($headers, ['dusun', 'lingkungan']);

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
                'wilayah' => 0,
            ];

            foreach (array_slice($rows, 1) as $i => $row) {
                $rowNumber = $i + 2; 

                $nikRaw = isset($row[$nikIndex]) ? trim((string) $row[$nikIndex]) : '';
                $nik = preg_replace('/\D+/', '', $nikRaw);
                $nama = isset($row[$namaIndex]) ? trim((string) $row[$namaIndex]) : '';
                $nkk = ($nkkIndex !== false && isset($row[$nkkIndex])) ? trim((string) $row[$nkkIndex]) : null;
                $alamat = ($alamatIndex !== false && isset($row[$alamatIndex])) ? trim((string) $row[$alamatIndex]) : '';
                $rtRaw = ($rtIndex !== false && isset($row[$rtIndex])) ? trim((string) $row[$rtIndex]) : '';
                $rwRaw = ($rwIndex !== false && isset($row[$rwIndex])) ? trim((string) $row[$rwIndex]) : '';
                $dusunRaw = ($dusunIndex !== false && isset($row[$dusunIndex])) ? trim((string) $row[$dusunIndex]) : '';

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
                if ($nik !== '' && strlen($nik) !== 16) {
                    $errors['nik'][] = 'NIK harus 16 karakter';
                    $columnErrorCounts['nik']++;
                }
                if ($nik !== '' && isset($seenNik[$nik])) {
                    $errors['nik'][] = 'NIK duplikat di file';
                    $columnErrorCounts['nik']++;
                }

                if ($nkk !== null && $nkk !== '') {
                    $nkkClean = preg_replace('/\D+/', '', $nkk);
                    if (strlen($nkkClean) !== 16) {
                        $errors['nkk'][] = 'No. KK harus 16 digit';
                        $columnErrorCounts['nkk']++;
                    }
                }

                $wilayahRes = $this->resolveWilayahForWebImport($rwRaw, $rtRaw, $dusunRaw);
                if ($wilayahRes['status'] === 'conflict') {
                    $errors['wilayah'][] = $wilayahRes['reason'];
                    $columnErrorCounts['wilayah']++;
                } else {
                    $rwObj = Rw::find($wilayahRes['rw_id']);
                    $rtObj = Rt::find($wilayahRes['rt_id']);
                    if (($rwObj && $rwObj->needs_review) || ($rtObj && $rtObj->needs_review)) {
                        $errors['wilayah_info'] = 'Peringatan: Wilayah ini belum diverifikasi di Master';
                    }
                }

                if ($nik !== '') {
                    $existingAny = Penduduk::withTrashed()->where('nik', $nik)->first();
                    if ($existingAny) {
                        $permanentMutasi = Mutasi::where('penduduk_id', $existingAny->id)
                            ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
                            ->exists();

                        if ($permanentMutasi) {
                            $errors['nik'][] = "Terlarang: Penduduk ini sudah berstatus Meninggal/Pindah (Mutasi)";
                            $columnErrorCounts['nik']++;
                        } else {
                            $errors['nik'][] = "Review: NIK sudah ada di sistem (Perlu keputusan)";
                            $columnErrorCounts['nik']++;
                        }
                    }
                }

                $preview = [
                    'row' => $rowNumber,
                    'nik' => $nik,
                    'nama' => $nama,
                    'nkk' => $nkk,
                    'alamat' => $alamat,
                    'rt' => $rtRaw,
                    'rw' => $rwRaw,
                    'dusun' => $dusunRaw,
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

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_data_rows' => count($validRows) + count($invalidRows),
                    'valid_rows' => count($validRows),
                    'invalid_rows' => count($invalidRows),
                    'column_error_counts' => $columnErrorCounts,
                ],
                'preview' => [
                    'valid' => array_slice($validRows, 0, 50),
                    'invalid' => array_slice($invalidRows, 0, 200),
                    'valid_shown' => min(count($validRows), 50),
                    'invalid_shown' => min(count($invalidRows), 200),
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
                if ($nik !== '' && strlen($nik) !== 16) $errors['nik'][] = 'NIK harus 16 karakter';
                if ($nik !== '' && isset($seenNik[$nik])) $errors['nik'][] = 'NIK duplikat di file';
                
                if ($nkk !== null && $nkk !== '') {
                    $nkkClean = preg_replace('/\D+/', '', $nkk);
                    if (strlen($nkkClean) !== 16) $errors['nkk'][] = 'No. KK harus 16 digit';
                }

                if (!empty($errors)) {
                    $invalidRows[] = [
                        'baris' => $rowNumber,
                        'nik' => $nik,
                        'nama' => $nama,
                        'nkk' => $nkk,
                        'errors' => $errors
                    ];
                }

                if ($nik !== '') $seenNik[$nik] = true;
            }

            if (empty($invalidRows)) {
                return redirect()->back()->with('success', 'Tidak ada baris invalid.');
            }

            $filename = 'invalid_rows_penduduk_' . now()->format('Ymd_His') . '.xlsx';
            return Excel::download(new class($invalidRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function __construct(private array $rows) {}
                public function headings(): array { return ['Baris', 'NIK', 'Nama', 'No. KK', 'Error Details']; }
                public function array(): array {
                    return array_map(function ($r) {
                        return [ $r['baris'], $r['nik'], $r['nama'], $r['nkk'], json_encode($r['errors']) ];
                    }, $this->rows);
                }
            }, $filename);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
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

        $batchId    = 'webimp-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6));
        $sourceFile = $request->file('file')->getClientOriginalName();
        $now        = now()->toDateTimeString();

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $sheets = Excel::toArray([], $request->file('file'));
            $rows   = $sheets[0] ?? [];

            if (count($rows) < 2) {
                return redirect()->back()->with('error', 'File kosong.');
            }

            // ── Parse Headers ─────────────────────────────────────────────
            $rawHeader = $rows[0] ?? [];
            $headers = array_map(function ($h) {
                $text = Str::lower(trim((string)$h));
                $text = preg_replace('/[^a-z0-9\s]/', ' ', $text);
                return trim(preg_replace('/\s+/', ' ', $text));
            }, $rawHeader);

            $findIdx = function (array $headers, array $candidates) {
                foreach ($headers as $idx => $h) {
                    foreach ($candidates as $c) {
                        if ($h === $c || str_contains($h, $c)) return $idx;
                    }
                }
                return false;
            };

            $nikIdx   = $findIdx($headers, ['nik', 'nomor induk kependudukan']);
            $namaIdx  = $findIdx($headers, ['nama', 'nama lengkap']);
            $nkkIdx   = $findIdx($headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']);
            $jkIdx    = $findIdx($headers, ['jenis kelamin', 'jk']);
            $alamatIdx= $findIdx($headers, ['alamat']);
            $rtIdx    = $findIdx($headers, ['rt']);
            $rwIdx    = $findIdx($headers, ['rw']);
            $dusunIdx = $findIdx($headers, ['dusun']);
            $ttlIdx   = $findIdx($headers, ['tanggal lahir']);

            if ($nikIdx === false || $namaIdx === false) {
                return redirect()->back()->with('error', 'Header wajib NIK dan Nama tidak ditemukan.');
            }

            // ── PRE-LOAD: Wilayah ke Memory ───────────────────────────────
            // Format cache: "rw_kode:rt_kode" => ['rt_id', 'rw_id', 'dusun_id']
            $wilayahCache = [];
            Rt::with('rw')->get()->each(function ($rt) use (&$wilayahCache) {
                if (!$rt->rw) return;
                $key = $rt->rw->kode . ':' . $rt->kode;
                $wilayahCache[$key] = [
                    'status'   => 'ok',
                    'rt_id'    => $rt->id,
                    'rw_id'    => $rt->rw_id,
                    'dusun_id' => $rt->dusun_id,
                ];
            });

            // ── PRE-LOAD: Existing KKs ────────────────────────────────────
            $kkCache = KartuKeluarga::all()->keyBy('nkk'); // nkk => model

            // ── PRE-LOAD: Existing Penduduk NIKs ─────────────────────────
            $existingNiks = Penduduk::withTrashed()->pluck('nik')->flip(); // O(1) lookup

            // ── Accumulators ──────────────────────────────────────────────
            $newKksBatch      = []; // nkk => data array (for insertOrIgnore)
            $pendudukRows     = []; // nik => data array (for upsert)
            $issuesBatch      = []; // flat array for batch insert
            $affectedNkks     = []; // nkk => true
            $summary          = ['imported' => 0, 'updated' => 0, 'issues' => 0];

            // ── Process Rows ──────────────────────────────────────────────
            foreach (array_slice($rows, 1) as $i => $row) {
                $rowNumber = $i + 2;
                $nik  = preg_replace('/\D+/', '', trim((string)($row[$nikIdx]  ?? '')));
                $nama = trim((string)($row[$namaIdx] ?? ''));
                $nkk  = $nkkIdx !== false ? preg_replace('/\D+/', '', trim((string)($row[$nkkIdx] ?? ''))) : '';

                if (empty($nik) && empty($nama)) continue;

                $rwRaw   = $rwIdx    !== false ? (string)($row[$rwIdx]    ?? '') : '001';
                $rtRaw   = $rtIdx    !== false ? (string)($row[$rtIdx]    ?? '') : '001';
                $dusunRaw= $dusunIdx !== false ? (string)($row[$dusunIdx] ?? '') : '';
                $alamat  = $alamatIdx !== false ? (trim((string)($row[$alamatIdx] ?? '')) ?: 'Alamat tidak diketahui') : 'Alamat tidak diketahui';

                // Validasi NIK
                if (strlen($nik) !== 16) {
                    $summary['issues']++;
                    $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'invalid_nik', "NIK '{$nik}' tidak valid (" . strlen($nik) . " digit).", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now);
                    continue;
                }

                // Validasi NKK
                if (!empty($nkk) && strlen($nkk) !== 16) {
                    $summary['issues']++;
                    $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'invalid_nkk', "No. KK '{$nkk}' tidak valid (" . strlen($nkk) . " digit).", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now);
                    continue;
                }

                // Resolve wilayah dari cache (no DB query)
                $rwKode  = $this->normalizeKodeWilayah($rwRaw);
                $rtKode  = $this->normalizeKodeWilayah($rtRaw);
                $cacheKey = "{$rwKode}:{$rtKode}";
                if (!isset($wilayahCache[$cacheKey])) {
                    $summary['issues']++;
                    $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'wilayah_conflict', "Wilayah RT '{$rtRaw}' / RW '{$rwRaw}' belum terdaftar.", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now);
                    continue;
                }
                $wilayah = $wilayahCache[$cacheKey];

                // Track KK baru (jika belum ada di cache maupun batch)
                if ($nkk && !isset($kkCache[$nkk]) && !isset($newKksBatch[$nkk])) {
                    $newKksBatch[$nkk] = [
                        'nkk'                  => $nkk,
                        'alamat'               => $alamat,
                        'rt_id'                => $wilayah['rt_id'],
                        'rw_id'                => $wilayah['rw_id'],
                        'dusun_id'             => $wilayah['dusun_id'],
                        'nama_kepala_keluarga' => $nama,
                        'nik_kepala_keluarga'  => $nik,
                        'jumlah_anggota'       => 0,
                        'anggota_aktif'        => 0,
                        'anggota_mutasi'       => 0,
                        'anggota_meninggal'    => 0,
                        'anggota_pindah'       => 0,
                        'anggota_pisah_kk'     => 0,
                        'status_kk'            => 'normal',
                        'created_at'           => $now,
                        'updated_at'           => $now,
                    ];
                }

                $affectedNkks[$nkk] = true;

                $isNew = !isset($existingNiks[$nik]);
                $pendudukRows[$nik] = [
                    'nik'               => $nik,
                    '_nkk'              => $nkk, // temporary, dihapus sebelum insert
                    'nama'              => $nama,
                    'jenis_kelamin'     => $this->mapJenisKelaminSimple((string)($jkIdx !== false ? ($row[$jkIdx] ?? '') : '')),
                    'tanggal_lahir'     => $this->parseDateSimple((string)($ttlIdx !== false ? ($row[$ttlIdx] ?? '') : '')),
                    'deleted_at'        => null,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];

                if ($isNew) $summary['imported']++;
                else $summary['updated']++;
            }

            // ── Batch Insert KK Baru (tanpa events/activity log) ──────────
            DB::beginTransaction();

            foreach (array_chunk(array_values($newKksBatch), 200) as $chunk) {
                KartuKeluarga::withoutEvents(fn() => KartuKeluarga::insertOrIgnore($chunk));
            }

            // Reload KK cache setelah insert baru
            $allNkks = array_keys($affectedNkks);
            $freshKkMap = KartuKeluarga::whereIn('nkk', $allNkks)->get()->keyBy('nkk');

            // ── Batch Upsert Penduduk (tanpa events/activity log) ─────────
            $pendudukInsert = [];
            $affectedKkIds  = [];
            foreach ($pendudukRows as $nik => $data) {
                $kk = $freshKkMap[$data['_nkk']] ?? null;
                if (!$kk) continue;
                $affectedKkIds[] = $kk->id;
                unset($data['_nkk']);
                $data['kartu_keluarga_id'] = $kk->id;
                $pendudukInsert[] = $data;
            }

            foreach (array_chunk($pendudukInsert, 200) as $chunk) {
                Penduduk::withoutEvents(fn() =>
                    Penduduk::upsert(
                        $chunk,
                        ['nik'],
                        ['nama', 'kartu_keluarga_id', 'jenis_kelamin', 'tanggal_lahir', 'deleted_at', 'updated_at']
                    )
                );
            }

            // ── Batch Insert Issues ───────────────────────────────────────
            foreach (array_chunk($issuesBatch, 200) as $chunk) {
                ImportConflict::insert($chunk);
            }

            DB::commit();

            // ── Batch Recalculate (sekali per KK di akhir) ───────────────
            $kkService = app(\App\Services\KartuKeluargaService::class);
            foreach (array_unique($affectedKkIds) as $kkId) {
                $kkService->recalculate($kkId);
            }

            $msg = "Import selesai! ✅ Baru: {$summary['imported']} | Diperbarui: {$summary['updated']} | Issues: {$summary['issues']}";
            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Penduduk Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    /**
     * Helper: Build issue row array untuk batch insert.
     */
    private function buildIssueRow(string $batchId, string $sourceFile, string $issueType, string $reason, int $rowNumber, string $nik, string $nama, string $nkk, string $rwRaw, string $rtRaw, string $dusunRaw, string $now): array
    {
        return [
            'batch_id'    => $batchId,
            'source_file' => $sourceFile,
            'sheet_name'  => 'Sheet1',
            'row_number'  => $rowNumber,
            'nik'         => $nik ?: null,
            'nama'        => $nama ?: null,
            'nkk'         => $nkk ?: null,
            'rw_raw'      => $rwRaw ?: null,
            'rt_raw'      => $rtRaw ?: null,
            'dusun_raw'   => $dusunRaw ?: null,
            'reason'      => $reason,
            'issue_type'  => $issueType,
            'status'      => 'pending',
            'meta'        => json_encode([]),
            'payload_raw' => json_encode([]),
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
    }


    /**
     * Download Template Import
     */

    public function downloadTemplate($type)
    {
        if ($type === 'penduduk') {
            return Excel::download(new PendudukTemplateExport, 'template_penduduk.xlsx');
        }

        $templates = [
            'bantuan_sosial' => 'template_bantuan_sosial.xlsx',
            'umkm' => 'template_umkm.xlsx',
        ];

        if (!isset($templates[$type])) return redirect()->back()->with('error', 'Template tidak ditemukan!');

        $templatePath = storage_path('app/templates/' . $templates[$type]);
        if (file_exists($templatePath)) return response()->download($templatePath);

        return redirect()->back()->with('error', 'File template tidak ditemukan!');
    }

    private function normalizeKodeWilayah($value): ?string
    {
        $clean = preg_replace('/\D+/', '', (string)$value);
        return $clean ? str_pad(substr($clean, 0, 3), 3, '0', STR_PAD_LEFT) : null;
    }

    private function resolveWilayahForWebImport(string $rwRaw, string $rtRaw, ?string $dusunRaw = null): array
    {
        $rwKode = $this->normalizeKodeWilayah($rwRaw);
        $rtKode = $this->normalizeKodeWilayah($rtRaw);
        
        $rw = $rwKode ? Rw::where('kode', $rwKode)->first() : Rw::where('nama', 'like', "%{$rwRaw}%")->first();
        if ($rw) {
            $rt = $rtKode ? Rt::where('kode', $rtKode)->where('rw_id', $rw->id)->first() : Rt::where('nama', 'like', "%{$rtRaw}%")->where('rw_id', $rw->id)->first();
            if ($rt) return $this->formatResolveResult($rw, $rt);
        }

        $rtGlobal = $rtKode ? Rt::with('rw')->where('kode', $rtKode)->first() : Rt::with('rw')->where('nama', 'like', "%{$rtRaw}%")->first();
        if ($rtGlobal) return $this->formatResolveResult($rtGlobal->rw, $rtGlobal, "RT '{$rtRaw}' terdeteksi di RW {$rtGlobal->rw->kode}");

        return ['status' => 'conflict', 'reason' => "Wilayah Baru: RT '{$rtRaw}' / RW '{$rwRaw}' belum terdaftar."];
    }

    private function formatResolveResult($rw, $rt, $warning = null): array
    {
        return [
            'status' => 'ok',
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'dusun_id' => $rt->dusun_id,
            'rw_kode' => $rw->kode,
            'rt_kode' => $rt->kode,
            'dusun_nama' => optional($rt->dusun)->nama,
            'warning' => $warning
        ];
    }

    private function storeWebImportIssue(string $batchId, string $sourceFile, string $issueType, string $reason, int $rowNumber, string $nik, string $nama, string $nkk, string $rwRaw, string $rtRaw, string $dusunRaw, array $meta = [], array $payloadRaw = []): void
    {
        ImportConflict::create([
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
        $v = strtoupper(trim($value));
        if (in_array($v, ['P', 'PEREMPUAN', 'FEMALE', 'WANITA', 'PR'])) return 'PEREMPUAN';
        return 'LAKI-LAKI';
    }

    private function parseDateSimple(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || $value === '-') return null;
        try {
            if (is_numeric($value)) {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $excelDate->format('Y-m-d');
            }
            if (strpos($value, '/') !== false) return \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) { return null; }
    }
}
