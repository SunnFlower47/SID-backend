<?php

namespace App\Imports;

use App\Models\Penduduk;
use App\Traits\WilayahResolver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PendudukImport implements ToCollection, WithHeadingRow, WithValidation, WithChunkReading
{
    use WilayahResolver;

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // 1. Basic Cleaning
                $nik = trim($row['nik'] ?? '');
                $nama = trim($row['nama'] ?? '');

                if (empty($nik) || empty($nama)) {
                    continue;
                }

                // 2. Resolve Wilayah (Using Standardized Regex & Sanity Check from Trait)
                $wilayah = $this->resolveWilayah(
                    $row['rt'] ?? '001',
                    $row['rw'] ?? '001',
                    $row['dusun'] ?? null
                );

                // Generate NKK if not provided
                $nkk = $row['nkk'] ?? $row['no_kk'] ?? $this->generateNKK();

                // Create Penduduk
                Penduduk::updateOrCreate(
                    ['nik' => $row['nik']],
                    [
                        'nkk' => $nkk,
                        'nik' => $row['nik'],
                        'nama' => $row['nama'],
                        'jenis_kelamin' => $this->mapJenisKelamin($row['jenis_kelamin'] ?? ''),
                        'tempat_lahir' => $row['tempat_lahir'] ?? '',
                        'tanggal_lahir' => $this->parseDate($row['tanggal_lahir'] ?? ''),
                        'agama' => $this->mapAgama($row['agama'] ?? ''),
                        'status_perkawinan' => $this->mapStatusPerkawinan($row['status_perkawinan'] ?? ''),
                        'kedudukan_keluarga' => $this->mapKedudukanKeluarga($row['kedudukan_keluarga'] ?? ''),
                        'pendidikan' => $this->mapPendidikan($row['pendidikan'] ?? ''),
                        'pekerjaan' => $row['pekerjaan'] ?? '',
                        'nama_ayah' => $row['nama_ayah'] ?? null,
                        'nama_ibu' => $row['nama_ibu'] ?? null,
                        'alamat' => $row['alamat'] ?? 'Alamat tidak diketahui',
                        'rt_id' => $wilayah['rt_id'],
                        'rw_id' => $wilayah['rw_id'],
                        'dusun_id' => $wilayah['dusun_id'],
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Error importing row: ' . $e->getMessage(), [
                    'row' => is_array($row) ? $row : (method_exists($row, 'toArray') ? $row->toArray() : $row)
                ]);
                continue;
            }
        }
    }


    /**
     * Generate NKK if not provided
     */
    private function generateNKK()
    {
        return date('Ymd') . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Map jenis kelamin
     */
    private function mapJenisKelamin($value)
    {
        $value = strtoupper(trim($value));
        
        if (in_array($value, ['L', 'LAKI-LAKI', 'LAKI LAKI', 'PRIA', 'MALE'])) {
            return 'LAKI-LAKI';
        } elseif (in_array($value, ['P', 'PEREMPUAN', 'WANITA', 'FEMALE'])) {
            return 'PEREMPUAN';
        }
        
        return 'LAKI-LAKI'; // Default
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
        $value = strtolower(trim($value));
        
        $statusMap = [
            'belum kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'cerai hidup' => 'Cerai Hidup',
            'cerai mati' => 'Cerai Mati',
        ];
        
        return $statusMap[$value] ?? 'Belum Kawin';
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
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = [
                'Y-m-d',
                'd/m/Y',
                'd-m-Y',
                'Y/m/d',
                'd-m-y',
                'd/m/y',
            ];

            foreach ($formats as $format) {
                $date = \DateTime::createFromFormat($format, $dateString);
                if ($date !== false) {
                    return $date->format('Y-m-d');
                }
            }

            // Try Carbon parsing
            return \Carbon\Carbon::parse($dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Could not parse date: ' . $dateString);
            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            '*.nik' => 'required|string|size:16|distinct',
            '*.nama' => 'required|string|max:255',
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
