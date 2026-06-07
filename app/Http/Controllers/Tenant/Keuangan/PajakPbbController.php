<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\PajakPbbObjek;
use App\Jobs\SyncPbbMapagbumiJob;

class PajakPbbController extends Controller
{
    public function index(Request $request)
    {
        $query = PajakPbbObjek::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nop', 'like', "%{$search}%")
                  ->orWhere('nama_wp', 'like', "%{$search}%");
            });
        }

        if ($request->has('status_sync') && $request->status_sync != '') {
            if ($request->status_sync === 'sudah') {
                $query->whereNotNull('last_synced_at');
            } else if ($request->status_sync === 'belum') {
                $query->whereNull('last_synced_at');
            }
        }

        $objeks = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        $stats = \App\Models\PajakPbbTagihan::selectRaw('tahun, 
                SUM(pbb_terhutang) as total_potensi, 
                SUM(CASE WHEN status = "LUNAS" THEN pbb_terhutang ELSE 0 END) as total_realisasi
            ')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->take(4)
            ->get();

        return Inertia::render('Tenant/PajakPbb/Index', [
            'objeks' => $objeks,
            'stats' => $stats,
            'filters' => $request->only('search')
        ]);
    }

    public function show($id)
    {
        $objek = PajakPbbObjek::with(['tagihans' => function($q) {
            $q->orderBy('tahun', 'desc');
        }])->findOrFail($id);

        return Inertia::render('Tenant/PajakPbb/Show', [
            'objek' => $objek
        ]);
    }

    public function sync($id)
    {
        $objek = PajakPbbObjek::findOrFail($id);
        
        // Eksekusi secara instan (synchronous) agar user bisa langsung lihat hasil
        \App\Jobs\SyncPbbMapagbumiJob::dispatchSync($objek, 0);

        return redirect()->back()->with('success', 'Data PBB untuk NOP '.$objek->nop.' berhasil disinkronisasi dengan Mapagbumi.');
    }

    public function searchNop(Request $request)
    {
        $nop = $request->query('nop');
        if (!$nop) {
            return response()->json(['error' => 'NOP wajib diisi'], 400);
        }

        // Cari berdasarkan NOP persis atau mirip
        $objek = PajakPbbObjek::where('nop', $nop)->first();
        
        if (!$objek) {
            return response()->json(['error' => 'Data PBB tidak ditemukan dengan NOP tersebut'], 404);
        }

        return response()->json($objek);
    }

    public function destroy($id)
    {
        $objek = PajakPbbObjek::findOrFail($id);
        
        // Delete tagihans first (if no cascade on migration)
        $objek->tagihans()->delete();
        $objek->delete();

        return redirect()->route('pajak-pbb.index')->with('success', 'Data Objek PBB beserta histori tagihannya berhasil dihapus.');
    }
}
