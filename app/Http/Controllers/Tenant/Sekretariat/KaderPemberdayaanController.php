<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Sekretariat\KaderPemberdayaan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class KaderPemberdayaanController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('kader_pemberdayaan.view');

        $query = KaderPemberdayaan::query()->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('bidang', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $kaders = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/KaderPemberdayaan/Index', [
            'kaders' => $kaders,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        Gate::authorize('kader_pemberdayaan.create');

        return Inertia::render('Tenant/Sekretariat/KaderPemberdayaan/Form', [
            'kader' => new KaderPemberdayaan(),
            'is_edit' => false
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('kader_pemberdayaan.create');

        $validated = $request->validate([
            'nik' => 'nullable|string|size:16|unique:kader_pemberdayaans,nik',
            'nama' => 'required|string|max:255',
            'umur' => 'required|integer|min:1|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'bidang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:aktif,tidak_aktif',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';

        KaderPemberdayaan::create($validated);

        return redirect()->route('sekretariat.kader-pemberdayaan.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Kader Pemberdayaan berhasil ditambahkan.']);
    }

    public function show(KaderPemberdayaan $kaderPemberdayaan)
    {
        Gate::authorize('kader_pemberdayaan.view');

        return Inertia::render('Tenant/Sekretariat/KaderPemberdayaan/Show', [
            'kader' => $kaderPemberdayaan,
        ]);
    }

    public function edit(KaderPemberdayaan $kaderPemberdayaan)
    {
        Gate::authorize('kader_pemberdayaan.edit');

        return Inertia::render('Tenant/Sekretariat/KaderPemberdayaan/Form', [
            'kader' => $kaderPemberdayaan,
            'is_edit' => true
        ]);
    }

    public function update(Request $request, KaderPemberdayaan $kaderPemberdayaan)
    {
        Gate::authorize('kader_pemberdayaan.edit');

        $validated = $request->validate([
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('kader_pemberdayaans', 'nik')->ignore($kaderPemberdayaan->id)],
            'nama' => 'required|string|max:255',
            'umur' => 'required|integer|min:1|max:150',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'bidang' => 'required|string|max:255',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'status' => 'nullable|in:aktif,tidak_aktif',
        ]);

        $kaderPemberdayaan->update($validated);

        return redirect()->route('sekretariat.kader-pemberdayaan.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Kader Pemberdayaan berhasil diperbarui.']);
    }

    public function destroy(KaderPemberdayaan $kaderPemberdayaan)
    {
        Gate::authorize('kader_pemberdayaan.delete');

        $kaderPemberdayaan->delete();

        return redirect()->route('sekretariat.kader-pemberdayaan.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Kader Pemberdayaan berhasil dihapus.']);
    }

    /**
     * API endpoint: cek apakah NIK sudah terdaftar di tabel kader
     */
    public function checkNik(Request $request)
    {
        $nik = $request->query('nik');
        $ignoreId = $request->query('ignore_id');

        $query = KaderPemberdayaan::where('nik', $nik);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();
        $kader = $exists ? $query->first(['nama', 'bidang', 'status']) : null;

        return response()->json([
            'exists' => $exists,
            'kader' => $kader,
        ]);
    }
}
