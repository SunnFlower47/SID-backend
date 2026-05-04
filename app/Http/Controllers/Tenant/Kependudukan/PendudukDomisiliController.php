<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendudukDomisiliRequest;
use App\Models\PendudukDomisili;
use App\Services\PendudukDomisiliService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PendudukDomisiliController extends Controller
{
    public function __construct(private PendudukDomisiliService $service)
    {
        $this->middleware(['auth', 'can:kependudukan']);
    }

    /**
     * Display a listing of the resource with summary cards and filters.
     */
    public function index(Request $request)
    {
        $query = PendudukDomisili::with(['rt', 'rw', 'dusun'])
            ->filter($request->all())
            ->orderBy('updated_at', 'desc');

        $domisilis = Inertia::defer(fn() => $query->paginate(20)->withQueryString());

        // Summary Cards
        $stats = Inertia::defer(fn() => [
            'total_aktif'         => PendudukDomisili::aktif()->count(),
            'expired_bulan_ini'   => PendudukDomisili::expired()
                                        ->whereMonth('tanggal_berlaku', now()->month)
                                        ->whereYear('tanggal_berlaku', now()->year)
                                        ->count(),
            'baru_masuk_bulan_ini' => PendudukDomisili::whereMonth('tanggal_masuk', now()->month)
                                        ->whereYear('tanggal_masuk', now()->year)
                                        ->count(),
            'warning_expired'     => PendudukDomisili::warningExpiry(30)->count(),
        ]);

        // Master data untuk filter
        $rtList    = \App\Models\Rt::orderBy('kode')->get(['id', 'kode']);
        $rwList    = \App\Models\Rw::orderBy('kode')->get(['id', 'kode']);
        $dusunList = \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']);

        return Inertia::render('Tenant/Domisili/Index', [
            'domisilis' => $domisilis,
            'stats'     => $stats,
            'filters'   => $request->all(['search', 'status', 'rt_id', 'rw_id', 'dusun_id', 'asal_daerah', 'keperluan_domisili']),
            'rtList'    => $rtList,
            'rwList'    => $rwList,
            'dusunList' => $dusunList,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Tenant/Domisili/Form', [
            'domisili'   => null,
            'rtList'     => \App\Models\Rt::with('rw')->orderBy('kode')->get(['id', 'kode', 'rw_id', 'dusun_id']),
            'rwList'     => \App\Models\Rw::orderBy('kode')->get(['id', 'kode']),
            'dusunList'  => \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendudukDomisiliRequest $request)
    {
        try {
            // Resolve dusun_id dari rt jika tidak diisi
            if (empty($request->dusun_id) && $request->rt_id) {
                $rt = \App\Models\Rt::find($request->rt_id);
                $data = array_merge($request->validated(), ['dusun_id' => optional($rt)->dusun_id]);
            } else {
                $data = $request->validated();
            }

            $this->service->create($data);

            return redirect()->route('domisili.index')
                ->with('success', 'Data penduduk domisili berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PendudukDomisili $domisili)
    {
        return Inertia::render('Tenant/Domisili/Form', [
            'domisili'  => $domisili->load(['rt', 'rw', 'dusun']),
            'rtList'    => \App\Models\Rt::with('rw')->orderBy('kode')->get(['id', 'kode', 'rw_id', 'dusun_id']),
            'rwList'    => \App\Models\Rw::orderBy('kode')->get(['id', 'kode']),
            'dusunList' => \App\Models\Dusun::orderBy('nama')->get(['id', 'nama']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePendudukDomisiliRequest $request, PendudukDomisili $domisili)
    {
        try {
            $data = $request->validated();
            if (empty($data['dusun_id']) && !empty($data['rt_id'])) {
                $rt = \App\Models\Rt::find($data['rt_id']);
                $data['dusun_id'] = optional($rt)->dusun_id;
            }
            $this->service->update($domisili, $data);

            return redirect()->route('domisili.index')
                ->with('success', 'Data penduduk domisili berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Perpanjang domisili +3 bulan.
     */
    public function perpanjang(PendudukDomisili $domisili)
    {
        try {
            $this->service->perpanjang($domisili);
            return redirect()->route('domisili.index')
                ->with('success', "Domisili atas nama {$domisili->nama} berhasil diperpanjang 3 bulan. Nomor surat baru telah digenerate.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cabut domisili secara manual.
     */
    public function cabut(Request $request, PendudukDomisili $domisili)
    {
        $request->validate([
            'alasan' => 'required|string|min:10|max:500',
        ], [
            'alasan.required' => 'Alasan pencabutan wajib diisi.',
            'alasan.min'      => 'Alasan pencabutan minimal 10 karakter.',
        ]);

        try {
            $this->service->cabut($domisili, $request->alasan);
            return redirect()->route('domisili.index')
                ->with('success', "Domisili atas nama {$domisili->nama} berhasil dicabut.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Soft delete.
     */
    public function destroy(PendudukDomisili $domisili)
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Hapus surat pengajuan terkait jika ada
            if ($domisili->surat_pengajuan_id) {
                $surat = \App\Models\SuratPengajuan::find($domisili->surat_pengajuan_id);
                if ($surat) {
                    $surat->delete();
                }
            }

            $domisili->delete();

            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('domisili.index')
                ->with('success', 'Data domisili dan surat terkait berhasil dihapus.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Check NIK availability (AJAX).
     */
    public function checkNik(Request $request)
    {
        $nik = $request->get('nik');
        if (strlen($nik) !== 16) {
            return response()->json(['status' => 'invalid', 'message' => 'NIK harus 16 digit.']);
        }

        $isPermanent = \App\Models\Penduduk::where('nik', $nik)->exists();
        if ($isPermanent) {
            return response()->json([
                'status'  => 'blocked',
                'message' => 'NIK ini terdaftar sebagai penduduk TETAP. Tidak dapat didaftarkan sebagai domisili.',
            ]);
        }

        $isActiveDomisili = PendudukDomisili::where('nik', $nik)->where('status', 'aktif')->exists();
        if ($isActiveDomisili) {
            return response()->json([
                'status'  => 'duplicate',
                'message' => 'NIK ini sudah memiliki data domisili AKTIF.',
            ]);
        }

        return response()->json(['status' => 'available', 'message' => 'NIK tersedia.']);
    }
}
