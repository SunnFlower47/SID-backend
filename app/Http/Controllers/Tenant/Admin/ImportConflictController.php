<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ImportConflict;
use App\Models\Rw;
use App\Models\Rt;
use App\Services\Kependudukan\ImportConflictService;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ImportConflictController extends Controller
{
    protected $importConflictService;

    public function __construct(ImportConflictService $importConflictService)
    {
        $this->middleware('auth');
        $this->middleware('can:admin_sistem');
        $this->importConflictService = $importConflictService;
    }

    /**
     * Display the list of conflicts
     */
    public function importConflicts(Request $request)
    {
        Gate::authorize('admin_sistem');

        return Inertia::render('Tenant/ImportConflict/Index', [
            'conflicts' => Inertia::defer(function() use ($request) {
                return $this->importConflictService->getConflicts($request->all());
            }),
            'stats' => $this->importConflictService->getConflictStats(),
            'rws' => Rw::with('rts')->orderBy('kode')->get(),
            'filters' => $request->only(['status', 'batch_id', 'issue_type'])
        ]);
    }

    /**
     * Resolve individual conflict
     */
    public function resolveImportConflict(Request $request, ImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        $data = $request->validate([
            'action' => 'required|in:use_existing,create_override,skip,fix_fields,keep_existing_nik,update_existing_from_incoming,change_incoming_nik',
            'rw_id' => 'nullable|exists:rws,id',
            'rt_id' => 'nullable|exists:rts,id',
            'nik_new' => 'nullable|string|max:20',
            'nama_new' => 'nullable|string|max:255',
            'nkk_new' => 'nullable|string|max:20',
            'alamat_new' => 'nullable|string|max:500',
            'dusun_new' => 'nullable|string|max:100',
            'rw_new' => 'nullable|string|max:10',
            'rt_new' => 'nullable|string|max:10',
        ]);

        // Strict validation for NIK and NKK if provided
        if ($request->filled('nik_new')) {
            $nikClean = preg_replace('/\D+/', '', $data['nik_new']);
            if (strlen($nikClean) !== 16) {
                return back()->with('error', 'Format NIK salah! Harus tepat 16 digit angka.');
            }
        }
        if ($request->filled('nkk_new')) {
            $nkkClean = preg_replace('/\D+/', '', $data['nkk_new']);
            if (strlen($nkkClean) !== 16) {
                return back()->with('error', 'Format No. KK salah! Harus tepat 16 digit angka.');
            }
        }

        try {
            $res = $this->importConflictService->resolveConflict($conflict, $data, optional($request->user())->id);

            if ($res['success']) {
                return back()->with('success', $res['message']);
            } else {
                return back()->with('error', $res['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reset resolved conflict back to pending
     */
    public function resetImportConflict(Request $request, ImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        try {
            $this->importConflictService->resetConflict($conflict);
            return back()->with('success', 'Status issue berhasil di-reset menjadi Pending.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reprocess individual resolved conflict manually
     */
    public function reprocessImportIssue(Request $request, ImportConflict $conflict)
    {
        Gate::authorize('admin_sistem');

        try {
            $this->importConflictService->reprocessConflict($conflict);
            return back()->with('success', 'Reprocess issue berhasil dijalankan.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Reprocess gagal: ' . $e->getMessage());
        }
    }
}