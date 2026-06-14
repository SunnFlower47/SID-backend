<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DesaSetting extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    protected $casts = [
        'value' => 'string'
    ];

    // Groups constants
    const GROUP_GENERAL = 'general';
    const GROUP_LOGO = 'logo';
    const GROUP_GEOGRAPHY = 'geography';
    const GROUP_PROFILE = 'profile'; // New group for Vision, Mission, History
    const GROUP_SOCIAL = 'social';   // New group for Social Media

    /**
     * Get setting value by key
     */
    public static function getValue($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value, $type = 'text', $group = 'general', $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description
            ]
        );
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get all settings as key-value array
     */
    public static function getAllAsArray()
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get desa information
     */
    public static function getDesaInfo()
    {
        return [
            'nama_desa' => static::getValue('nama_desa', 'Desa Cibatu'),
            'kecamatan' => static::getValue('kecamatan', 'Cibatu'),
            'kabupaten' => static::getValue('kabupaten', 'Purwakarta'),
            'provinsi' => static::getValue('provinsi', 'Jawa Barat'),
            'kode_pos' => static::getValue('kode_pos', '41161'),
            'alamat_lengkap' => static::getValue('alamat_lengkap', 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161'),
            'telepon' => static::getValue('telepon', '(0264) 123456'),
            'email' => static::getValue('email', 'desacibatu.2001@gmail.com'),
            'website' => static::getValue('website', 'https://desa-cibatu.id'),
            'latitude' => static::getValue('latitude', '-6.5001403'),
            'longitude' => static::getValue('longitude', '107.5342964')
        ];
    }

    /**
     * Get full profile for centralized dashboard
     */
    public static function getFullProfile()
    {
        return [
            'general' => static::getDesaInfo(),
            'branding' => static::getLogos(),
            'geography' => static::getLuasWilayah(),
            'leadership' => [
                'kepala_desa' => static::getKepalaDesaInfo(),
                'sekretaris' => static::getSekretarisInfo(),
            ],
            'additional' => [
                'visi' => static::getValue('visi', ''),
                'misi' => static::getValue('misi', ''),
                'sejarah_desa' => static::getValue('sejarah_desa', ''),
                'tahun_berdiri' => static::getValue('tahun_berdiri', '1860'),
                'kepala_desa_pertama' => static::getValue('kepala_desa_pertama', 'Ki Arpan'),
                'karakteristik_desa' => static::getValue('karakteristik_desa', 'Industri'),
                'facebook' => static::getValue('link_facebook', ''),
                'instagram' => static::getValue('link_instagram', ''),
                'youtube' => static::getValue('link_youtube', ''),
                'whatsapp' => static::getValue('link_whatsapp', ''),
            ]
        ];
    }

    /**
     * Get luas wilayah information
     */
    public static function getLuasWilayah()
    {
        return [
            'luas_total' => static::getValue('luas_total', '1250'),
            'luas_pemukiman' => static::getValue('luas_pemukiman', '450'),
            'luas_pertanian' => static::getValue('luas_pertanian', '600'),
            'luas_hutan' => static::getValue('luas_hutan', '200'),
            'luas_lainnya' => static::getValue('luas_lainnya', '0')
        ];
    }

    /**
     * Get kepala desa information from struktur desa
     */
    public static function getKepalaDesaInfo()
    {
        $kepalaDesa = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')
            ->where('status_aktif', true)
            ->first();

        if ($kepalaDesa) {
            return [
                'nama' => $kepalaDesa->nama,
                'nip' => $kepalaDesa->nik ?? '',
                'jabatan' => $kepalaDesa->jabatan
            ];
        }

        // Fallback to settings if no data in struktur desa
        return [
            'nama' => static::getValue('nama_kepala_desa', 'H. MAMAN SUTARMAN, S.Pd.I'),
            'nip' => static::getValue('nip_kepala_desa', '19651231 199003 1 001'),
            'jabatan' => static::getValue('jabatan_kepala_desa', 'Kepala Desa Cibatu')
        ];
    }

    /**
     * Get sekretaris information from struktur desa
     */
    public static function getSekretarisInfo()
    {
        $sekretaris = \App\Models\StrukturDesa::where('kategori', 'sekretaris')
            ->where('status_aktif', true)
            ->first();

        if ($sekretaris) {
            return [
                'nama' => $sekretaris->nama,
                'nip' => $sekretaris->nik ?? '',
                'jabatan' => $sekretaris->jabatan
            ];
        }

        // Fallback to settings if no data in struktur desa
        return [
            'nama' => static::getValue('nama_sekretaris', 'Drs. BUDIMAN, M.Si'),
            'nip' => static::getValue('nip_sekretaris', '19700315 199203 1 002'),
            'jabatan' => static::getValue('jabatan_sekretaris', 'Sekretaris Desa Cibatu')
        ];
    }

    /**
     * Get logo paths
     */
    public static function getLogos()
    {
        return [
            'desa' => static::getValue('logo_desa'),
            'kabupaten' => static::getValue('logo_kabupaten'),
            'provinsi' => static::getValue('logo_provinsi')
        ];
    }

    /**
     * Get surat settings
     */
    public static function getSuratSettings()
    {
        return [
            'format_nomor_surat' => static::getValue('format_nomor_surat', '{kode_surat}/{nomor_urut}/{kode_desa}/{bulan}/{tahun}'),
            'kode_desa' => static::getValue('kode_desa', '2001'),
            'kode_surat_keterangan-domisili' => static::getValue('kode_surat_keterangan-domisili', 'SKD'),
            'kode_surat_pengantar' => static::getValue('kode_surat_pengantar', 'SP'),
            'kode_surat_pindah' => static::getValue('kode_surat_pindah', 'SKP'),
            'kode_surat_kematian' => static::getValue('kode_surat_kematian', 'SKK'),
            'kode_surat_kelahiran' => static::getValue('kode_surat_kelahiran', 'SKKL'),
            'kode_surat_tidak-mampu-dewasa' => static::getValue('kode_surat_tidak-mampu-dewasa', 'SKTM'),
            'kode_surat_tidak-mampu-anak' => static::getValue('kode_surat_tidak-mampu-anak', 'SKTM'),
            'kode_surat_sku' => static::getValue('kode_surat_sku', 'SKU'),
            'kode_surat_sktm_dewasa' => static::getValue('kode_surat_sktm_dewasa', 'SKTM'),
            'kode_surat_sktm_anak' => static::getValue('kode_surat_sktm_anak', 'SKTM'),
            'kode_surat_domisili' => static::getValue('kode_surat_domisili', 'SKD')
        ];
    }

    /**
     * Generate nomor surat with standard format: [Nomor Urut]/2001/[Bulan Romawi]/[Tahun]
     */
    public static function generateNomorSurat($kodeSurat = null)
    {
        $nomorUrut = static::getNextNomorUrut();
        $kodeDesa = static::getValue('kode_desa', '2001');
        $bulanRomawi = static::intToRoman(date('n'));
        $tahun = date('Y');

        // Format: [Kode Surat]/[Nomor Urut]/[Kode Desa]/[Bulan Romawi]/[Tahun]
        if ($kodeSurat) {
            return "{$kodeSurat}/{$nomorUrut}/{$kodeDesa}/{$bulanRomawi}/{$tahun}";
        }
        
        return "{$nomorUrut}/{$kodeDesa}/{$bulanRomawi}/{$tahun}";
    }

    /**
     * Convert integer to Roman numeral
     */
    public static function intToRoman($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    /**
     * Get next nomor urut for surat (berurutan per tahun)
     */
    private static function getNextNomorUrut()
    {
        $currentYear = date('Y');

        // Cek apakah counter untuk tahun ini sudah ada
        $lastNumber = static::getValue("last_surat_number_{$currentYear}", 0);

        // Jika belum ada counter untuk tahun ini, inisialisasi dengan nomor terakhir dari database
        if ($lastNumber == 0) {
            // Hitung dari surat_pengajuans yang sudah ada
            $approvedPengajuans = \App\Models\SuratPengajuan::whereYear('created_at', $currentYear)
                ->get();

            $maxNumber = 0;
            foreach ($approvedPengajuans as $pengajuan) {
                $nomorUrut = static::extractNomorUrutFromNomorSurat($pengajuan->nomor_surat);
                if ($nomorUrut > $maxNumber) {
                    $maxNumber = $nomorUrut;
                }
            }

            $lastNumber = $maxNumber;
        }

        $nextNumber = $lastNumber + 1;

        // Update counter untuk tahun ini
        static::setValue("last_surat_number_{$currentYear}", $nextNumber, 'number', 'system');

        return str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Extract nomor urut from nomor surat
     */
    private static function extractNomorUrutFromNomorSurat($nomorSurat)
    {
        // Format standar baru: 001/2001/IV/2024
        // Format lama: SKD/001/2001/01/2024
        $parts = explode('/', $nomorSurat);
        if (count($parts) >= 2) {
            // Jika bagian pertama adalah angka, berarti itu nomor urut (format baru)
            if (is_numeric($parts[0])) {
                return (int) $parts[0];
            }
            // Jika bukan, berarti nomor urut ada di bagian kedua (format lama)
            return (int) $parts[1];
        }
        return 0;
    }

    /**
     * Get the activity log options for the model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
