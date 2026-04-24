<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Dusun;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\WilayahImportConflict;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportDataFromExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:excel-data {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data penduduk dari file Excel yang sudah ada';

    protected array $importReport = [
        'rows_total' => 0,
        'rows_imported' => 0,
        'rows_skipped_conflict' => 0,
        'rows_skipped_invalid' => 0,
        'wilayah_auto_created' => 0,
        'wilayah_conflicts' => [],
    ];

    protected string $batchId;
    protected ?string $sourceFile = null;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Increase memory limit
        ini_set('memory_limit', '512M');

        $filePath = $this->argument('file') ?? 'DATA PENDUDUK DESA CIBATU LAMA.xlsx';
        $this->sourceFile = $filePath;
        $this->batchId = 'imp-' . now()->format('YmdHis') . '-' . Str::lower(Str::random(6));

        if (!file_exists($filePath)) {
            $this->error("File tidak ditemukan: {$filePath}");
            return 1;
        }

        $this->info("Memulai import data dari: {$filePath}");

        try {
            DB::beginTransaction();

            // Baca file Excel menggunakan PhpSpreadsheet dengan optimasi
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($filePath);

            $totalImported = 0;
            $totalSheets = 0;

            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $sheetName = $worksheet->getTitle();
                $this->info("Memproses sheet: {$sheetName}");
                $totalSheets++;

                $imported = $this->importWorksheetData($worksheet, $sheetName);
                $totalImported += $imported;

                $this->info("Sheet {$sheetName}: {$imported} data berhasil diimport");

                // Clear memory after each sheet
                $worksheet->disconnectCells();
                unset($worksheet);
                gc_collect_cycles();
            }

            DB::commit();

            $this->info("✅ Import selesai!");
            $this->info("🧾 Batch ID: {$this->batchId}");
            $this->info("📊 Total sheet diproses: {$totalSheets}");
            $this->info("👥 Total data diimport: {$totalImported}");
            $this->info("🧭 Wilayah auto-created: " . $this->importReport['wilayah_auto_created']);
            $this->warn("⚠️ Konflik wilayah (skipped): " . $this->importReport['rows_skipped_conflict']);
            $this->warn("⚠️ Row invalid/empty (skipped): " . $this->importReport['rows_skipped_invalid']);

        } catch (\Exception $e) {
            DB::rollBack();

            $this->error("❌ Error saat import: " . $e->getMessage());
            Log::error('Import Excel Error: ' . $e->getMessage());

            return 1;
        }

        return 0;
    }

    /**
     * Import data dari satu worksheet
     */
    private function importWorksheetData($worksheet, $sheetName)
    {
        $imported = 0;
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();

        // Get headers from first row
        $headers = [];
        for ($col = 'A'; $col <= $highestColumn; $col++) {
            $headers[$col] = $worksheet->getCell($col . '1')->getValue();
        }

        // Process data rows (skip header row)
        for ($row = 2; $row <= $highestRow; $row++) {
            try {
                $rowData = [];
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $rowData[$col] = $worksheet->getCell($col . $row)->getValue();
                }

                $this->importReport['rows_total']++;

                // Skip empty rows
                $nik = preg_replace('/\D+/', '', (string)$this->getCellValue($rowData, $headers, ['nik', 'NIK', 'C']));
                $nama = trim((string)$this->getCellValue($rowData, $headers, ['nama', 'Nama', 'NAMA', 'N A M A', 'D']));

                if (!$nik || !$nama) {
                    $this->importReport['rows_skipped_invalid']++;
                    $this->storeIssue(
                        issueType: 'required_field_missing',
                        sheetName: $sheetName,
                        row: $row,
                        nik: $nik,
                        nama: $nama,
                        nkk: '',
                        rwRaw: (string)($this->getCellValue($rowData, $headers, ['rw', 'RW', 'R']) ?? ''),
                        rtRaw: (string)($this->getCellValue($rowData, $headers, ['rt', 'RT', 'Q']) ?? ''),
                        dusunRaw: (string)($this->getCellValue($rowData, $headers, ['dusun', 'Dusun', 'DUSUN']) ?? ''),
                        reason: 'NIK atau nama kosong/tidak valid',
                        meta: ['headers' => $headers],
                        payloadRaw: $rowData
                    );
                    continue;
                }

                $nkk = preg_replace('/\D+/', '', (string)$this->getCellValue($rowData, $headers, ['nkk', 'NKK', 'no_kk', 'NO_KK', 'B']));
                if (!$nkk) {
                    $nkk = 'KK' . date('ymd') . str_pad((string)$row, 6, '0', STR_PAD_LEFT);
                }

                $rawRt = $this->getCellValue($rowData, $headers, ['rt', 'RT', 'Q']) ?? $this->extractRTFromSheet($sheetName);
                $rawRw = $this->getCellValue($rowData, $headers, ['rw', 'RW', 'R']) ?? '001';
                $rawDusun = $this->getCellValue($rowData, $headers, ['dusun', 'Dusun', 'DUSUN']) ?? null;

                $wilayah = $this->resolveWilayah((string)$rawRw, (string)$rawRt, $rawDusun);

                if (($wilayah['status'] ?? '') === 'conflict') {
                    $this->importReport['rows_skipped_conflict']++;
                    $conflict = [
                        'row' => $row,
                        'nik' => $nik,
                        'nama' => $nama,
                        'reason' => $wilayah['reason'] ?? 'conflict',
                        'rw' => $rawRw,
                        'rt' => $rawRt,
                    ];
                    $this->importReport['wilayah_conflicts'][] = $conflict;
                    $this->storeIssue(
                        issueType: 'wilayah_conflict',
                        sheetName: $sheetName,
                        row: $row,
                        nik: $nik,
                        nama: $nama,
                        nkk: $nkk,
                        rwRaw: (string)$rawRw,
                        rtRaw: (string)$rawRt,
                        dusunRaw: (string)($rawDusun ?? ''),
                        reason: (string)($wilayah['reason'] ?? 'conflict'),
                        meta: $conflict,
                        payloadRaw: $rowData
                    );
                    continue;
                }

                if (!empty($wilayah['auto_created'])) {
                    $this->importReport['wilayah_auto_created']++;
                }

                $existingKK = Penduduk::where('nkk', $nkk)->first();
                $existingNik = Penduduk::where('nik', $nik)->first();
                if ($existingNik && trim((string)$existingNik->nkk) !== trim((string)$nkk)) {
                    $this->importReport['rows_skipped_conflict']++;
                    $this->storeIssue(
                        issueType: 'nik_conflict',
                        sheetName: $sheetName,
                        row: $row,
                        nik: $nik,
                        nama: $nama,
                        nkk: $nkk,
                        rwRaw: (string)$rawRw,
                        rtRaw: (string)$rawRt,
                        dusunRaw: (string)($rawDusun ?? ''),
                        reason: 'NIK sudah ada dengan NKK berbeda',
                        meta: ['existing_nkk' => $existingNik->nkk, 'incoming_nkk' => $nkk],
                        payloadRaw: $rowData
                    );
                    continue;
                }

                // Create Penduduk
                Penduduk::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'nkk' => $nkk,
                        'nik' => $nik,
                        'nama' => $nama,
                        'jenis_kelamin' => $this->mapJenisKelamin($this->getCellValue($rowData, $headers, ['jenis_kelamin', 'Jenis Kelamin', 'JENIS KELAMIN', 'E']) ?? ''),
                        'tempat_lahir' => $this->getCellValue($rowData, $headers, ['tempat_lahir', 'Tempat Lahir', 'TEMPAT LAHIR', 'F']) ?? '',
                        'tanggal_lahir' => $this->parseExcelDate($this->getCellValue($rowData, $headers, ['tanggal_lahir', 'Tanggal Lahir', 'TGL LAHIR', 'G']) ?? ''),
                        'agama' => $this->mapAgama($this->getCellValue($rowData, $headers, ['agama', 'Agama', 'AGAMA', 'I']) ?? ''),
                        'status_perkawinan' => $this->mapStatusPerkawinan($this->getCellValue($rowData, $headers, ['status_perkawinan', 'Status Perkawinan', 'STATUS', 'J']) ?? ''),
                        'kedudukan_keluarga' => $this->mapKedudukanKeluarga($this->getCellValue($rowData, $headers, ['kedudukan_keluarga', 'Kedudukan Keluarga', 'KEDUDUKAN KK', 'KEDUDUKAN', 'K']) ?? ''),
                        'pendidikan' => $this->mapPendidikan($this->getCellValue($rowData, $headers, ['pendidikan', 'Pendidikan', 'PENDIDIKAN', 'L']) ?? ''),
                        'pekerjaan' => $this->getCellValue($rowData, $headers, ['pekerjaan', 'Pekerjaan', 'PEKERJAAN', 'M']) ?? '',
                        'nama_ayah' => $this->getCellValue($rowData, $headers, ['nama_ayah', 'Nama Ayah', 'NAMA AYAH', 'N']) ?? null,
                        'nama_ibu' => $this->getCellValue($rowData, $headers, ['nama_ibu', 'Nama Ibu', 'NAMA IBU', 'O']) ?? null,
                        'alamat' => $this->getCellValue($rowData, $headers, ['alamat', 'Alamat', 'ALAMAT', 'P']) ?? ($existingKK->alamat ?? 'Alamat tidak diketahui'),
                        'rt' => $wilayah['rt_kode'],
                        'rw' => $wilayah['rw_kode'],
                        'dusun' => $wilayah['dusun_nama'],
                        'keterangan' => $this->getCellValue($rowData, $headers, ['keterangan', 'Keterangan', 'KETERANGAN', 'S']) ?? null,
                    ]
                );

                $imported++;
                $this->importReport['rows_imported']++;

            } catch (\Exception $e) {
                $this->warn("Error importing row {$row}: " . $e->getMessage());
                Log::error('Error importing row: ' . $e->getMessage(), [
                    'row' => $row,
                    'data' => $rowData
                ]);
                continue;
            }
        }

        return $imported;
    }

    /**
     * Get cell value by searching for matching header
     */
    private function getCellValue($rowData, $headers, $possibleHeaders)
    {
        foreach ($possibleHeaders as $header) {
            foreach ($headers as $col => $headerValue) {
                if (strtolower(trim($headerValue)) === strtolower(trim($header))) {
                    return $rowData[$col] ?? null;
                }
            }
        }
        return null;
    }

    /**
     * Normalize kode wilayah to 3 digit numeric.
     */
    private function normalizeKodeWilayah(?string $value, string $default = '001'): string
    {
        $clean = preg_replace('/\D+/', '', (string)$value);
        if ($clean === null || $clean === '') {
            return $default;
        }

        return str_pad(substr($clean, 0, 3), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve wilayah with smart rules: valid / unknown(auto-create) / conflict(hold).
     */
    private function resolveWilayah(string $rwRaw, string $rtRaw, ?string $dusunRaw = null): array
    {
        $rwKode = $this->normalizeKodeWilayah($rwRaw, '001');
        $rtKode = $this->normalizeKodeWilayah($rtRaw, '001');

        $rw = Rw::where('kode', $rwKode)->first();
        $rtInAnyRw = Rt::where('kode', $rtKode)->first();

        // conflict: RT code exists in different RW than requested.
        if ($rw && $rtInAnyRw && (int)$rtInAnyRw->rw_id !== (int)$rw->id) {
            return [
                'status' => 'conflict',
                'reason' => "RT {$rtKode} sudah terdaftar di RW lain",
            ];
        }

        $autoCreated = false;

        if (!$rw) {
            $rw = Rw::create([
                'kode' => $rwKode,
                'nama' => "RW {$rwKode}",
                'is_active' => true,
                'is_auto_generated' => true,
                'needs_review' => true,
            ]);
            $autoCreated = true;
        }

        $rt = Rt::where('kode', $rtKode)->where('rw_id', $rw->id)->first();

        if (!$rt) {
            $dusunNama = trim((string)$dusunRaw);
            $dusun = null;

            if ($dusunNama !== '') {
                $dusun = Dusun::firstOrCreate(
                    ['nama' => $dusunNama],
                    [
                        'kode' => strtoupper(substr(preg_replace('/\s+/', '', $dusunNama), 0, 12)),
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
            $autoCreated = true;
        }

        return [
            'status' => $autoCreated ? 'unknown' : 'valid',
            'auto_created' => $autoCreated,
            'rw_kode' => $rw->kode,
            'rt_kode' => $rt->kode,
            'dusun_nama' => optional($rt->dusun)->nama,
        ];
    }

    private function storeIssue(string $issueType, string $sheetName, int $row, string $nik, string $nama, string $nkk, string $rwRaw, string $rtRaw, string $dusunRaw, string $reason, array $meta = [], array $payloadRaw = []): void
    {
        WilayahImportConflict::create([
            'batch_id' => $this->batchId,
            'source_file' => $this->sourceFile,
            'sheet_name' => $sheetName,
            'row_number' => $row,
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

    /**
     * Extract RT number from sheet name
     */
    private function extractRTFromSheet($sheetName)
    {
        preg_match('/RT[_\s]*(\d+)/i', $sheetName, $matches);
        return isset($matches[1]) ? str_pad($matches[1], 2, '0', STR_PAD_LEFT) : '01';
    }

    /**
     * Map jenis kelamin
     */
    private function mapJenisKelamin($value)
    {
        $value = strtolower(trim($value));

        if (in_array($value, ['l', 'laki-laki', 'male', 'pria', 'lk'])) {
            return 'L';
        } elseif (in_array($value, ['p', 'perempuan', 'female', 'wanita', 'pr'])) {
            return 'P';
        }

        return 'L'; // Default
    }

    /**
     * Map agama
     */
    private function mapAgama($value)
    {
        $value = strtolower(trim($value));

        $agamaMap = [
            'islam' => 'Islam',
            'kristen' => 'Kristen',
            'katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'khonghucu' => 'Khonghucu',
            'konghucu' => 'Khonghucu',
        ];

        return $agamaMap[$value] ?? 'Islam';
    }

    /**
     * Map status perkawinan
     */
    private function mapStatusPerkawinan($value)
    {
        $value = trim($value);
        $valueLower = strtolower($value);

        // Mapping yang mempertahankan variasi asli dari Excel
        $statusMap = [
            'belum kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'kawin tercatat' => 'Kawin Tercatat',
            'cerai hidup' => 'Cerai Hidup',
            'cerai mati' => 'Cerai Mati',
        ];

        // Cek exact match dulu untuk mempertahankan variasi
        if (isset($statusMap[$valueLower])) {
            return $statusMap[$valueLower];
        }

        // Cek case-insensitive match
        foreach ($statusMap as $key => $mapped) {
            if (strtolower($key) === $valueLower) {
                return $mapped;
            }
        }

        // Jika tidak ada match, return asli atau default
        return $value ?: 'Belum Kawin';
    }

    /**
     * Map kedudukan keluarga
     */
    private function mapKedudukanKeluarga($value)
    {
        $value = strtolower(trim($value));

        $kedudukanMap = [
            'kepala keluarga' => 'Kepala Keluarga',
            'istri' => 'Istri',
            'anak' => 'Anak',
            'menantu' => 'Menantu',
            'cucu' => 'Cucu',
            'orang tua' => 'Orang Tua',
            'mertua' => 'Mertua',
            'famili lain' => 'Famili Lain',
            'pembantu' => 'Pembantu',
            'lainnya' => 'Lainnya',
        ];

        return $kedudukanMap[$value] ?? 'Anak';
    }

    /**
     * Map pendidikan
     */
    private function mapPendidikan($value)
    {
        $value = strtolower(trim($value));

        $pendidikanMap = [
            'tidak/belum sekolah' => 'Tidak/Belum Sekolah',
            'tidak tamat sd/sederajat' => 'Tidak Tamat SD/Sederajat',
            'tamat sd/sederajat' => 'Tamat SD/Sederajat',
            'smp/sederajat' => 'SMP/Sederajat',
            'sma/sederajat' => 'SMA/Sederajat',
            'diploma i/ii' => 'Diploma I/II',
            'akademi/diploma iii/s.muda' => 'Akademi/Diploma III/S.Muda',
            'diploma iv/strata i' => 'Diploma IV/Strata I',
            'strata ii' => 'Strata II',
            'strata iii' => 'Strata III',
        ];

        return $pendidikanMap[$value] ?? 'Tidak/Belum Sekolah';
    }

    /**
     * Parse Excel date (serial number)
     */
    private function parseExcelDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            // If it's a number (Excel serial date)
            if (is_numeric($dateValue)) {
                $excelEpoch = new \DateTime('1900-01-01');
                $excelEpoch->add(new \DateInterval('P' . (intval($dateValue) - 2) . 'D'));
                return $excelEpoch->format('Y-m-d');
            }

            // Try different date formats
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'd-m-Y',
                'Y/m/d',
                'd-m-y',
                'd/m/y',
                'Y-m-d H:i:s',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateValue);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Try Carbon parsing
            return \Carbon\Carbon::parse($dateValue)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date: ' . $dateValue);
            return null;
        }
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        return $this->parseExcelDate($dateString);
    }
}
