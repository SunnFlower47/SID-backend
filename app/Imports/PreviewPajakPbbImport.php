<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Models\PajakPbbObjek;

class PreviewPajakPbbImport implements ToCollection, WithChunkReading
{
    public $validRows = [];
    public $invalidRows = [];
    public $columnErrorCounts = [
        'nop' => 0,
        'nama' => 0,
    ];
    public $seenNop = [];

    public $totalDataRows = 0;
    public $validCount = 0;
    public $invalidCount = 0;

    private $headers = [];
    private $headerIndexes = [];
    private $isHeaderParsed = false;
    private $rowCounter = 1;
    
    private $fullReport;

    public function __construct(bool $fullReport = false)
    {
        $this->fullReport = $fullReport;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->rowCounter++;

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
                            if ($h === $candidate || str_contains($h, $candidate)) return $idx;
                        }
                    }
                    return false;
                };

                $this->headerIndexes = [
                    'nop' => $findHeaderIndex($this->headers, ['nop', 'nomor objek pajak']),
                    'nama' => $findHeaderIndex($this->headers, ['nama', 'wajib pajak']),
                ];

                if ($this->headerIndexes['nop'] === false) {
                    throw new \InvalidArgumentException('Header wajib NOP tidak ditemukan.');
                }

                $this->isHeaderParsed = true;
                continue;
            }

            $nopIdx = $this->headerIndexes['nop'];
            $namaIdx = $this->headerIndexes['nama'];

            $nopRaw = isset($row[$nopIdx]) ? trim((string) $row[$nopIdx]) : '';
            $nop = preg_replace('/\D+/', '', $nopRaw);
            $nama = ($namaIdx !== false && isset($row[$namaIdx])) ? trim((string) $row[$namaIdx]) : '';

            // Skip dummy instruction row
            if (str_contains(strtolower($nopRaw), 'hapus')) continue;
            if ($nop === '' && $nama === '') continue;

            $this->totalDataRows++;
            $errors = [];

            if ($nop === '') {
                $errors['nop'][] = 'NOP wajib diisi';
                $this->columnErrorCounts['nop']++;
            } elseif (strlen($nop) !== 18) {
                $errors['nop'][] = 'NOP harus 18 digit (terdeteksi ' . strlen($nop) . ' digit)';
                $this->columnErrorCounts['nop']++;
            } elseif (isset($this->seenNop[$nop])) {
                $errors['nop'][] = 'NOP duplikat di file Excel ini';
                $this->columnErrorCounts['nop']++;
            }

            if ($nama === '') {
                $errors['nama_info'] = 'Nama kosong, akan diisi "Anonim"';
            }

            if ($nop !== '' && strlen($nop) === 18 && !isset($this->seenNop[$nop])) {
                $existing = PajakPbbObjek::where('nop', $nop)->first();
                if ($existing) {
                    $errors['nop_info'] = "Update (Nama Lama: {$existing->nama_wp})";
                } else {
                    $errors['nop_info'] = "NOP Baru (Insert)";
                }
            }

            $preview = [
                'row' => $this->rowCounter,
                'nop' => $nop,
                'nama' => $nama,
            ];

            $fatalErrors = collect($errors)
                ->except(['nop_info', 'nama_info'])
                ->flatten()
                ->values()
                ->all();

            if (empty($fatalErrors)) {
                $this->validCount++;
                if (count($this->validRows) < 50) {
                    if (!empty($errors['nop_info'])) $preview['info'] = $errors['nop_info'];
                    if (!empty($errors['nama_info'])) $preview['info'] = ($preview['info'] ?? '') . ' | ' . $errors['nama_info'];
                    $this->validRows[] = $preview;
                }
            } else {
                $this->invalidCount++;
                if ($this->fullReport || count($this->invalidRows) < 200) {
                    $preview['errors_by_column'] = $errors;
                    $preview['errors'] = $fatalErrors;
                    $this->invalidRows[] = $preview;
                }
            }

            if ($nop !== '') $this->seenNop[$nop] = true;
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getResult(): array
    {
        return [
            'summary' => [
                'total_data_rows' => $this->totalDataRows,
                'valid_rows' => $this->validCount,
                'invalid_rows' => $this->invalidCount,
                'column_error_counts' => $this->columnErrorCounts,
            ],
            'preview' => [
                'valid' => $this->validRows,
                'invalid' => $this->invalidRows,
                'valid_shown' => count($this->validRows),
                'invalid_shown' => count($this->invalidRows),
                'valid_total' => $this->validCount,
                'invalid_total' => $this->invalidCount,
            ]
        ];
    }
}
