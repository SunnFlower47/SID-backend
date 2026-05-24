<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Penduduk;
use App\Models\SuratPengajuan;
use App\Models\DesaSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WebDesaController extends Controller
{



    /**
     * Check status pengajuan
     */
    public function checkStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_surat' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor surat harus diisi',
                'errors' => $validator->errors()
            ], 400);
        }

        $pengajuan = SuratPengajuan::with('penduduk')
            ->where('nomor_surat', $request->nomor_surat)
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor surat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nomor_surat' => $pengajuan->nomor_surat,
                'surat_type' => $pengajuan->surat_type,
                'penduduk' => $pengajuan->penduduk->nama,
                'status' => $pengajuan->status,
                'status_label' => ucfirst($pengajuan->status),
                'tanggal_pengajuan' => $pengajuan->created_at->format('d/m/Y H:i'),
                'tanggal_proses' => $pengajuan->processed_at ? $pengajuan->processed_at->format('d/m/Y H:i') : null,
                'tanggal_selesai' => $pengajuan->completed_at ? $pengajuan->completed_at->format('d/m/Y H:i') : null,
                'keterangan_admin' => $pengajuan->keterangan_admin
            ]
        ]);
    }

    /**
     * Get desa information (legacy method for compatibility)
     */
    public function getDesaInfo()
    {
        // Redis cache untuk 2 jam (desa info sangat jarang berubah)
        return Cache::remember('api_desa_info', 7200, function () {
            try {
                $desaInfo = DesaSetting::getDesaInfo();
                $kepalaDesa = DesaSetting::getKepalaDesaInfo();
                $sekretaris = DesaSetting::getSekretarisInfo();
                $logos = DesaSetting::getLogos();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'desa' => $desaInfo,
                        'kepala_desa' => $kepalaDesa,
                        'sekretaris' => $sekretaris,
                        'logos' => $logos,
                        // Legacy fields for backward compatibility
                        'nama_desa' => $desaInfo['nama_desa'] ?? 'Desa Cibatu',
                        'kecamatan' => $desaInfo['kecamatan'] ?? 'Cibatu',
                        'kabupaten' => $desaInfo['kabupaten'] ?? 'Purwakarta',
                        'provinsi' => $desaInfo['provinsi'] ?? 'Jawa Barat',
                        'alamat' => $desaInfo['alamat_lengkap'] ?? 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta',
                        'kode_pos' => $desaInfo['kode_pos'] ?? '41151',
                        'telepon' => $desaInfo['telepon'] ?? '(0264) 123456',
                        'email' => $desaInfo['email'] ?? 'desa@cibatu.id',
                        'website' => $desaInfo['website'] ?? 'https://cibatu.desa.id'
                    ]
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=7200',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil informasi desa',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get statistics data
     */
    public function getStatistics()
    {
        // Redis cache untuk 2 menit (statistics sering berubah tapi tidak real-time)
        return Cache::remember('api_statistics', 120, function () {
            // Real-time data without cache
            $stats = DB::table('penduduks as p')
                ->leftJoin('kartu_keluargas as kk', 'p.kartu_keluarga_id', '=', 'kk.id')
                ->select([
                    // Total penduduk aktif (tidak di-soft delete)
                    DB::raw('COUNT(*) as total_penduduk'),

                    // Total KK (Sekarang di tabel kartu_keluargas)
                    DB::raw('COUNT(DISTINCT p.kartu_keluarga_id) as total_kk'),

                    // Total RT - Sekarang di tabel kartu_keluargas
                    DB::raw('COUNT(DISTINCT kk.rt_id) as total_rt'),

                    // Jenis kelamin (Sesuai database L/P atau LAKI-LAKI/PEREMPUAN)
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin IN ("L", "LAKI-LAKI") THEN 1 END) as laki_laki'),
                    DB::raw('COUNT(CASE WHEN p.jenis_kelamin IN ("P", "PEREMPUAN") THEN 1 END) as perempuan'),
                ])
                ->whereNull('p.deleted_at')
                ->first();

                // Extract values dari single query
                $totalPenduduk = $stats->total_penduduk;
                $totalKK = $stats->total_kk;
                $totalRt = $stats->total_rt;
                $lakiLaki = $stats->laki_laki;
                $perempuan = $stats->perempuan;

                // Simple counts untuk data lain
                $totalMutasi = \App\Models\Mutasi::count();
                $totalBerita = \App\Models\Berita::published()->count();
                $totalPengajuan = SuratPengajuan::count();

            // OPTIMASI: Query terpisah untuk pendidikan dan pekerjaan dengan LEFT JOIN
            $pendidikan = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pendidikan', DB::raw('COUNT(*) as jumlah'))
                ->whereNull('m.id')
                ->whereNotNull('p.pendidikan')
                ->where('p.pendidikan', '!=', '')
                ->groupBy('p.pendidikan')
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->pluck('jumlah', 'pendidikan')
                ->toArray();

            $pekerjaan = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select('p.pekerjaan', DB::raw('COUNT(*) as jumlah'))
                ->whereNull('m.id')
                ->whereNotNull('p.pekerjaan')
                ->where('p.pekerjaan', '!=', '')
                ->groupBy('p.pekerjaan')
                ->orderBy('jumlah', 'desc')
                ->limit(5)
                ->pluck('jumlah', 'pekerjaan')
                ->toArray();

            // Age groups calculation
            $ageGroups = DB::table('penduduks as p')
                ->leftJoin('mutasis as m', function($join) {
                    $join->on('m.penduduk_id', '=', 'p.id')
                         ->whereIn('m.jenis_mutasi', ['kematian', 'pindah_keluar']);
                })
                ->select(
                    DB::raw('CASE
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) < 5 THEN "0-4"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 5 AND 9 THEN "5-9"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 10 AND 14 THEN "10-14"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 15 AND 19 THEN "15-19"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 20 AND 24 THEN "20-24"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 25 AND 29 THEN "25-29"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 30 AND 34 THEN "30-34"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 35 AND 39 THEN "35-39"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 40 AND 44 THEN "40-44"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 45 AND 49 THEN "45-49"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 50 AND 54 THEN "50-54"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 55 AND 59 THEN "55-59"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) BETWEEN 60 AND 64 THEN "60-64"
                        WHEN TIMESTAMPDIFF(YEAR, p.tanggal_lahir, CURDATE()) >= 65 THEN "65+"
                        ELSE "Tidak Diketahui"
                    END as age_group'),
                    DB::raw('COUNT(CASE WHEN m.id IS NULL THEN 1 END) as total')
                )
                ->whereNull('m.id')
                ->whereNotNull('p.tanggal_lahir')
                ->groupBy('age_group')
                ->orderByRaw('CASE
                    WHEN age_group = "0-4" THEN 1
                    WHEN age_group = "5-9" THEN 2
                    WHEN age_group = "10-14" THEN 3
                    WHEN age_group = "15-19" THEN 4
                    WHEN age_group = "20-24" THEN 5
                    WHEN age_group = "25-29" THEN 6
                    WHEN age_group = "30-34" THEN 7
                    WHEN age_group = "35-39" THEN 8
                    WHEN age_group = "40-44" THEN 9
                    WHEN age_group = "45-49" THEN 10
                    WHEN age_group = "50-54" THEN 11
                    WHEN age_group = "55-59" THEN 12
                    WHEN age_group = "60-64" THEN 13
                    WHEN age_group = "65+" THEN 14
                    ELSE 15
                END')
                ->get();

                // Calculate usia produktif (15-64 tahun) and usia lansia (65+ tahun)
                $usiaProduktif = 0;
                $usiaLansia = 0;

                foreach ($ageGroups as $group) {
                    $ageRange = $group->age_group;
                    $total = $group->total;

                    if ($ageRange === '15-19' || $ageRange === '20-24' || $ageRange === '25-29' ||
                        $ageRange === '30-34' || $ageRange === '35-39' || $ageRange === '40-44' ||
                        $ageRange === '45-49' || $ageRange === '50-54' || $ageRange === '55-59' ||
                        $ageRange === '60-64') {
                        $usiaProduktif += $total;
                    } elseif ($ageRange === '65+') {
                        $usiaLansia += $total;
                    }
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_penduduk' => $totalPenduduk,
                        'total_kk' => $totalKK,
                        'total_rt' => $totalRt,
                        'total_mutasi' => $totalMutasi,
                        'total_berita' => $totalBerita,
                        'total_pengajuan' => $totalPengajuan,
                        'laki_laki' => $lakiLaki,
                        'perempuan' => $perempuan,
                        'pendidikan' => $pendidikan,
                        'pekerjaan' => $pekerjaan,
                        'age_groups' => $ageGroups,
                        'usia_produktif' => $usiaProduktif,
                        'usia_lansia' => $usiaLansia,
                        // Legacy fields for backward compatibility
                        'penduduk' => $totalPenduduk,
                        'kartu_keluarga' => $totalKK, // Keep for API compatibility
                        'mutasi' => $totalMutasi,
                        'berita' => $totalBerita,
                        'pengajuan_surat' => $totalPengajuan
                    ]
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=120',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
        });
    }

    /**
     * Get penduduk statistics
     */
    public function getPendudukStats()
    {
        $stats = [
            'total' => Penduduk::whereNull('deleted_at')->count(),
            'laki_laki' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['L', 'LAKI-LAKI'])->count(),
            'perempuan' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['P', 'PEREMPUAN'])->count(),
            'by_age' => [
                'anak' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17')->count(),
                'remaja' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 17 AND 30')->count(),
                'dewasa' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 31 AND 50')->count(),
                'lansia' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 51 AND 65')->count(),
                'manula' => Penduduk::whereNull('deleted_at')->whereRaw('TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) > 65')->count(),
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get KK statistics
     */
    public function getKKStats()
    {
        $stats = [
            'total' => DB::table('penduduks')->whereNull('deleted_at')->distinct('kartu_keluarga_id')->count(),
            'by_dusun' => DB::table('penduduks as p')
                ->join('kartu_keluargas as kk', 'p.kartu_keluarga_id', '=', 'kk.id')
                ->join('dusuns as d', 'kk.dusun_id', '=', 'd.id')
                ->selectRaw('d.nama as dusun, COUNT(DISTINCT p.kartu_keluarga_id) as total')
                ->whereNull('p.deleted_at')
                ->groupBy('d.nama')
                ->get()
                ->pluck('total', 'dusun')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get mutasi statistics
     */
    public function getMutasiStats()
    {
        $stats = [
            'total' => \App\Models\Mutasi::count(),
            'masuk' => \App\Models\Mutasi::where('jenis_mutasi', 'masuk')->count(),
            'keluar' => \App\Models\Mutasi::where('jenis_mutasi', 'keluar')->count(),
            'this_month' => \App\Models\Mutasi::whereMonth('created_at', now()->month)->count()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get public statistics for welcome page (no API key required)
     */
    public function getPublicStatistics()
    {
        // Cache untuk 5 menit
        return Cache::remember('api_public_statistics', 300, function () {
            try {
                // Basic statistics only (no sensitive data)
                $stats = [
                    'total_penduduk' => DB::table('penduduks')->whereNull('deleted_at')->count(),
                    'total_kk' => DB::table('penduduks')->whereNull('deleted_at')->distinct('kartu_keluarga_id')->count(),
                    'total_rt' => DB::table('kartu_keluargas')->distinct('rt_id')->count(),
                    'surat_selesai' => SuratPengajuan::whereIn('status', ['completed', 'selesai'])->count(),
                    'pengaduan_total' => \App\Models\Pengaduan::count(),
                    'pengaduan_selesai' => \App\Models\Pengaduan::where('status', 'selesai')->count(),
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil statistik',
                    'error' => $e->getMessage()
                ], 500);
            }
        });
    }

    /**
     * Get public penduduk statistics for welcome page (no API key required)
     */
    public function getPublicPendudukStats()
    {
        // Cache untuk 5 menit
        return Cache::remember('api_public_penduduk_stats', 300, function () {
            try {
                $stats = [
                    'total' => Penduduk::whereNull('deleted_at')->count(),
                    'laki_laki' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['L', 'LAKI-LAKI'])->count(),
                    'perempuan' => Penduduk::whereNull('deleted_at')->whereIn('jenis_kelamin', ['P', 'PEREMPUAN'])->count(),
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=300',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil statistik penduduk',
                ], 500);
            }
        });
    }

    /**
     * Get public desa info (nama desa + sosial media) for welcome page footer
     * No API key required — returns only non-sensitive public info
     */
    public function getPublicDesaInfo()
    {
        // Cache 10 menit — sosmed jarang berubah
        return Cache::remember('api_public_desa_info', 600, function () {
            try {
                $desaInfo = DesaSetting::getDesaInfo();

                // Sanitize & validate URLs sebelum dikembalikan
                $sanitizeUrl = function($url) {
                    if (empty($url)) return null;
                    $parsed = parse_url($url);
                    // Hanya izinkan http/https scheme
                    if (!isset($parsed['scheme']) || !in_array($parsed['scheme'], ['http', 'https'])) {
                        return null;
                    }
                    return filter_var($url, FILTER_SANITIZE_URL);
                };

                return response()->json([
                    'success' => true,
                    'data' => [
                        'nama_desa' => $desaInfo['nama_desa'] ?? 'Desa Cibatu',
                        'email' => $desaInfo['email'] ?? null,
                        'telepon' => $desaInfo['telepon'] ?? null,
                        'alamat' => $desaInfo['alamat_lengkap'] ?? null,
                        'social' => [
                            'facebook' => $sanitizeUrl(DesaSetting::getValue('link_facebook', '')),
                            'instagram' => $sanitizeUrl(DesaSetting::getValue('link_instagram', '')),
                            'whatsapp' => $sanitizeUrl(DesaSetting::getValue('link_whatsapp', '')),
                            'youtube' => $sanitizeUrl(DesaSetting::getValue('link_youtube', '')),
                        ],
                    ]
                ])->withHeaders([
                    'Cache-Control' => 'public, max-age=600',
                    'X-Content-Type-Options' => 'nosniff',
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil info desa',
                ], 500);
            }
        });
    }

    /**
     * Get contact information
     */
    public function getContactInfo()
    {
        $desaSettings = DesaSetting::getByGroup('desa_info');

        return response()->json([
            'success' => true,
            'data' => [
                'alamat' => $desaSettings['alamat'] ?? 'Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta',
                'telepon' => $desaSettings['telepon'] ?? '(0264) 123456',
                'email' => $desaSettings['email'] ?? 'desa@cibatu.id',
                'website' => $desaSettings['website'] ?? 'https://cibatu.desa.id',
                'facebook' => $desaSettings['facebook'] ?? null,
                'instagram' => $desaSettings['instagram'] ?? null,
                'jam_kerja' => 'Senin - Jumat: 08:00 - 16:00',
                'koordinat' => [
                    'lat' => -6.5567,
                    'lng' => 107.4432
                ]
            ]
        ]);
    }

    /**
     * Submit contact form
     */
public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'telepon' => 'nullable|string|max:20',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            // Simpan ke tabel pengaduans sebagai kategori "lainnya"
            $pengaduan = \App\Models\Pengaduan::create([
                'nama_pelapor' => $request->nama,
                'email' => $request->email,
                'telepon' => $request->telepon,
                'alamat' => 'Tidak disebutkan', // Default karena tidak ada field alamat di form kontak
                'kategori' => 'lainnya',
                'judul' => $request->subjek,
                'deskripsi' => $request->pesan,
                'lokasi' => null,
                'foto' => null,
                'prioritas' => 'rendah', // Default untuk kontak
                'status' => 'baru'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim. Terima kasih atas masukan Anda!',
                'data' => [
                    'id' => $pengaduan->id,
                    'nomor_pengaduan' => 'P-' . str_pad($pengaduan->id, 6, '0', STR_PAD_LEFT)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get services list
     */
    public function getServices()
    {
        $services = [
            [
                'id' => 'surat-keterangan',
                'name' => 'Surat Keterangan',
                'description' => 'Berbagai jenis surat keterangan',
                'icon' => 'fas fa-file-alt',
                'color' => 'blue'
            ],
            [
                'id' => 'bantuan-sosial',
                'name' => 'Bantuan Sosial',
                'description' => 'Informasi bantuan sosial pemerintah',
                'icon' => 'fas fa-hand-holding-heart',
                'color' => 'green'
            ],
            [
                'id' => 'pengaduan',
                'name' => 'Pengaduan',
                'description' => 'Layanan pengaduan dan keluhan',
                'icon' => 'fas fa-comments',
                'color' => 'red'
            ],
            [
                'id' => 'konsultasi',
                'name' => 'Konsultasi',
                'description' => 'Konsultasi administrasi desa',
                'icon' => 'fas fa-user-tie',
                'color' => 'purple'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get announcements
     */
    public function getAnnouncements()
    {
        $announcements = \App\Models\Berita::published()
            ->where('kategori', 'pengumuman')
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $announcements
        ]);
    }

    /**
     * Get single announcement
     */
    public function getAnnouncement($id)
    {
        $announcement = \App\Models\Berita::published()
            ->where('id', $id)
            ->where('kategori', 'pengumuman')
            ->first();

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }


    /**
     * Generate nomor surat
     */
    private function generateNomorSurat($suratType)
    {
        $type = \App\Models\SuratType::find($suratType);
        $kodeSurat = $type ? $type->kode : 'SK';

        return \App\Models\DesaSetting::generateNomorSurat($kodeSurat);
    }

    /**
     * Get desa information
     */
    public function desaInfo()
    {
        try {
            $desaInfo = DesaSetting::getDesaInfo();
            $kepalaDesa = DesaSetting::getKepalaDesaInfo();
            $sekretaris = DesaSetting::getSekretarisInfo();
            $logos = DesaSetting::getLogos();

            return response()->json([
                'success' => true,
                'data' => [
                    'desa' => $desaInfo,
                    'kepala_desa' => $kepalaDesa,
                    'sekretaris' => $sekretaris,
                    'logos' => $logos
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contact information
     */
    public function contactInfo()
    {
        try {
            $desaInfo = DesaSetting::getDesaInfo();

            return response()->json([
                'success' => true,
                'data' => [
                    'alamat' => $desaInfo['alamat_lengkap'],
                    'telepon' => $desaInfo['telepon'],
                    'email' => $desaInfo['email'],
                    'website' => $desaInfo['website'],
                    'kode_pos' => $desaInfo['kode_pos'],
                    'kecamatan' => $desaInfo['kecamatan'],
                    'kabupaten' => $desaInfo['kabupaten'],
                    'provinsi' => $desaInfo['provinsi']
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil informasi kontak',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
