<?php

namespace App\Http\Controllers\Tenant\Kependudukan;

use App\Http\Controllers\Controller;

use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

class TrashPendudukController extends Controller
{
    /**
     * Display a listing of residents that are soft-deleted but have no mutation record.
     */
    public function index(Request $request)
    {
        Gate::authorize('settings.view');

        $query = Penduduk::onlyTrashed()
            ->doesntHave('mutasis')
            ->withWilayah();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('kartuKeluarga', function($sq) use ($search) {
                      $sq->where('nkk', 'like', "%{$search}%");
                  });
            });
        }

        return \Inertia\Inertia::render('Tenant/Trash/Penduduk/Index', [
            'penduduks' => \Inertia\Inertia::defer(fn() => $query->latest('deleted_at')->paginate(10)->withQueryString()),
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Restore a soft-deleted resident.
     */
    public function restore($id)
    {
        Gate::authorize('settings.view');

        $penduduk = Penduduk::onlyTrashed()->findOrFail($id);
        $penduduk->restore();

        return back()->with('success', "Data penduduk {$penduduk->nama} berhasil dipulihkan.");
    }

    /**
     * Permanently delete a soft-deleted resident.
     */
    public function forceDelete($id)
    {
        Gate::authorize('settings.view');

        $penduduk = Penduduk::onlyTrashed()->findOrFail($id);
        $nama = $penduduk->nama;
        
        DB::beginTransaction();
        try {
            $penduduk->forceDelete();
            DB::commit();

            return back()->with('success', "Data penduduk {$nama} telah dihapus permanen dari sistem.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal menghapus data: " . $e->getMessage());
        }
    }
}
