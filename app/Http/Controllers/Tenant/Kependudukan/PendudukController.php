<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Penduduk;
use App\Http\Requests\StorePendudukRequest;
use App\Http\Requests\UpdatePendudukRequest;
use App\Services\PendudukService;
use App\Exports\PendudukExport;
use App\Models\Rt;
use App\Models\Rw;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Inertia\Inertia;

class PendudukController extends Controller
{
    protected $pendudukService;

    public function __construct(PendudukService $pendudukService)
    {
        $this->middleware(['auth', 'can:kependudukan']);
        $this->pendudukService = $pendudukService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simpan query terakhir dari halaman index agar filter + page tidak hilang saat kembali dari CRUD
        session(['penduduk_index_query' => $request->query()]);

        $query = Penduduk::withWilayah()
            ->filter($request->all())
            ->orderByFamilyRole();

        $penduduks = $query->paginate(50);
        
        // Statistics - Use KartuKeluarga for total_kk (Source of Truth), exclude resolved/archived KK
        $stats = [
            'total' => Penduduk::whereNull('deleted_at')->count(),
            'laki_laki' => Penduduk::where('jenis_kelamin', 'LAKI-LAKI')->whereNull('deleted_at')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'PEREMPUAN')->whereNull('deleted_at')->count(),
            'total_kk' => \App\Models\KartuKeluarga::where('anggota_aktif', '>', 0)->count(),
        ];

        // Filter Lists (Fetched from Master Tables)
        $rtList = \App\Models\Rt::orderBy('kode')->get();
        $rwList = \App\Models\Rw::orderBy('kode')->get();
        $dusunList = \App\Models\Dusun::orderBy('nama')->get();

