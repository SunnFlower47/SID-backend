<?php

namespace App\Http\Controllers;

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

class PendudukController extends Controller
{
    protected $pendudukService;

    public function __construct(PendudukService $pendudukService)
    {
        $this->middleware(['auth', 'can:penduduk.view']);
        $this->pendudukService = $pendudukService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Simpan query terakhir dari halaman index agar filter + page tidak hilang saat kembali dari CRUD
        session(['penduduk_index_query' => $request->query()]);

        $query = Penduduk::filter($request->all())
            ->orderByFamilyRole();

        $penduduks = $query->paginate(50);
        
        // Statistics
        $stats = [
            'total' => Penduduk::whereNull('deleted_at')->count(),
            'laki_laki' => Penduduk::where('jenis_kelamin', 'LAKI-LAKI')->whereNull('deleted_at')->count(),
            'perempuan' => Penduduk::where('jenis_kelamin', 'PEREMPUAN')->whereNull('deleted_at')->count(),
            'total_kk' => Penduduk::whereNull('deleted_at')->select('nkk')->distinct()->count(),
        ];

        // Filter Lists
        $rtList = Penduduk::select('rt')->distinct()->whereNotNull('rt')->orderBy('rt')->pluck('rt');
        $rwList = Penduduk::select('rw')->distinct()->whereNotNull('rw')->orderBy('rw')->pluck('rw');
        $dusunList = Penduduk::select('dusun')->distinct()->whereNotNull('dusun')->orderBy('dusun')->pluck('dusun');

        return view('penduduk.index', compact('penduduks', 'stats', 'rtList', 'rwList', 'dusunList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('penduduk.create');

        $kepalaKeluargaData = Penduduk::where('kedudukan_keluarga', 'Kepala Keluarga')
            ->select('nkk', 'nama', 'alamat', 'rt', 'rw')
            ->orderBy('nkk')
            ->get()
            ->keyBy('nkk');

        $allNKKs = Penduduk::select('nkk')->distinct()->whereNotNull('nkk')->orderBy('nkk')->pluck('nkk');

        $existingNKKs = $allNKKs->map(function($nkk) use ($kepalaKeluargaData) {
            $kk = $kepalaKeluargaData->get($nkk);
            return [
                'nkk' => $nkk,
                'kepala_keluarga' => $kk ? $kk->nama : 'Tidak ada kepala keluarga',
                'alamat' => $kk ? $kk->alamat : '-',
                'rt' => $kk ? $kk->rt : '-',
                'rw' => $kk ? $kk->rw : '-',
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
                        'dusun' => optional($rt->dusun)->nama,
                    ];
                })->values(),
            ];
        })->values();

