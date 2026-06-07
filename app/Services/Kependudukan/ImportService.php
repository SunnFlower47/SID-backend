<?php

namespace App\Services\Kependudukan;

use App\Models\Rt;
use App\Models\Rw;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use App\Models\ImportConflict;
use App\Models\Mutasi;
use App\Imports\PendudukImport;
use App\Imports\BantuanSosialImport;
use App\Imports\UmkmImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportService
{
    /**
     * Import basic penduduk Excel using Maatwebsite Excel
     */
    public function importBasicExcel($file): void
    {
        DB::transaction(function () use ($file) {
            Excel::import(new PendudukImport, $file);
        });
    }

    /**
     * Import Bantuan Sosial Excel
     */
    public function importBantuanSosial($file): void
    {
        Excel::import(new BantuanSosialImport, $file);
    }

    /**
     * Import UMKM Excel
     */
    public function importUmkm($file): void
    {
        Excel::import(new UmkmImport, $file);
    }

    /**
     * Import Pajak PBB Excel
     */
    public function importPajakPbb($file): void
    {
        Excel::import(new \App\Imports\PajakPbbImport, $file);
    }

    /**
     * Generate preview of penduduk data from Excel
     */
    public function previewPenduduk($file): array
    {
        set_time_limit(0);

        $import = new \App\Imports\PreviewPendudukImport($this);
        Excel::import($import, $file);

        return $import->getResult();
    }

    /**
     * Generate preview of Pajak PBB data from Excel
     */
    public function previewPajakPbb($file): array
    {
        set_time_limit(0);

        $import = new \App\Imports\PreviewPajakPbbImport();
        Excel::import($import, $file);

        return $import->getResult();
    }

    /**
     * Get invalid rows for Pajak PBB report generation
     */
    public function getPajakPbbInvalidRows($file): array
    {
        set_time_limit(0);

        $import = new \App\Imports\PreviewPajakPbbImport(true); // fullReport = true
        Excel::import($import, $file);

        return $import->invalidRows;
    }

    /**
     * Get invalid rows for report generation
     */
    public function getPendudukInvalidRows($file): array
    {
        set_time_limit(0);

        $import = new class($this) implements \Maatwebsite\Excel\Concerns\ToCollection, \Maatwebsite\Excel\Concerns\WithChunkReading {
            private $importService;
            public $invalidRows = [];
            public $seenNik = [];
            private $headers = [];
            private $headerIndexes = [];
            private $isHeaderParsed = false;
            private $rowCounter = 1;

            public function __construct($importService)
            {
                $this->importService = $importService;
            }

            public function collection(\Illuminate\Support\Collection $rows)
            {
                foreach ($rows as $row) {
                    $this->rowCounter++;

                    if (!$this->isHeaderParsed) {
                        $rawHeader = $row->toArray();
                        $this->headers = array_map(function ($h) {
                            $text = trim((string) $h);
                            $text = \Illuminate\Support\Str::lower($text);
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

                        $this->headerIndexes = [
                            'nik' => $findHeaderIndex($this->headers, ['nik', 'nomor induk kependudukan']),
                            'nama' => $findHeaderIndex($this->headers, ['nama', 'nama lengkap']),
                            'nkk' => $findHeaderIndex($this->headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']),
                            'alamat' => $findHeaderIndex($this->headers, ['alamat', 'domisili']),
                            'rt' => $findHeaderIndex($this->headers, ['rt']),
                            'rw' => $findHeaderIndex($this->headers, ['rw']),
                            'dusun' => $findHeaderIndex($this->headers, ['dusun', 'lingkungan']),
                        ];
                        $this->isHeaderParsed = true;
                        continue;
                    }

                    $nikIdx = $this->headerIndexes['nik'];
                    $namaIdx = $this->headerIndexes['nama'];
                    $nkkIdx = $this->headerIndexes['nkk'];
                    $alamatIdx = $this->headerIndexes['alamat'];
                    $rtIdx = $this->headerIndexes['rt'];
                    $rwIdx = $this->headerIndexes['rw'];
                    $dusunIdx = $this->headerIndexes['dusun'];

                    $nikRaw = isset($row[$nikIdx]) ? trim((string) $row[$nikIdx]) : '';
                    $nik = preg_replace('/\D+/', '', $nikRaw);
                    $nama = isset($row[$namaIdx]) ? trim((string) $row[$namaIdx]) : '';
                    $nkk = ($nkkIdx !== false && isset($row[$nkkIdx])) ? trim((string) $row[$nkkIdx]) : null;
                    $alamat = ($alamatIdx !== false && isset($row[$alamatIdx])) ? trim((string) $row[$alamatIdx]) : '';
                    $rtRaw = ($rtIdx !== false && isset($row[$rtIdx])) ? trim((string) $row[$rtIdx]) : '';
                    $rwRaw = ($rwIdx !== false && isset($row[$rwIdx])) ? trim((string) $row[$rwIdx]) : '';
                    $dusunRaw = ($dusunIdx !== false && isset($row[$dusunIdx])) ? trim((string) $row[$dusunIdx]) : '';

                    if ($nik === '' && $nama === '') continue;

                    $errors = [];
                    $warnings = [];
                    if ($nik === '') $errors['nik'][] = 'NIK wajib diisi';
                    if ($nama === '') $errors['nama'][] = 'Nama wajib diisi';
                    if ($nik !== '' && strlen($nik) !== 16) $warnings['nik'][] = 'NIK harus 16 karakter';
                    if ($nik !== '' && isset($this->seenNik[$nik])) $warnings['nik'][] = 'NIK duplikat di file';
                    if ($nkk !== null && $nkk !== '') {
                        $nkkClean = preg_replace('/\D+/', '', $nkk);
                        if (strlen($nkkClean) !== 16) $warnings['nkk'][] = 'No. KK harus 16 digit';
                    }

                    $wilayahRes = $this->importService->resolveWilayahForWebImport($rwRaw, $rtRaw, $dusunRaw);
                    if ($wilayahRes['status'] === 'conflict') {
                        $warnings['wilayah_info'] = "Wilayah Tidak Dikenal: RT '{$rtRaw}' / RW '{$rwRaw}'";
                    }

                    if ($nik !== '') {
                        $existingAny = \App\Models\Penduduk::withTrashed()->where('nik', $nik)->first();
                        if ($existingAny) {
                            $permanentMutasi = \App\Models\Mutasi::where('penduduk_id', $existingAny->id)
                                ->whereIn('jenis_mutasi', ['kematian', 'pindah_keluar'])
                                ->exists();
                            if ($permanentMutasi) {
                                $errors['nik'][] = "Terlarang: Penduduk ini sudah berstatus Meninggal/Pindah";
                            }
                        }
                    }

                    $fatalErrors = collect($errors)->flatten()->values()->all();

                    if (!empty($fatalErrors)) {
                        $this->invalidRows[] = [
                            'row' => $this->rowCounter,
                            'nik' => $nik,
                            'nama' => $nama,
                            'nkk' => $nkk,
                            'alamat' => $alamat,
                            'rt' => $rtRaw,
                            'rw' => $rwRaw,
                            'dusun' => $dusunRaw,
                            'errors_by_column' => $errors,
                            'errors' => $fatalErrors,
                        ];
                    }

                    if ($nik !== '') $this->seenNik[$nik] = true;
                }
            }

            public function chunkSize(): int
            {
                return 1000;
            }
        };

        \Maatwebsite\Excel\Facades\Excel::import($import, $file);
        return $import->invalidRows;
    }

    /**
     * Batch import penduduk from Excel
     */
    public function importPenduduk($file, string $originalFilename): array
    {
        $batchId    = 'webimp-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6));
        $sourceFile = $originalFilename;
        $now        = now()->toDateTimeString();

        $sheets = Excel::toArray([], $file);
        $rows   = $sheets[0] ?? [];

        if (count($rows) < 2) {
            throw new \InvalidArgumentException('File kosong.');
        }

        // Parse Headers
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
        $tempatLahirIdx = $findIdx($headers, ['tempat lahir']);
        $agamaIdx = $findIdx($headers, ['agama']);
        $statusPerkawinanIdx = $findIdx($headers, ['status perkawinan', 'status perkawinan']);
        $kedudukanKeluargaIdx = $findIdx($headers, ['kedudukan keluarga', 'shdk']);
        $pendidikanIdx = $findIdx($headers, ['pendidikan']);
        $pekerjaanIdx = $findIdx($headers, ['pekerjaan']);
        $dapatMembacaIdx = $findIdx($headers, ['dapat membaca', 'membaca huruf']);
        $namaAyahIdx = $findIdx($headers, ['nama ayah']);
        $namaIbuIdx = $findIdx($headers, ['nama ibu']);
        $golonganDarahIdx = $findIdx($headers, ['golongan darah', 'gol darah', 'goldar']);
        $warganegaraIdx = $findIdx($headers, ['warganegara', 'kewarganegaraan', 'wn']);
        $noAktaLahirIdx = $findIdx($headers, ['no akta lahir', 'akta lahir', 'nomor akta']);
        $statusPendidikanIdx = $findIdx($headers, ['status pendidikan', 'status sekolah']);
        $teleponIdx = $findIdx($headers, ['telepon', 'no telepon', 'wa', 'no wa', 'hp', 'no hp']);
        $cacatTypeIdx = $findIdx($headers, ['cacat', 'jenis cacat', 'disabilitas']);
        $sakitMenahunIdx = $findIdx($headers, ['sakit menahun', 'penyakit menahun']);
        $statusAsuransiIdx = $findIdx($headers, ['status asuransi', 'asuransi', 'bpjs']);
        $keteranganIdx = $findIdx($headers, ['keterangan', 'ket']);

        if ($nikIdx === false || $namaIdx === false) {
            throw new \InvalidArgumentException('Header wajib NIK dan Nama tidak ditemukan.');
        }

        // PRE-LOAD: Wilayah ke Memory
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

        // PRE-LOAD: Existing KKs
        $kkCache = KartuKeluarga::all()->keyBy('nkk');

        // PRE-LOAD: Existing Penduduk NIKs
        $existingNiks = Penduduk::withTrashed()->pluck('nik')->flip();

        // Accumulators
        $newKksBatch      = [];
        $pendudukRows     = [];
        $issuesBatch      = [];
        $affectedNkks     = [];
        $seenNikFile      = [];
        $summary          = ['imported' => 0, 'updated' => 0, 'issues' => 0];

        // Process Rows
        foreach (array_slice($rows, 1) as $i => $row) {
            $rowNumber = $i + 2;
            
            $assocRow = [];
            foreach ($headers as $idx => $h) {
                if (trim((string)$h) !== '') {
                    $assocRow[$h] = $row[$idx] ?? '';
                }
            }
            $nik  = preg_replace('/\D+/', '', trim((string)($row[$nikIdx]  ?? '')));
            $nama = trim((string)($row[$namaIdx] ?? ''));
            $nkk  = $nkkIdx !== false ? preg_replace('/\D+/', '', trim((string)($row[$nkkIdx] ?? ''))) : '';

            if (empty($nik) && empty($nama)) continue;

            $rwRaw   = $rwIdx    !== false ? (string)($row[$rwIdx]    ?? '') : '001';
            $rtRaw   = $rtIdx    !== false ? (string)($row[$rtIdx]    ?? '') : '001';
            $dusunRaw= $dusunIdx !== false ? (string)($row[$dusunIdx] ?? '') : '';
            $alamat  = $alamatIdx !== false ? (trim((string)($row[$alamatIdx] ?? '')) ?: 'Alamat tidak diketahui') : 'Alamat tidak diketahui';

            if (strlen($nik) !== 16) {
                $summary['issues']++;
                $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'invalid_nik', "NIK '{$nik}' tidak valid (" . strlen($nik) . " digit).", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now, $assocRow);
                continue;
            }

            if (!empty($nkk) && strlen($nkk) !== 16) {
                $summary['issues']++;
                $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'invalid_nkk', "No. KK '{$nkk}' tidak valid (" . strlen($nkk) . " digit).", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now, $assocRow);
                continue;
            }

            if (isset($seenNikFile[$nik])) {
                $summary['issues']++;
                $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'nik_conflict', "NIK '{$nik}' duplikat di dalam file.", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now, $assocRow);
                continue;
            }
            $seenNikFile[$nik] = true;

            // Resolve wilayah dari cache
            $rwKode  = $this->normalizeKodeWilayah($rwRaw);
            $rtKode  = $this->normalizeKodeWilayah($rtRaw);
            $cacheKey = "{$rwKode}:{$rtKode}";
            if (!isset($wilayahCache[$cacheKey])) {
                $summary['issues']++;
                $issuesBatch[] = $this->buildIssueRow($batchId, $sourceFile, 'wilayah_conflict', "Wilayah RT '{$rtRaw}' / RW '{$rwRaw}' belum terdaftar.", $rowNumber, $nik, $nama, $nkk, $rwRaw, $rtRaw, $dusunRaw, $now, $assocRow);
                continue;
            }
            $wilayah = $wilayahCache[$cacheKey];

            // Track KK baru
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
                '_nkk'              => $nkk, // temporary
                'nama'              => $nama,
                'jenis_kelamin'     => $this->mapJenisKelaminSimple((string)($jkIdx !== false ? ($row[$jkIdx] ?? '') : '')),
                'tempat_lahir'      => (string)($tempatLahirIdx !== false ? ($row[$tempatLahirIdx] ?? '') : ''),
                'tanggal_lahir'     => $this->parseDateSimple((string)($ttlIdx !== false ? ($row[$ttlIdx] ?? '') : '')),
                'agama'             => $this->mapAgama((string)($agamaIdx !== false ? ($row[$agamaIdx] ?? '') : '')),
                'status_perkawinan' => $this->mapStatusPerkawinan((string)($statusPerkawinanIdx !== false ? ($row[$statusPerkawinanIdx] ?? '') : '')),
                'kedudukan_keluarga'=> $this->mapKedudukanKeluarga((string)($kedudukanKeluargaIdx !== false ? ($row[$kedudukanKeluargaIdx] ?? '') : '')),
                'pendidikan'        => $this->mapPendidikan((string)($pendidikanIdx !== false ? ($row[$pendidikanIdx] ?? '') : '')),
                'pekerjaan'         => (string)($pekerjaanIdx !== false ? ($row[$pekerjaanIdx] ?? '') : ''),
                'dapat_membaca_huruf'=> (string)($dapatMembacaIdx !== false ? ($row[$dapatMembacaIdx] ?? '-') : '-'),
                'nama_ayah'         => (string)($namaAyahIdx !== false ? ($row[$namaAyahIdx] ?? '') : ''),
                'nama_ibu'          => (string)($namaIbuIdx !== false ? ($row[$namaIbuIdx] ?? '') : ''),
                'golongan_darah'    => (string)($golonganDarahIdx !== false ? ($row[$golonganDarahIdx] ?? '') : ''),
                'warganegara'       => (string)($warganegaraIdx !== false ? ($row[$warganegaraIdx] ?? 'WNI') : 'WNI'),
                'no_akta_lahir'     => (string)($noAktaLahirIdx !== false ? ($row[$noAktaLahirIdx] ?? '') : ''),
                'status_pendidikan' => (string)($statusPendidikanIdx !== false ? ($row[$statusPendidikanIdx] ?? '') : ''),
                'telepon'           => (string)($teleponIdx !== false ? ($row[$teleponIdx] ?? '') : ''),
                'cacat_type'        => (string)($cacatTypeIdx !== false ? ($row[$cacatTypeIdx] ?? '') : ''),
                'sakit_menahun'     => (string)($sakitMenahunIdx !== false ? ($row[$sakitMenahunIdx] ?? '') : ''),
                'status_asuransi'   => (string)($statusAsuransiIdx !== false ? ($row[$statusAsuransiIdx] ?? '') : ''),
                'keterangan'        => (string)($keteranganIdx !== false ? ($row[$keteranganIdx] ?? '') : ''),
                'deleted_at'        => null,
                'created_at'        => $now,
                'updated_at'        => $now,
            ];

            if ($isNew) $summary['imported']++;
            else $summary['updated']++;
        }

        // Batch Insert KK Baru
        DB::beginTransaction();
        try {
            foreach (array_chunk(array_values($newKksBatch), 200) as $chunk) {
                KartuKeluarga::withoutEvents(fn() => KartuKeluarga::insertOrIgnore($chunk));
            }

            // Reload KK cache
            $allNkks = array_keys($affectedNkks);
            $freshKkMap = KartuKeluarga::whereIn('nkk', $allNkks)->get()->keyBy('nkk');

            // Batch Upsert Penduduk
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
                        [
                            'nama', 'kartu_keluarga_id', 'jenis_kelamin', 'tanggal_lahir', 'deleted_at', 'updated_at',
                            'golongan_darah', 'warganegara', 'no_akta_lahir', 'status_pendidikan', 'telepon', 'cacat_type', 'sakit_menahun', 'status_asuransi', 'dapat_membaca_huruf', 'keterangan'
                        ]
                    )
                );
            }

            // Batch Insert Issues
            foreach (array_chunk($issuesBatch, 200) as $chunk) {
                ImportConflict::insert($chunk);
            }

            DB::commit();

            // Batch Recalculate — gunakan method batch recalculateMultiple
            $kkService = app(\App\Services\Kependudukan\KartuKeluargaService::class);
            $kkService->recalculateMultiple(array_unique($affectedKkIds));

            return $summary;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get path for downloadable template file
     */
    public function getTemplatePath(string $type): string
    {
        $templates = [
            'bantuan_sosial' => 'template_bantuan_sosial.xlsx',
            'umkm' => 'template_umkm.xlsx',
        ];

        if (!isset($templates[$type])) {
            throw new \Exception('Template tidak ditemukan!');
        }

        $templatePath = storage_path('app/templates/' . $templates[$type]);
        if (!file_exists($templatePath)) {
            throw new \Exception('File template tidak ditemukan!');
        }

        return $templatePath;
    }

    // ──────────────── Helper Methods ────────────────

    private function normalizeKodeWilayah($value): ?string
    {
        $clean = preg_replace('/\D+/', '', (string)$value);
        return $clean ? str_pad(substr($clean, 0, 3), 3, '0', STR_PAD_LEFT) : null;
    }

    public function resolveWilayahForWebImport(string $rwRaw, string $rtRaw, ?string $dusunRaw = null): array
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

    public function formatResolveResult($rw, $rt, $warning = null): array
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

    private function buildIssueRow(string $batchId, string $sourceFile, string $issueType, string $reason, int $rowNumber, string $nik, string $nama, string $nkk, string $rwRaw, string $rtRaw, string $dusunRaw, string $now, array $assocRow = []): array
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
            'payload_raw' => json_encode($assocRow),
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
    }

    private function mapAgama($value)
    {
        $value = strtolower(trim($value));
        $agamaMap = [
            'islam' => 'Islam', 'kristen' => 'Kristen', 'katolik' => 'Katolik',
            'hindu' => 'Hindu', 'buddha' => 'Buddha', 'khonghucu' => 'Khonghucu', 'konghucu' => 'Khonghucu',
        ];
        return $agamaMap[$value] ?? 'Islam';
    }

    private function mapStatusPerkawinan($value)
    {
        $value = strtolower(trim($value));
        $statusMap = [
            'belum kawin' => 'Belum Kawin', 'kawin' => 'Kawin',
            'cerai hidup' => 'Cerai Hidup', 'cerai mati' => 'Cerai Mati',
        ];
        return $statusMap[$value] ?? 'Belum Kawin';
    }

    private function mapKedudukanKeluarga($value)
    {
        $value = strtolower(trim($value));
        $kedudukanMap = [
            'kepala keluarga' => 'Kepala Keluarga', 'istri' => 'Istri', 'anak' => 'Anak',
            'menantu' => 'Menantu', 'cucu' => 'Cucu', 'orang tua' => 'Orang Tua',
            'mertua' => 'Mertua', 'famili lain' => 'Famili Lain', 'pembantu' => 'Pembantu', 'lainnya' => 'Lainnya',
        ];
        return $kedudukanMap[$value] ?? 'Anak';
    }

    private function mapPendidikan($value)
    {
        $value = strtolower(trim($value));
        $pendidikanMap = [
            'tidak/belum sekolah' => 'Tidak/Belum Sekolah', 'tidak tamat sd/sederajat' => 'Tidak Tamat SD/Sederajat',
            'tamat sd/sederajat' => 'Tamat SD/Sederajat', 'smp/sederajat' => 'SMP/Sederajat', 'sma/sederajat' => 'SMA/Sederajat',
            'diploma i/ii' => 'Diploma I/II', 'akademi/diploma iii/s.muda' => 'Akademi/Diploma III/S.Muda',
            'diploma iv/strata i' => 'Diploma IV/Strata I', 'strata ii' => 'Strata II', 'strata iii' => 'Strata III',
        ];
        return $pendidikanMap[$value] ?? 'Tidak/Belum Sekolah';
    }
}