        return Inertia::render('Tenant/Penduduk/Index', [
            'penduduks' => $penduduks,
            'stats' => $stats,
            'rtList' => $rtList,
            'rwList' => $rwList,
            'dusunList' => $dusunList,
            'filters' => $request->all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('kependudukan');

        $existingNKKs = \App\Models\KartuKeluarga::withWilayah()
            ->orderBy('nkk')
            ->get()
            ->map(function($kk) {
                return [
                    'nkk' => $kk->nkk,
                    'kepala_keluarga' => $kk->nama_kepala_keluarga,
                    'alamat' => $kk->alamat,
                    'rt' => $kk->rt_label,
                    'rw' => $kk->rw_label,
                ];
            });

        $rws = Rw::with(['rts.dusun'])->orderBy('kode')->get();
        $masterRwOptions = $rws->map(function ($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function ($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun_id' => $rt->dusun_id,
                        'dusun' => optional($rt->dusun)->nama,
                    ];
                })->values(),
            ];
        })->values();

        return Inertia::render('Tenant/Penduduk/Create', [
            'existingNKKs' => $existingNKKs,
            'rws' => $rws,
            'masterRwOptions' => $masterRwOptions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendudukRequest $request)
    {
        Gate::authorize('kependudukan');

        try {
            $validated = $request->validated();
            
            // Automatically set dusun_id if rt_id is provided but dusun_id is missing
            if (!empty($validated['rt_id']) && empty($validated['dusun_id'])) {
                $rtModel = Rt::find($validated['rt_id']);
                if ($rtModel) {
                    $validated['dusun_id'] = $rtModel->dusun_id;
                }
            }

            $penduduk = $this->pendudukService->createPenduduk(
                $validated,
                $request->input('family_members', [])
            );

            $familyCount = count(array_filter($request->input('family_members', []), fn($m) => !empty($m['nik'])));
            $message = 'Data penduduk berhasil ditambahkan!' . ($familyCount > 0 ? " Beserta {$familyCount} anggota keluarga." : '');

            return redirect()->route('penduduk.index', $this->getFilterRedirect($request, $penduduk))
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data penduduk: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Penduduk $penduduk)
    {
        $penduduk->load([
            'mutasis', 
            'kartuKeluarga.rtMaster', 
            'kartuKeluarga.rwMaster', 
            'kartuKeluarga.dusunMaster',
            'kartuKeluarga.penduduks'
        ]);
        return Inertia::render('Tenant/Penduduk/Show', [
            'penduduk' => $penduduk
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penduduk $penduduk)
    {
        Gate::authorize('kependudukan');
        $penduduk->load('kartuKeluarga.rtMaster', 'kartuKeluarga.rwMaster', 'kartuKeluarga.dusunMaster');
        $rws = Rw::with(['rts.dusun'])->orderBy('kode')->get();
        $masterRwOptions = $rws->map(function ($rw) {
            return [
                'id' => $rw->id,
                'kode' => $rw->kode,
                'nama' => $rw->nama,
                'rts' => $rw->rts->map(function ($rt) {
                    return [
                        'id' => $rt->id,
                        'kode' => $rt->kode,
                        'dusun' => optional($rt->dusun)->nama,
                    ];
                })->values(),
            ];
        })->values();
        return Inertia::render('Tenant/Penduduk/Edit', [
            'penduduk' => $penduduk,
            'rws' => $rws,
            'masterRwOptions' => $masterRwOptions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendudukRequest $request, Penduduk $penduduk)
    {
        Gate::authorize('kependudukan');

        try {
            $validated = $request->validated();
            
            // Sync dusun_id if rt_id changed
            if (!empty($validated['rt_id']) && $validated['rt_id'] != $penduduk->rt_id) {
                // dusun_id is no longer stored on penduduk
            }

            $this->pendudukService->updatePenduduk($penduduk, $validated);

            return redirect()->route('penduduk.index', $this->getFilterRedirect($request, $penduduk))
                ->with('success', 'Data penduduk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui data penduduk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penduduk $penduduk)
    {
        Gate::authorize('kependudukan');

        try {
            $penduduk->delete();
            return redirect()->route('penduduk.index', $this->getFilterRedirect(request(), $penduduk))
                ->with('success', 'Data penduduk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus penduduk: ' . $e->getMessage());
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        Gate::authorize('kependudukan');
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        return Excel::download(new PendudukExport($request), 'data_penduduk_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Update address for entire family
     */
    public function updateFamilyAddress(Request $request, $nkk)
    {
        Gate::authorize('kependudukan');

        $validated = $request->validate([
            'alamat' => 'required|string|max:500',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
        ]);

        $rtModel = Rt::find($validated['rt_id']);
        if (!$rtModel || (int)$rtModel->rw_id !== (int)$validated['rw_id']) {
            return redirect()->back()->withInput()->with('error', 'RT tidak sesuai dengan RW yang dipilih.');
        }

        $validated['dusun_id'] = $rtModel->dusun_id;

        try {
            $kk = \App\Models\KartuKeluarga::where('nkk', $nkk)->firstOrFail();
            
            $kk->update([
                'alamat' => $validated['alamat'],
                'rt_id' => $validated['rt_id'],
                'rw_id' => $validated['rw_id'],
                'dusun_id' => $validated['dusun_id'],
            ]);

            app(\App\Services\KartuKeluargaService::class)->recalculate($kk->id);

            return redirect()->route('penduduk.index', $this->getFilterRedirect($request))
                ->with('success', "Alamat keluarga berhasil diperbarui lewat tabel Kartu Keluarga!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update alamat: ' . $e->getMessage());
        }
    }

    /**
     * Helper to preserve filters
     */
    private function getFilterRedirect(Request $request, $penduduk = null)
    {
        $filters = [];

        if (session()->has('penduduk_index_query')) {
            $filters = session('penduduk_index_query', []);
        }

        if (empty($filters) && $request->header('referer')) {
            parse_str(parse_url($request->header('referer'), PHP_URL_QUERY) ?? '', $filters);
        }

        $filters = array_filter($filters, fn($v) => $v !== null && $v !== ''); 

        if (!empty($filters) && $penduduk) {
            $visible = true;
            if (($filters['rt_id'] ?? null) && $filters['rt_id'] != $penduduk->rt_id) $visible = false;
            if (($filters['rw_id'] ?? null) && $filters['rw_id'] != $penduduk->rw_id) $visible = false;
            if (($filters['dusun_id'] ?? null) && $filters['dusun_id'] != $penduduk->dusun_id) $visible = false;
            
            if (!$visible) {
                $filters['rt_id'] = $penduduk->rt_id;
                $filters['rw_id'] = $penduduk->rw_id;
                $filters['dusun_id'] = $penduduk->dusun_id;
            }
        }

        return $filters;
    }

    /**
     * Check if NIK exists (exact match)
     */
    public function checkNIKExists(Request $request)
    {
        $nik = $request->get('nik');
        if (strlen($nik) !== 16) return response()->json(['exists' => false]);
        
        $query = Penduduk::withWilayah()->where('nik', $nik);
        if ($request->exclude_id) $query->where('id', '!=', $request->exclude_id);
        
        $penduduk = $query->first();
        return response()->json(['exists' => !!$penduduk, 'data' => $penduduk]);
    }

    /**
     * Search penduduk for autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 3) {
             return response()->json([]);
        }

        try {
            $penduduks = Penduduk::where(function($q) use ($query) {
                            $q->where('nama', 'like', "%{$query}%")
                              ->orWhere('nik', 'like', "%{$query}%");
                        })
                        ->limit(10)
                        ->get(['id', 'nama', 'nik', 'kartu_keluarga_id', 'pekerjaan']);
                        
            return response()->json($penduduks);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
