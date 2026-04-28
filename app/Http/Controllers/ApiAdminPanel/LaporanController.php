<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Models\Mutasi;
use App\Models\Berita;
use App\Models\SuratPengajuan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class LaporanController extends Controller
{
    public function index(): JsonResponse
    {
        Gate::authorize('laporan.view');

        $genderDist = Penduduk::select('jenis_kelamin', DB::raw('count(*) as total'))->groupBy('jenis_kelamin')->get();
        
        $ageGroups = Penduduk::selectRaw('
            CASE
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) < 17 THEN "Anak (0-16)"
                WHEN TIMESTAMPDIFF(YEAR, tanggal_lahir, CURDATE()) BETWEEN 17 AND 60 THEN "Produktif (17-60)"
                ELSE "Lansia (60+)"
            END as age_group, COUNT(*) as total
        ')->groupBy('age_group')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'summary' => [
                    'total_penduduk' => Penduduk::count(),
                    'total_kk' => Penduduk::distinct('nkk')->count(),
                    'total_mutasi' => Mutasi::count(),
                ],
                'charts' => [
                    'gender' => $genderDist,
                    'age' => $ageGroups,
                ]
            ]
        ]);
    }

    public function penduduk(Request $request): JsonResponse
    {
        $query = Penduduk::query();
        if ($request->filled('dusun_id')) $query->where('dusun_id', $request->dusun_id);
        if ($request->filled('rt_id')) $query->where('rt_id', $request->rt_id);
        if ($request->filled('jenis_kelamin')) $query->where('jenis_kelamin', $request->jenis_kelamin);

        $data = $query->orderBy('nama')->paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'filters' => [
                    'dusuns' => \App\Models\Dusun::orderBy('nama')->get(),
                    'rts' => \App\Models\Rt::orderBy('kode')->get(),
                ]
            ]
        ]);
    }

    public function mutasi(Request $request): JsonResponse
    {
        $query = Mutasi::with('penduduk');
        if ($request->filled('jenis')) $query->where('jenis_mutasi', $request->jenis);
        if ($request->filled('start_date')) $query->whereDate('tanggal_mutasi', '>=', $request->start_date);

        $data = $query->orderBy('tanggal_mutasi', 'desc')->paginate($request->get('per_page', 50));

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => [
                'stats' => [
                    'kematian' => Mutasi::where('jenis_mutasi', 'kematian')->count(),
                    'kelahiran' => Mutasi::where('jenis_mutasi', 'kelahiran')->count(),
                    'pindah' => Mutasi::whereIn('jenis_mutasi', ['pindah_masuk', 'pindah_keluar'])->count(),
                ]
            ]
        ]);
    }

    public function generate(Request $request): JsonResponse
    {
        // Placeholder for PDF/Excel generation via dedicated service
        return response()->json(['status' => 'success', 'message' => 'Fungsi download laporan sedang disiapkan']);
    }
}
