<?php

namespace App\Http\Controllers\ApiAdminPanel;

use App\Models\Dusun;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\WilayahChangeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\JsonResponse;

class WilayahController extends Controller
{
    /**
     * List all Dusun, RW, and RT for dashboard/settings.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $dusuns = Dusun::orderBy('nama')->get();
        $rws = Rw::orderBy('kode')->get();
        $rts = Rt::with(['rw', 'dusun'])->orderBy('kode')->get();

        $pendudukCounts = Penduduk::query()
            ->selectRaw('rw_id, rt_id, COUNT(*) as total')
            ->whereNotNull('rt_id')
            ->groupBy('rw_id', 'rt_id')
            ->get()
            ->keyBy(fn ($row) => (string) $row->rw_id . '|' . (string) $row->rt_id);

        foreach ($rts as $rt) {
            $key = (string) $rt->rw_id . '|' . (string) $rt->id;
            $rt->penduduk_count = (int) ($pendudukCounts[$key]->total ?? 0);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'dusuns' => $dusuns,
                'rws' => $rws,
                'rts' => $rts,
                'summary' => [
                    'total_dusun' => $dusuns->count(),
                    'total_rw' => $rws->count(),
                    'total_rt' => $rts->count(),
                    'penduduk_terpetakan' => (int) $rts->sum('penduduk_count'),
                ]
            ]
        ]);
    }

    /**
     * Get tree structure for dropdowns.
     */
    public function getTree(): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $dusuns = Dusun::orderBy('nama')->get()->map(function($dusun) {
            $rts = Rt::with('rw')->where('dusun_id', $dusun->id)->get();
            
            $rws = $rts->groupBy('rw_id')->map(function($items, $rwId) {
                $rw = $items->first()->rw;
                if (!$rw) return null;
                return [
                    'id' => $rw->id,
                    'kode' => $rw->kode,
                    'nama' => $rw->nama,
                    'rts' => $items->map(function($rt) {
                        return [
                            'id' => $rt->id,
                            'kode' => $rt->kode,
                            'nama' => $rt->nama,
                        ];
                    })->values()
                ];
            })->filter()->values();

            return [
                'id' => $dusun->id,
                'nama' => $dusun->nama,
                'rws' => $rws
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $dusuns
        ]);
    }

    public function storeDusun(Request $request): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $data = $request->validate([
            'nama' => 'required|string|max:100|unique:dusuns,nama',
            'kode' => 'nullable|string|max:20|unique:dusuns,kode',
        ]);

        $dusun = Dusun::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Dusun berhasil ditambahkan',
            'data' => $dusun
        ], 201);
    }

    public function storeRw(Request $request): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $data = $request->validate([
            'kode' => 'required|string|max:3|unique:rws,kode',
            'nama' => 'nullable|string|max:100',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        $rw = Rw::create([
            'kode' => $kode,
            'nama' => $data['nama'] ?? "RW {$kode}",
            'is_active' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'RW berhasil ditambahkan',
            'data' => $rw
        ], 201);
    }

    public function storeRt(Request $request): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $data = $request->validate([
            'kode' => 'required|string|max:3',
            'rw_id' => 'required|exists:rws,id',
            'dusun_id' => 'nullable|exists:dusuns,id',
            'nama' => 'nullable|string|max:100',
        ]);

        $kode = str_pad(preg_replace('/[^0-9]/', '', $data['kode']), 3, '0', STR_PAD_LEFT);

        $rt = Rt::updateOrCreate(
            ['kode' => $kode, 'rw_id' => $data['rw_id']],
            [
                'dusun_id' => $data['dusun_id'] ?? null,
                'nama' => $data['nama'] ?? "RT {$kode}",
                'is_active' => true
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'RT berhasil ditambahkan/diperbarui',
            'data' => $rt
        ]);
    }

    public function destroyRt(Rt $rt): JsonResponse
    {
        Gate::authorize('wilayah.view');

        $rtKode = $rt->kode;
        $rwKode = optional($rt->rw)->kode;

        $usedCount = Penduduk::where('rt_id', $rt->id)->count();

        if ($usedCount > 0) {
            return response()->json([
                'status' => 'error',
                'message' => "RT {$rtKode}/RW {$rwKode} tidak bisa dihapus karena masih dipakai {$usedCount} penduduk."
            ], 422);
        }

        $rt->delete();

        return response()->json([
            'status' => 'success',
            'message' => "RT {$rtKode}/RW {$rwKode} berhasil dihapus."
        ]);
    }
}
