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
        $affectedKkIds = [];

        foreach ($collection as $row) {
            try {
                // 1. Basic Cleaning
                $nik = trim($row['nik'] ?? '');
                $nama = trim($row['nama'] ?? '');
                $nkk = trim($row['nkk'] ?? $row['no_kk'] ?? '');

                if (empty($nik) || empty($nama) || empty($nkk)) {
                    continue;
                }

                // 2. Resolve Wilayah
                $wilayah = $this->resolveWilayah(
                    $row['rt'] ?? '001',
                    $row['rw'] ?? '001',
                    $row['dusun'] ?? null
                );

                // 3. Find or Create Kartu Keluarga
                $kk = \App\Models\KartuKeluarga::withTrashed()->firstOrCreate(
                    ['nkk' => $nkk],
                    [
                        'nama_kepala_keluarga' => $this->mapKedudukanKeluarga($row['kedudukan_keluarga'] ?? '') === 'Kepala Keluarga' ? $nama : 'Belum Ditentukan',
                        'nik_kepala_keluarga' => $this->mapKedudukanKeluarga($row['kedudukan_keluarga'] ?? '') === 'Kepala Keluarga' ? $nik : null,
                        'alamat' => $row['alamat'] ?? 'Alamat Desa Cibatu',
                        'rt_id' => $wilayah['rt_id'],
                        'rw_id' => $wilayah['rw_id'],
                        'dusun_id' => $wilayah['dusun_id'],
                    ]
                );

                $affectedKkIds[] = $kk->id;

                // 4. Create or Update Penduduk (ID-based)
                Penduduk::withTrashed()->updateOrCreate(
                    ['nik' => $nik],
                    [
                        'kartu_keluarga_id' => $kk->id,
                        'nama' => $nama,
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
                        'deleted_at' => null,
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Error importing row: ' . $e->getMessage());
                continue;
            }
        }

        // 5. Batch Recalculate Statistics (The Magic Performance Fix) ⚡
        $uniqueKkIds = array_unique($affectedKkIds);
        if (!empty($uniqueKkIds)) {
            $kkService = app(\App\Services\KartuKeluargaService::class);
            foreach ($uniqueKkIds as $kkId) {
                $kkService->recalculate($kkId);
            }
        }
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
