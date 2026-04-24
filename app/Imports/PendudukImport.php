<?php

namespace App\Imports;

use App\Models\Penduduk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PendudukImport implements ToCollection, WithHeadingRow, WithValidation
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // Skip empty rows
                if (empty($row['nik']) || empty($row['nama'])) {
                    continue;
                }

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
                        'rt' => $this->formatRT($row['rt'] ?? '01'),
                        'rw' => $this->formatRW($row['rw'] ?? '01'),
                        'dusun' => $this->determineDusun($row['rt'] ?? '01'),
                        'keterangan' => $row['keterangan'] ?? null,
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Error importing row: ' . $e->getMessage(), [
                    'row' => $row->toArray()
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
        // Generate 16-digit NKK
        return str_pad(rand(1000000000000000, 9999999999999999), 16, '0', STR_PAD_LEFT);
    }

    /**
     * Format RT to 3 digits
     */
    private function formatRT($rt)
    {
        return str_pad($rt, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Format RW to 3 digits
     */
    private function formatRW($rw)
    {
        return str_pad($rw, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Determine dusun based on RT
     */
    private function determineDusun($rt)
    {
        $rtFormatted = str_pad($rt, 3, '0', STR_PAD_LEFT);
        $dusunSatu = ['001', '002', '003', '004', '007', '008'];
        
        return in_array($rtFormatted, $dusunSatu) ? 'Dusun Satu' : 'Dusun Dua';
    }

    /**
     * Map jenis kelamin
     */
    private function mapJenisKelamin($value)
    {
        $value = strtolower(trim($value));
        
        if (in_array($value, ['l', 'laki-laki', 'male', 'pria'])) {
            return 'L';
        } elseif (in_array($value, ['p', 'perempuan', 'female', 'wanita'])) {
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
            '*.nik' => 'required|string|max:16|distinct|unique:penduduks,nik',
            '*.nama' => 'required|string|max:255',
        ];
    }
}
