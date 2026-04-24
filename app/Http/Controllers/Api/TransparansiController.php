<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProyekDesa;
use App\Models\BantuanSosial;
use App\Models\Apbdes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransparansiController extends Controller
{
    /**
     * Get transparansi data
     */
    public function index()
    {
        try {
            $tahun = now()->year;

            // Get APBDes data from database
            $apbdesData = Apbdes::tahun($tahun)->disetujui()->get();
            $totalAnggaran = $apbdesData->sum('anggaran');
            $totalRealisasi = $apbdesData->sum('realisasi');
            $persentase = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 2) : 0;

            $data = [
                'apbdes' => [
                    'tahun' => $tahun,
                    'total_anggaran' => $totalAnggaran,
                    'realisasi' => $totalRealisasi,
                    'persentase' => $persentase
                ],
                'proyek' => [
                    'total' => ProyekDesa::count(),
                    'selesai' => ProyekDesa::where('status', 'selesai')->count(),
                    'berjalan' => ProyekDesa::where('status', 'berjalan')->count(),
                    'rencana' => ProyekDesa::where('status', 'rencana')->count()
                ],
                'bantuan_sosial' => [
                    'total_program' => BantuanSosial::count(),
                    'total_penerima' => \App\Models\PenerimaBantuanSosial::count(),
                    'total_dana' => BantuanSosial::sum('total_dana')
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data transparansi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get APBDes data
     */
    public function apbdes(Request $request)
    {
        try {
            $tahun = $request->get('tahun', now()->year);

            // Get APBDes data from database
            $apbdesData = Apbdes::tahun($tahun)->disetujui()->get();

            // Calculate totals by jenis
            $pendapatan = $apbdesData->where('jenis', 'pendapatan');
            $belanja = $apbdesData->where('jenis', 'belanja');
            $pembiayaan = $apbdesData->where('jenis', 'pembiayaan');

            $totalAnggaran = $apbdesData->sum('anggaran');
            $totalRealisasi = $apbdesData->sum('realisasi');
            $persentase = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 2) : 0;

            // Group by nama_rekening for detailed breakdown
            $pendapatanDetail = $pendapatan->groupBy('nama_rekening')->map(function($items) {
                return [
                    'sumber' => $items->first()->nama_rekening,
                    'jumlah' => $items->sum('anggaran'),
                    'realisasi' => $items->sum('realisasi'),
                    'persentase' => $items->sum('anggaran') > 0 ? round(($items->sum('realisasi') / $items->sum('anggaran')) * 100, 2) : 0
                ];
            })->values();

            $belanjaDetail = $belanja->groupBy('nama_rekening')->map(function($items) {
                return [
                    'bidang' => $items->first()->nama_rekening,
                    'jumlah' => $items->sum('anggaran'),
                    'realisasi' => $items->sum('realisasi'),
                    'persentase' => $items->sum('anggaran') > 0 ? round(($items->sum('realisasi') / $items->sum('anggaran')) * 100, 2) : 0
                ];
            })->values();

            $data = [
                'tahun' => $tahun,
                'total_anggaran' => $totalAnggaran,
                'realisasi' => $totalRealisasi,
                'persentase' => $persentase,
                'pendapatan' => $pendapatanDetail,
                'belanja' => $belanjaDetail,
                'detail' => [
                    'pendapatan' => [
                        'total' => $pendapatan->sum('anggaran'),
                        'realisasi' => $pendapatan->sum('realisasi')
                    ],
                    'belanja' => [
                        'total' => $belanja->sum('anggaran'),
                        'realisasi' => $belanja->sum('realisasi')
                    ],
                    'pembiayaan' => [
                        'total' => $pembiayaan->sum('anggaran'),
                        'realisasi' => $pembiayaan->sum('realisasi')
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data APBDes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get proyek pembangunan data
     */
    public function proyekPembangunan(Request $request)
    {
        try {
            $proyek = ProyekDesa::query();
            $tahun = $request->get('tahun', now()->year);

            // Filter by year based on tanggal_mulai
            $proyek->whereYear('tanggal_mulai', $tahun);

            if ($request->has('status')) {
                $proyek->where('status', $request->status);
            }

            $data = $proyek->orderBy('created_at', 'desc')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => $item->id,
                        'nama' => $item->nama_proyek,
                        'deskripsi' => $item->deskripsi,
                        'lokasi' => $item->lokasi,
                        'anggaran' => $item->anggaran,
                        'progress' => $item->progress ?? 0,
                        'status' => $item->status,
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'tanggal_selesai' => $item->tanggal_selesai
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data proyek pembangunan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bantuan sosial transparansi data
     */
    public function bantuanSosialTransparansi(Request $request)
    {
        try {
            $tahun = $request->get('tahun', now()->year);

            // Get data from database dengan query yang aman
            $bantuanSosial = BantuanSosial::select('id', 'nama_program', 'deskripsi', 'status', 'tanggal_mulai', 'tanggal_selesai')
                ->whereYear('tanggal_mulai', $tahun)
                ->get();

            // Hitung total penerima dan dana dari tabel penerima
            $totalPenerima = \App\Models\PenerimaBantuanSosial::count();
            $totalDana = \App\Models\PenerimaBantuanSosial::sum('nilai_diterima');

            $data = [
                'total_program' => $bantuanSosial->count(),
                'total_penerima' => $totalPenerima,
                'total_dana' => $totalDana,
                'program' => $bantuanSosial->map(function($item) {
                    // Hitung penerima dan dana per program
                    $penerimaCount = \App\Models\PenerimaBantuanSosial::where('bantuan_sosial_id', $item->id)->count();
                    $totalDanaProgram = \App\Models\PenerimaBantuanSosial::where('bantuan_sosial_id', $item->id)->sum('nilai_diterima');

                    return [
                        'id' => $item->id,
                        'nama_program' => $item->nama_program,
                        'deskripsi' => $item->deskripsi,
                        'total_dana' => $totalDanaProgram,
                        'jumlah_penerima' => $penerimaCount,
                        'status' => $item->status,
                        'tanggal_mulai' => $item->tanggal_mulai,
                        'tanggal_selesai' => $item->tanggal_selesai
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error in bantuanSosialTransparansi: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data bantuan sosial transparansi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
