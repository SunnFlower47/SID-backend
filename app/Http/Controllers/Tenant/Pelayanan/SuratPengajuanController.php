<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Models\SuratPengajuan;
use App\Models\SuratType;
use App\Services\Pelayanan\SuratPengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SuratPengajuanController extends Controller
{
    public function __construct(protected SuratPengajuanService $suratService)
    {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Daftar pengajuan surat dengan filter.
     */
    public function index(Request $request)
    {
        $pengajuans = $this->suratService
            ->getFilteredQuery($request)
            ->paginate(20);

        return Inertia::render('Tenant/SuratPengajuan/Index', [
            'pengajuans' => $pengajuans,
            'statusList' => SuratPengajuan::STATUS_LIST,
            'suratTypes' => SuratType::where('is_active', true)->get(),
            'filters'    => $request->only(['status', 'jenis_surat', 'search']),
        ]);
    }

    /**
     * Form tambah pengajuan baru.
     */
    public function create()
    {
        return Inertia::render('Tenant/SuratPengajuan/Create', [
            'suratTypes' => SuratType::where('is_active', true)->orderBy('nama')->get(),
            'wilayah'    => [
                'dusun' => \App\Models\Dusun::all(),
                'rw'    => \App\Models\Rw::all(),
                'rt'    => \App\Models\Rt::all(),
            ],
        ]);
    }

    /**
     * Simpan pengajuan baru via Action class.
     */
    public function store(Request $request)
    {
        Gate::authorize('surat.view');

        $validated = $request->validate([
            'jenis_surat'         => 'required|string',
            'penduduk_id'         => 'nullable|exists:penduduks,id',
            'keperluan'           => 'nullable|string|max:500',
            'tujuan'              => 'nullable|string|max:255',
            'tanggal_surat'       => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan'       => 'nullable|array',
            'penandatangan'       => 'nullable|in:kepala_desa,sekretaris_desa',
        ]);

        try {
            $result = app(\App\Actions\Surat\StoreSuratAction::class)->execute($validated);
            return redirect()->route('admin.surat-pengajuan.index')
                ->with($result['type'], $result['message']);
        } catch (\Exception $e) {
            Log::error('Error storing surat pengajuan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Detail pengajuan surat.
     */
    public function show(SuratPengajuan $suratPengajuan)
    {
        $suratPengajuan->load(['penduduk', 'admin']);

        return Inertia::render('Tenant/SuratPengajuan/Show', [
            'suratPengajuan' => $suratPengajuan,
            'statusList'     => SuratPengajuan::STATUS_LIST,
        ]);
    }

    /**
     * Form edit pengajuan.
     */
    public function edit(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');
        $suratPengajuan->load('penduduk');

        // Flatten data_tambahan agar form frontend bisa membaca data bersarang (contoh: kematian.hari menjadi kematian_hari)
        $dataTambahan = $suratPengajuan->data_tambahan;
        if (is_array($dataTambahan) && !empty($dataTambahan)) {
            $flattened = \Illuminate\Support\Arr::dot($dataTambahan);
            $newData = $dataTambahan; // keep original just in case
            foreach ($flattened as $key => $value) {
                $newKey = str_replace('.', '_', $key);
                $newData[$newKey] = $value;
            }
            $suratPengajuan->data_tambahan = $newData;
        }

        return Inertia::render('Tenant/SuratPengajuan/Edit', [
            'suratPengajuan' => $suratPengajuan,
            'suratTypes'     => SuratType::where('is_active', true)->orderBy('nama')->get(),
            'wilayah'        => [
                'dusun' => \App\Models\Dusun::all(),
                'rw'    => \App\Models\Rw::all(),
                'rt'    => \App\Models\Rt::all(),
            ],
        ]);
    }

    /**
     * Update pengajuan via Action class.
     */
    public function update(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        $validated = $request->validate([
            'jenis_surat'         => 'required|string',
            'penduduk_id'         => 'nullable|exists:penduduks,id',
            'keperluan'           => 'nullable|string|max:500',
            'tujuan'              => 'nullable|string|max:255',
            'tanggal_surat'       => 'required|date',
            'keterangan_tambahan' => 'nullable|string|max:1000',
            'data_tambahan'       => 'nullable|array',
            'penandatangan'       => 'nullable|in:kepala_desa,sekretaris_desa',
        ]);

        try {
            $result = app(\App\Actions\Surat\UpdateSuratAction::class)->execute($suratPengajuan, $validated);
            return redirect()->route('admin.surat-pengajuan.index')
                ->with($result['type'], $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update status pengajuan (approve, proses, tolak, selesai).
     */
    public function updateStatus(Request $request, SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        $validated = $request->validate([
            'status'              => 'required|in:pending,diproses,selesai,ditolak',
            'keterangan_tambahan' => 'nullable|string|max:1000',
        ]);

        try {
            $this->suratService->updateStatus($suratPengajuan, $validated);
            return redirect()->back()->with('success', 'Status pengajuan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }

    /**
     * Generate dan unduh dokumen surat (Word/PDF).
     */
    public function generatePdf(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        try {
            return $this->suratService->generateDocument($suratPengajuan);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error generating surat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat surat: ' . $e->getMessage());
        }
    }

    /**
     * Preview surat via Blade view.
     */
    public function preview(SuratPengajuan $suratPengajuan)
    {
        Gate::authorize('surat.view');

        try {
            $data         = $this->suratService->buildSuratData($suratPengajuan);
            $templateName = $this->suratService->resolveTemplateName($suratPengajuan->jenis_surat);

            return view("surat.templates.{$templateName}", $data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat preview: ' . $e->getMessage());
        }
    }

    /**
     * Pencarian penduduk untuk autocomplete di form surat.
     */
    public function searchPenduduk(Request $request)
    {
        $search = $request->get('q');

        if (!$search || strlen($search) < 3) {
            return response()->json([]);
        }

        try {
            $results = Penduduk::withWilayah()
                ->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get()
                ->map(fn ($p) => [
                    'id'               => $p->id,
                    'nik'              => $p->nik,
                    'nama'             => $p->nama,
                    'alamat'           => $p->alamat,
                    'rt'               => $p->rt_label     ?? '-',
                    'rw'               => $p->rw_label     ?? '-',
                    'dusun'            => $p->dusun_label  ?? '-',
                    'jenis_kelamin'    => $p->jenis_kelamin,
                    'tempat_lahir'     => $p->tempat_lahir,
                    'tanggal_lahir'    => $p->tanggal_lahir?->toDateString(),
                    'agama'            => $p->agama,
                    'pekerjaan'        => $p->pekerjaan,
                    'status_perkawinan'=> $p->status_perkawinan,
                ]);

            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus pengajuan surat.
     */
    public function destroy($id)
    {
        Gate::authorize('surat.view');

        try {
            SuratPengajuan::findOrFail($id)->delete();
            return redirect()->route('admin.surat-pengajuan.index')
                ->with('success', 'Pengajuan surat berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
