<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\PeraturanDesa;
use App\Http\Requests\Sekretariat\PeraturanDesaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class PeraturanDesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('peraturan_desa.view');

        $query = PeraturanDesa::query()->orderBy('tahun_anggaran', 'desc')->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_peraturan', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('jenis') && $request->jenis != '') {
            $query->where('jenis_peraturan', $request->jenis);
        }

        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun_anggaran', $request->tahun);
        }

        $peraturans = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/PeraturanDesa/Index', [
            'peraturans' => $peraturans,
            'filters'    => $request->only(['search', 'jenis', 'tahun']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('peraturan_desa.create');

        return Inertia::render('Tenant/Sekretariat/PeraturanDesa/Form', [
            'peraturan' => new PeraturanDesa([
                'status' => 'disetujui',
                'tahun_anggaran' => date('Y')
            ]),
            'is_edit' => false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PeraturanDesaRequest $request)
    {
        Gate::authorize('peraturan_desa.create');

        $data = $request->validated();

        if ($request->hasFile('file_dokumen')) {
            $file = $request->file('file_dokumen');
            $filename = 'PERDES_' . str_replace(' ', '_', $data['jenis_peraturan']) . '_' . $data['tahun_anggaran'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['file_dokumen'] = $file->storeAs('sekretariat/peraturan_desa', $filename, 'public');
        }

        PeraturanDesa::create($data);

        return redirect()->route('sekretariat.peraturan-desa.index')
            ->with('success', 'Peraturan Desa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Gate::authorize('peraturan_desa.edit');

        $peraturan = PeraturanDesa::findOrFail($id);

        return Inertia::render('Tenant/Sekretariat/PeraturanDesa/Form', [
            'peraturan' => $peraturan,
            'is_edit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PeraturanDesaRequest $request, $id)
    {
        Gate::authorize('peraturan_desa.edit');

        $peraturan = PeraturanDesa::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('file_dokumen')) {
            // Hapus file lama jika ada
            if ($peraturan->file_dokumen && Storage::disk('public')->exists($peraturan->file_dokumen)) {
                Storage::disk('public')->delete($peraturan->file_dokumen);
            }
            $file = $request->file('file_dokumen');
            $filename = 'PERDES_' . str_replace(' ', '_', $data['jenis_peraturan']) . '_' . $data['tahun_anggaran'] . '_' . time() . '.' . $file->getClientOriginalExtension();
            $data['file_dokumen'] = $file->storeAs('sekretariat/peraturan_desa', $filename, 'public');
        }

        $peraturan->update($data);

        return redirect()->route('sekretariat.peraturan-desa.index')
            ->with('success', 'Peraturan Desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Gate::authorize('peraturan_desa.delete');

        $peraturan = PeraturanDesa::findOrFail($id);
        
        if ($peraturan->file_dokumen && Storage::disk('public')->exists($peraturan->file_dokumen)) {
            Storage::disk('public')->delete($peraturan->file_dokumen);
        }

        $peraturan->delete();

        return redirect()->route('sekretariat.peraturan-desa.index')
            ->with('success', 'Peraturan Desa berhasil dihapus.');
    }
}
