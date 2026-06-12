<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\BantuanSosial;
use Carbon\Carbon;

class PreviewBantuanSosialImport implements ToCollection, WithChunkReading
{
    public $validRows = [];
    public $conflictRows = [];
    public $invalidRows = [];
    public $columnErrorCounts = [
        'program' => 0,
        'periode' => 0,
        'tanggal' => 0,
    ];
    public $seenProgramPeriode = []; // To check duplicates within the file

    public $totalDataRows = 0;
    public $validCount = 0;
    public $conflictCount = 0;
    public $invalidCount = 0;

    private $headers = [];
    private $headerIndexes = [];
    private $isHeaderParsed = false;
    private $rowCounter = 1;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rowCounter++;

            // Handle Header Row
            if (!$this->isHeaderParsed) {
                $rawHeader = $row->toArray();
                $this->headers = array_map(function ($h) {
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

                $this->headerIndexes = [
                    'program' => $findHeaderIndex($this->headers, ['program', 'nama program']),
                    'jenis_bantuan' => $findHeaderIndex($this->headers, ['jenis bantuan']),
                    'periode' => $findHeaderIndex($this->headers, ['periode']),
                    'tanggal_mulai' => $findHeaderIndex($this->headers, ['tanggal mulai']),
                    'tanggal_selesai' => $findHeaderIndex($this->headers, ['tanggal selesai']),
                ];

                if ($this->headerIndexes['program'] === false || $this->headerIndexes['periode'] === false) {
                    throw new \InvalidArgumentException('Header wajib tidak ditemukan. Pastikan ada kolom Program dan Periode.');
                }

                $this->isHeaderParsed = true;
                continue; // Skip header row
            }

            // Process Data Row
            $programIdx = $this->headerIndexes['program'];
            $jenisIdx = $this->headerIndexes['jenis_bantuan'];
            $periodeIdx = $this->headerIndexes['periode'];
            $tanggalMulaiIdx = $this->headerIndexes['tanggal_mulai'];
            $tanggalSelesaiIdx = $this->headerIndexes['tanggal_selesai'];

            $program = isset($row[$programIdx]) ? trim((string) $row[$programIdx]) : '';
            $jenis = $jenisIdx !== false && isset($row[$jenisIdx]) ? trim((string) $row[$jenisIdx]) : '';
            $periode = isset($row[$periodeIdx]) ? trim((string) $row[$periodeIdx]) : '';
            $tanggalMulaiRaw = $tanggalMulaiIdx !== false && isset($row[$tanggalMulaiIdx]) ? trim((string) $row[$tanggalMulaiIdx]) : '';
            $tanggalSelesaiRaw = $tanggalSelesaiIdx !== false && isset($row[$tanggalSelesaiIdx]) ? trim((string) $row[$tanggalSelesaiIdx]) : '';

            // Skip empty rows
            if (empty($program) && empty($periode)) {
                continue;
            }

            $this->totalDataRows++;
            $errors = [];
            $errorsByColumn = [];

            // Validations
            if (empty($program)) {
                $errors[] = 'Program kosong';
                $errorsByColumn['program'] = 'Program kosong';
                $this->columnErrorCounts['program']++;
            }

            if (empty($periode)) {
                $errors[] = 'Periode kosong';
                $errorsByColumn['periode'] = 'Periode kosong';
                $this->columnErrorCounts['periode']++;
            }

            // Validate Dates
            $tanggalMulai = $this->parseDate($tanggalMulaiRaw);
            $tanggalSelesai = $this->parseDate($tanggalSelesaiRaw);

            if (!empty($tanggalMulaiRaw) && !$tanggalMulai) {
                $errors[] = 'Format tanggal mulai salah';
                $errorsByColumn['tanggal'] = 'Format salah';
                $this->columnErrorCounts['tanggal']++;
            }

            if (!empty($tanggalSelesaiRaw) && !$tanggalSelesai) {
                $errors[] = 'Format tanggal selesai salah';
                $errorsByColumn['tanggal'] = 'Format salah';
                $this->columnErrorCounts['tanggal']++;
            }

            // Check for conflict (already exists in DB or within file)
            $isConflict = false;
            $conflictReason = '';

            $uniqueKey = strtolower($program . '|' . $periode);

            if (empty($errors)) {
                if (in_array($uniqueKey, $this->seenProgramPeriode)) {
                    $isConflict = true;
                    $conflictReason = 'Duplikat program & periode dalam file Excel';
                } else {
                    $exists = BantuanSosial::where('nama_program', $program)
                        ->where('periode', $periode)
                        ->exists();

                    if ($exists) {
                        $isConflict = true;
                        $conflictReason = 'Program & periode sudah ada di database (akan dilewati)';
                    }
                }
                $this->seenProgramPeriode[] = $uniqueKey;
            }

            $rowData = [
                'row' => $this->rowCounter,
                'program' => $program,
                'periode' => $periode,
                'jenis' => $jenis,
            ];

            if (!empty($errors)) {
                $rowData['errors'] = $errors;
                $rowData['errors_by_column'] = $errorsByColumn;
                $this->invalidRows[] = $rowData;
                $this->invalidCount++;
            } elseif ($isConflict) {
                $rowData['info'] = $conflictReason;
                $this->conflictRows[] = $rowData;
                $this->conflictCount++;
            } else {
                $this->validRows[] = $rowData;
                $this->validCount++;
            }
        }
    }

    private function parseDate(string $value): ?string
    {
        $value = trim($value);
        if ($value === '' || $value === '-') return null;
        try {
            if (is_numeric($value)) {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                return $excelDate->format('Y-m-d');
            }
            if (strpos($value, '/') !== false) return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) { return null; }
    }

    public function getResult()
    {
        return [
            'summary' => [
                'total_rows' => $this->totalDataRows,
                'valid_rows' => $this->validCount,
                'conflict_rows' => $this->conflictCount,
                'invalid_rows' => $this->invalidCount,
                'column_error_counts' => $this->columnErrorCounts,
            ],
            'preview' => [
                'valid' => array_slice($this->validRows, 0, 20),
                'conflict' => array_slice($this->conflictRows, 0, 20),
                'invalid' => array_slice($this->invalidRows, 0, 20),
                'valid_shown' => min($this->validCount, 20),
                'valid_total' => $this->validCount,
                'conflict_shown' => min($this->conflictCount, 20),
                'conflict_total' => $this->conflictCount,
                'invalid_shown' => min($this->invalidCount, 20),
                'invalid_total' => $this->invalidCount,
            ]
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
