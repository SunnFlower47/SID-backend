<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\Umkm;
use App\Models\Penduduk;
use Carbon\Carbon;

class PreviewUmkmImport implements ToCollection, WithChunkReading
{
    public $validRows = [];
    public $conflictRows = [];
    public $invalidRows = [];
    public $columnErrorCounts = [
        'nama_usaha' => 0,
        'nik_pemilik' => 0,
        'wilayah' => 0,
    ];
    public $seenNikOrNama = []; // To check duplicates within the file

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
                    'nama_usaha' => $findHeaderIndex($this->headers, ['nama usaha']),
                    'nama_pemilik' => $findHeaderIndex($this->headers, ['nama pemilik']),
                    'nik_pemilik' => $findHeaderIndex($this->headers, ['nik pemilik', 'nik']),
                    'rt' => $findHeaderIndex($this->headers, ['rt']),
                    'rw' => $findHeaderIndex($this->headers, ['rw']),
                ];

                if ($this->headerIndexes['nama_usaha'] === false || $this->headerIndexes['nik_pemilik'] === false) {
                    throw new \InvalidArgumentException('Header wajib tidak ditemukan. Pastikan ada kolom Nama Usaha dan NIK Pemilik.');
                }

                $this->isHeaderParsed = true;
                continue; // Skip header row
            }

            // Process Data Row
            $namaUsahaIdx = $this->headerIndexes['nama_usaha'];
            $nikPemilikIdx = $this->headerIndexes['nik_pemilik'];
            $namaPemilikIdx = $this->headerIndexes['nama_pemilik'];
            $rtIdx = $this->headerIndexes['rt'];
            $rwIdx = $this->headerIndexes['rw'];

            $namaUsaha = isset($row[$namaUsahaIdx]) ? trim((string) $row[$namaUsahaIdx]) : '';
            $nikPemilikRaw = isset($row[$nikPemilikIdx]) ? trim((string) $row[$nikPemilikIdx]) : '';
            $nikPemilik = preg_replace('/\D+/', '', $nikPemilikRaw);
            $namaPemilik = $namaPemilikIdx !== false && isset($row[$namaPemilikIdx]) ? trim((string) $row[$namaPemilikIdx]) : '';
            $rt = $rtIdx !== false && isset($row[$rtIdx]) ? trim((string) $row[$rtIdx]) : '';
            $rw = $rwIdx !== false && isset($row[$rwIdx]) ? trim((string) $row[$rwIdx]) : '';

            // Skip empty rows
            if (empty($namaUsaha) && empty($nikPemilik)) {
                continue;
            }

            $this->totalDataRows++;
            $errors = [];
            $errorsByColumn = [];

            // Validations
            if (empty($namaUsaha)) {
                $errors[] = 'Nama Usaha kosong';
                $errorsByColumn['nama_usaha'] = 'Nama Usaha kosong';
                $this->columnErrorCounts['nama_usaha']++;
            }

            if (empty($nikPemilik) || strlen($nikPemilik) !== 16) {
                $errors[] = 'NIK tidak 16 digit';
                $errorsByColumn['nik_pemilik'] = 'NIK tidak 16 digit';
                $this->columnErrorCounts['nik_pemilik']++;
            } else {
                // Check if Penduduk exists
                $pendudukExists = Penduduk::where('nik', $nikPemilik)->exists();
                if (!$pendudukExists) {
                    $errors[] = "NIK $nikPemilik tidak ditemukan di database Penduduk";
                    $errorsByColumn['nik_pemilik'] = 'NIK tidak terdaftar';
                    $this->columnErrorCounts['nik_pemilik']++;
                }
            }

            if (empty($rt) || empty($rw)) {
                $errors[] = 'RT/RW kosong';
                $errorsByColumn['wilayah'] = 'RT/RW kosong';
                $this->columnErrorCounts['wilayah']++;
            }

            // Check for conflict
            $isConflict = false;
            $conflictReason = '';

            $uniqueKeyNik = 'nik_' . $nikPemilik;
            $uniqueKeyNama = 'nama_' . strtolower($namaUsaha);

            if (empty($errors)) {
                if (in_array($uniqueKeyNik, $this->seenNikOrNama) || in_array($uniqueKeyNama, $this->seenNikOrNama)) {
                    $isConflict = true;
                    $conflictReason = 'Duplikat NIK atau Nama Usaha dalam file Excel';
                } else {
                    $exists = Umkm::where('nik_pemilik', $nikPemilik)
                        ->orWhere('nama_usaha', $namaUsaha)
                        ->exists();

                    if ($exists) {
                        $isConflict = true;
                        $conflictReason = 'UMKM dengan NIK atau Nama Usaha tersebut sudah ada (akan dilewati/error)';
                    }
                }
                $this->seenNikOrNama[] = $uniqueKeyNik;
                $this->seenNikOrNama[] = $uniqueKeyNama;
            }

            $rowData = [
                'row' => $this->rowCounter,
                'nama' => $namaUsaha,
                'nik' => $nikPemilik,
                'pemilik' => $namaPemilik,
                'rt' => $rt,
                'rw' => $rw,
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
