<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use App\Services\Kependudukan\ImportService;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\Penduduk;
use App\Models\Mutasi;

class PreviewPendudukImport implements ToCollection, WithChunkReading
{
    private $importService;

    public $validRows = [];
    public $conflictRows = [];
    public $invalidRows = [];
    public $columnErrorCounts = [
        'nik' => 0,
        'nama' => 0,
        'nkk' => 0,
        'wilayah' => 0,
    ];
    public $seenNik = [];

    public $totalDataRows = 0;
    public $validCount = 0;
    public $conflictCount = 0;
    public $invalidCount = 0;

    private $headers = [];
    private $headerIndexes = [];
    private $isHeaderParsed = false;
    private $rowCounter = 1;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

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
                    'nik' => $findHeaderIndex($this->headers, ['nik', 'nomor induk kependudukan']),
                    'nama' => $findHeaderIndex($this->headers, ['nama', 'nama lengkap']),
                    'nkk' => $findHeaderIndex($this->headers, ['nkk', 'no kk', 'nomor kk', 'kartu keluarga']),
                    'alamat' => $findHeaderIndex($this->headers, ['alamat', 'domisili']),
                    'rt' => $findHeaderIndex($this->headers, ['rt']),
                    'rw' => $findHeaderIndex($this->headers, ['rw']),
                    'dusun' => $findHeaderIndex($this->headers, ['dusun', 'lingkungan']),
                ];

                if ($this->headerIndexes['nik'] === false || $this->headerIndexes['nama'] === false) {
                    throw new \InvalidArgumentException('Header wajib tidak ditemukan. Gunakan kolom yang mengandung NIK dan Nama (contoh: NIK, Nama, No. KK, dst).');
                }

                $this->isHeaderParsed = true;
                continue; // Skip header row
            }

            // Process Data Row
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

            if ($nik === '' && $nama === '') {
                continue;
            }

            $this->totalDataRows++;

            $errors = [];
            $warnings = [];
            if ($nik === '') {
                $errors['nik'][] = 'NIK wajib diisi';
                $this->columnErrorCounts['nik']++;
            }
            if ($nama === '') {
                $errors['nama'][] = 'Nama wajib diisi';
                $this->columnErrorCounts['nama']++;
            }
            if ($nik !== '' && strlen($nik) !== 16) {
                $warnings['nik'][] = 'NIK tidak 16 digit (Akan Masuk Konflik)';
            }
            if ($nik !== '' && isset($this->seenNik[$nik])) {
                $warnings['nik'][] = 'NIK duplikat di file (Akan Masuk Konflik)';
            }

            if ($nkk !== null && $nkk !== '') {
                $nkkClean = preg_replace('/\D+/', '', $nkk);
                if (strlen($nkkClean) !== 16) {
                    $warnings['nkk'][] = 'No. KK tidak 16 digit (Akan Masuk Konflik)';
                }
            }

            $wilayahRes = $this->importService->resolveWilayahForWebImport($rwRaw, $rtRaw, $dusunRaw);
            if ($wilayahRes['status'] === 'conflict') {
                $warnings['wilayah_info'] = "Wilayah Tidak Dikenal: RT '{$rtRaw}' / RW '{$rwRaw}' (Akan Masuk Konflik)";
                $this->columnErrorCounts['wilayah']++;
            } else {
                $rwObj = Rw::find($wilayahRes['rw_id']);
                $rtObj = Rt::find($wilayahRes['rt_id']);
                if (($rwObj && $rwObj->needs_review) || ($rtObj && $rtObj->needs_review)) {
                    $warnings['wilayah_info'] = 'Peringatan: Wilayah ini belum diverifikasi di Master';
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
                        $this->columnErrorCounts['nik']++;
                    } else {
                        $warnings['nik_info'] = "Review: NIK sudah ada di sistem (Perlu keputusan)";
                    }
                }
            }

            $preview = [
                'row' => $this->rowCounter,
                'nik' => $nik,
                'nama' => $nama,
                'nkk' => $nkk,
                'alamat' => $alamat,
                'rt' => $rtRaw,
                'rw' => $rwRaw,
                'dusun' => $dusunRaw,
            ];

            $fatalErrors = collect($errors)->flatten()->values()->all();

            if (empty($fatalErrors)) {
                if (empty($warnings)) {
                    $this->validCount++;
                    if (count($this->validRows) < 50) {
                        $this->validRows[] = $preview;
                    }
                } else {
                    $this->conflictCount++;
                    if (count($this->conflictRows) < 150) {
                        $infoParts = [];
                        if (!empty($warnings['nik_info'])) $infoParts[] = $warnings['nik_info'];
                        if (!empty($warnings['wilayah_info'])) $infoParts[] = $warnings['wilayah_info'];
                        if (!empty($warnings['nik'])) $infoParts[] = implode(', ', $warnings['nik']);
                        if (!empty($warnings['nkk'])) $infoParts[] = implode(', ', $warnings['nkk']);
                        
                        if (!empty($infoParts)) {
                            $preview['info'] = implode(' | ', $infoParts);
                        }
                        $this->conflictRows[] = $preview;
                    }
                }
            } else {
                $this->invalidCount++;
                if (count($this->invalidRows) < 200) {
                    $preview['errors_by_column'] = $errors;
                    $preview['errors'] = $fatalErrors;
                    $this->invalidRows[] = $preview;
                }
            }

            if ($nik !== '') {
                $this->seenNik[$nik] = true;
            }
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
                'conflict_rows' => $this->conflictCount,
                'invalid_rows' => $this->invalidCount,
                'column_error_counts' => $this->columnErrorCounts,
            ],
            'preview' => [
                'valid' => $this->validRows,
                'conflict' => $this->conflictRows,
                'invalid' => $this->invalidRows,
                'valid_shown' => count($this->validRows),
                'conflict_shown' => count($this->conflictRows),
                'invalid_shown' => count($this->invalidRows),
                'valid_total' => $this->validCount,
                'conflict_total' => $this->conflictCount,
                'invalid_total' => $this->invalidCount,
            ]
        ];
    }
}
