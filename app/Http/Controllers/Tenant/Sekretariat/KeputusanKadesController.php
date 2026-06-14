<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\KeputusanKades;
use App\Http\Requests\Sekretariat\KeputusanKadesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class KeputusanKadesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('keputusan_kades.view');

        $query = KeputusanKades::query()
            ->with('author:id,name')
            ->orderBy('tanggal_ditetapkan', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nomor_keputusan', 'like', "%{$request->search}%")
                  ->orWhere('judul_keputusan', 'like', "%{$request->search}%");
            });
        }

        $keputusan_kades = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/KeputusanKades/Index', [
            'keputusan_kades' => $keputusan_kades,
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('keputusan_kades.create');

        return Inertia::render('Tenant/Sekretariat/KeputusanKades/Form', [
            'keputusan' => new KeputusanKades(),
            'is_edit' => false
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KeputusanKadesRequest $request)
    {
        Gate::authorize('keputusan_kades.create');

        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if ($request->hasFile('file_dokumen')) {
            $data['file_dokumen'] = $request->file('file_dokumen')->store('sekretariat/keputusan_kades');
        }

        KeputusanKades::create($data);

        return redirect()->route('sekretariat.keputusan-kades.index')
            ->with('success', 'Keputusan Kepala Desa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        Gate::authorize('keputusan_kades.edit');

        $keputusan = KeputusanKades::findOrFail($id);

        return Inertia::render('Tenant/Sekretariat/KeputusanKades/Form', [
            'keputusan' => $keputusan,
            'is_edit' => true
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KeputusanKadesRequest $request, $id)
    {
        Gate::authorize('keputusan_kades.edit');

        $keputusan = KeputusanKades::findOrFail($id);
        $data = $request->validated();

        if ($request->hasFile('file_dokumen')) {
            // Delete old file if exists
            if ($keputusan->file_dokumen && Storage::disk('s3')->exists($keputusan->file_dokumen)) {
                Storage::disk('s3')->delete($keputusan->file_dokumen);
            }
            $data['file_dokumen'] = $request->file('file_dokumen')->store('sekretariat/keputusan_kades');
        }

        $keputusan->update($data);

        return redirect()->route('sekretariat.keputusan-kades.index')
            ->with('success', 'Keputusan Kepala Desa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Gate::authorize('keputusan_kades.delete');

        $keputusan = KeputusanKades::findOrFail($id);
        
        // Soft delete will keep the file, if we want to physically delete the file we can do it here,
        // but typically soft delete keeps files. We will just delete the record.
        $keputusan->delete();

        return redirect()->route('sekretariat.keputusan-kades.index')
            ->with('success', 'Keputusan Kepala Desa berhasil dihapus.');
    }
}
