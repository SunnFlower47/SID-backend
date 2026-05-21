<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Http\Requests\Kependudukan\StorePendudukRequest;
use App\Http\Requests\Kependudukan\UpdatePendudukRequest;
use App\Services\Kependudukan\PendudukService;
use App\Models\Rt;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PendudukExport;

class PendudukController extends Controller
{
    protected $pendudukService;

    public function __construct(PendudukService $pendudukService)
    {
        $this->pendudukService = $pendudukService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('kependudukan');

        $query = Penduduk::withWilayah()
            ->filter($request->all())
            ->orderByFamilyRole();

        $penduduks = $query->paginate($request->get('per_page', 50));
        
        return response()->json([
            'status' => 'success',
            'data' => $penduduks,
            'meta' => [
                'stats' => [
                    'total' => Penduduk::whereNull('deleted_at')->count(),
                    'laki_laki' => Penduduk::whereNull('deleted_at')
                        ->where('jenis_kelamin', 'LAKI-LAKI')
                        ->count(),
                    'perempuan' => Penduduk::whereNull('deleted_at')
                        ->where('jenis_kelamin', 'PEREMPUAN')
                        ->count(),
                    'total_kk' => \App\Models\KartuKeluarga::count(),
                ],
                'filters' => [
                    'rt' => \App\Models\Rt::orderBy('kode')->get(['id', 'kode']),
                    'rw' => \App\Models\Rw::orderBy('kode')->get(['id', 'kode']),
                    'dusun' => \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']),
                ]
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendudukRequest $request): JsonResponse
    {
        Gate::authorize('kependudukan');

        try {
            $validated = $request->validated();
            
            $penduduk = $this->pendudukService->createPenduduk(
                $validated,
                $request->input('family_members', [])
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Data penduduk berhasil ditambahkan',
                'data' => $penduduk
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Store Penduduk Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menambahkan data penduduk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        Gate::authorize('kependudukan');
        
        $penduduk = Penduduk::withWilayah()->withTrashed()->findOrFail($id);
        $penduduk->load(['mutasis', 'kartuKeluarga.penduduks']);
        
        return response()->json([
            'status' => 'success',
            'data' => $penduduk->toArray()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendudukRequest $request, $id): JsonResponse
    {
        Gate::authorize('kependudukan');

        try {
            $penduduk = Penduduk::withTrashed()->findOrFail($id);
            $validated = $request->validated();

            $this->pendudukService->updatePenduduk($penduduk, $validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Data penduduk berhasil diperbarui',
                'data' => $penduduk->fresh()->load('rtMaster', 'rwMaster', 'dusunMaster')
            ]);

        } catch (\Exception $e) {
            Log::error('API Update Penduduk Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data penduduk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        Gate::authorize('kependudukan');

        try {
            $penduduk = Penduduk::withTrashed()->findOrFail($id);
            $penduduk->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data penduduk berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus penduduk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search penduduk for autocomplete.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        if (strlen($query) < 3) {
             return response()->json([]);
        }

        $penduduks = Penduduk::withWilayah()->withTrashed()
                    ->where(function($q) use ($query) {
                        $q->where('nama', 'like', "%{$query}%")
                          ->orWhere('nik', 'like', "%{$query}%");
                    })
                    ->limit(10)
                    ->get(['id', 'nama', 'nik', 'alamat', 'pekerjaan', 'deleted_at'])
                    ->map(function($p) {
                        // Gunakan accessors yang sudah sinkron dengan KartuKeluarga
                        $p->rt_label = $p->rt_label;
                        $p->rw_label = $p->rw_label;
                        $p->dusun_label = $p->dusun_label;
                        return $p;
                    });
                    
        return response()->json([
            'status' => 'success',
            'data' => $penduduks
        ]);
    }

    /**
     * Check if NIK exists.
     */
    public function checkNIKExists(Request $request): JsonResponse
    {
        $nik = $request->get('nik');
        if (strlen($nik) !== 16) return response()->json(['exists' => false]);
        
        $query = Penduduk::withWilayah()->withTrashed()->where('nik', $nik);
        if ($request->exclude_id) $query->where('id', '!=', $request->exclude_id);
        
        $penduduk = $query->first();
        return response()->json([
            'exists' => !!$penduduk,
            'data' => $penduduk
        ]);
    }

    /**
     * Check if NKK exists.
     */
    public function checkNKKExists(Request $request): JsonResponse
    {
        $nkk = $request->get('nkk');
        if (strlen($nkk) !== 16) return response()->json(['exists' => false]);
        
        $kk = \App\Models\KartuKeluarga::withWilayah()->where('nkk', $nkk)->first();
        
        return response()->json([
            'exists' => !!$kk,
            'data' => $kk
        ]);
    }

    /**
     * Export to Excel.
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('kependudukan');
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        return Excel::download(
            new PendudukExport($request), 
            'data_penduduk_' . now()->format('Y-m-d_H-i-s') . '.xlsx'
        );
    }
}