        return view('penduduk.create', compact('existingNKKs', 'rws', 'masterRwOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendudukRequest $request)
    {
        Gate::authorize('penduduk.create');

        try {
            $validated = $request->validated();
            if (!empty($validated['rt_id'])) {
                $rtModel = Rt::with(['rw', 'dusun'])->find($validated['rt_id']);
                if ($rtModel && $rtModel->rw) {
                    $validated['rt'] = $rtModel->kode;
                    $validated['rw'] = $rtModel->rw->kode;
                    $validated['dusun'] = optional($rtModel->dusun)->nama;
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
        $penduduk->load('mutasi');
        return view('penduduk.show', compact('penduduk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penduduk $penduduk)
    {
        Gate::authorize('penduduk.edit');
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
        return view('penduduk.edit', compact('penduduk', 'rws', 'masterRwOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePendudukRequest $request, Penduduk $penduduk)
    {
        Gate::authorize('penduduk.edit');

        try {
            $validated = $request->validated();
            if (!empty($validated['rt_id'])) {
                $rtModel = Rt::with(['rw', 'dusun'])->find($validated['rt_id']);
                if ($rtModel && $rtModel->rw) {
                    $validated['rt'] = $rtModel->kode;
                    $validated['rw'] = $rtModel->rw->kode;
                    $validated['dusun'] = optional($rtModel->dusun)->nama;
                }
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
        Gate::authorize('penduduk.delete');

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
        Gate::authorize('penduduk.export');
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        return Excel::download(new PendudukExport($request), 'data_penduduk_' . now()->format('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Update address for entire family
     */
    public function updateFamilyAddress(Request $request, $nkk)
    {
        Gate::authorize('penduduk.edit');

        $validated = $request->validate([
            'alamat' => 'required|string|max:500',
            'rw_id' => 'required|exists:rws,id',
            'rt_id' => 'required|exists:rts,id',
        ]);

        $rtModel = Rt::with(['rw', 'dusun'])->find($validated['rt_id']);
        if (!$rtModel || !$rtModel->rw || (int)$rtModel->rw_id !== (int)$validated['rw_id']) {
            return redirect()->back()->withInput()->with('error', 'RT tidak sesuai dengan RW yang dipilih.');
        }

        $validated['rt'] = $rtModel->kode;
        $validated['rw'] = $rtModel->rw->kode;
        $validated['dusun'] = optional($rtModel->dusun)->nama;

        try {
            $count = $this->pendudukService->updateFamilyAddress($nkk, $validated);
            return redirect()->route('penduduk.index', $this->getFilterRedirect($request))
                ->with('success', "Alamat keluarga berhasil diperbarui untuk {$count} anggota keluarga!");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update alamat: ' . $e->getMessage());
        }
    }

    /**
     * Helper to preserve filters (Refactored from original monolithic code)
     */
    private function getFilterRedirect(Request $request, $penduduk = null)
    {
        $filters = [];

        // 1) Prioritas: query yang disimpan dari kunjungan terakhir ke halaman index
        if (session()->has('penduduk_index_query')) {
            $filters = session('penduduk_index_query', []);
        }

        // 2) Fallback: ambil dari referer kalau ada query (mis. dari halaman index langsung)
        if (empty($filters) && $request->header('referer')) {
            parse_str(parse_url($request->header('referer'), PHP_URL_QUERY) ?? '', $filters);
        }

        $filters = array_filter($filters, fn($v) => $v !== null && $v !== ''); // keep page + filter valid

        if (!empty($filters) && $penduduk) {
            // Check if visible
            $visible = true;
            if (($filters['rt'] ?? null) && $filters['rt'] != $penduduk->rt) $visible = false;
            if (($filters['rw'] ?? null) && $filters['rw'] != $penduduk->rw) $visible = false;
            
            // Override if not visible
            if (!$visible) {
                $filters['rt'] = $penduduk->rt;
                $filters['rw'] = $penduduk->rw;
            }
        }

        return $filters;
    }

    /**
     * Show family address update form (Unchanged logic)
     */
    public function showFamilyAddressForm($nkk)
    {
        Gate::authorize('penduduk.edit');
        $familyMembers = Penduduk::where('nkk', $nkk)->orderBy('kedudukan_keluarga')->get();

        if ($familyMembers->isEmpty()) return redirect()->back()->with('error', 'Keluarga tidak ditemukan.');

        $kepalaKeluarga = $familyMembers->firstWhere(fn($m) => strtolower($m->kedudukan_keluarga) === 'kepala keluarga');
        $currentAddress = $kepalaKeluarga ?: $familyMembers->first();

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

        return view('penduduk.family-address', compact('familyMembers', 'kepalaKeluarga', 'currentAddress', 'nkk', 'rws', 'masterRwOptions'));
    }

    // ... create/checkNIKExists/updateKepalaKeluarga kept as is or moved if needed ...
    
    /**
     * Check if NIK exists (exact match)
     */
    public function checkNIKExists(Request $request)
    {
        $nik = $request->get('nik');
        if (strlen($nik) !== 16) return response()->json(['exists' => false]);
        
        $query = Penduduk::where('nik', $nik);
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

        $penduduks = Penduduk::withTrashed() // Enable searching soft-deleted records
                    ->where(function($q) use ($query) {
                        $q->where('nama', 'like', "%{$query}%")
                          ->orWhere('nik', 'like', "%{$query}%");
                    })
                    ->limit(10)
                    ->get(['id', 'nama', 'nik', 'alamat', 'pekerjaan', 'deleted_at']);
                    
        return response()->json($penduduks);
    }
}
