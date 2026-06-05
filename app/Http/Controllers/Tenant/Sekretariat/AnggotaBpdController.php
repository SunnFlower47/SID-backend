<?php

namespace App\Http\Controllers\Tenant\Sekretariat;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Sekretariat\AnggotaBpd;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AnggotaBpdController extends Controller
{
    public function index(Request $request)
    {
        $query = AnggotaBpd::query()->orderBy('created_at', 'desc');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('jabatan', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $anggotas = $query->paginate(15)->withQueryString();

        return Inertia::render('Tenant/Sekretariat/AnggotaBPD/Index', [
            'anggotas' => $anggotas,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Tenant/Sekretariat/AnggotaBPD/Form', [
            'anggota' => new AnggotaBpd(),
            'is_edit' => false
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'nullable|string|size:16|unique:anggota_bpds,nik',
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_keputusan_pengangkatan' => 'nullable|string|max:255',
            'tanggal_keputusan_pengangkatan' => 'nullable|date',
            'no_keputusan_pemberhentian' => 'nullable|string|max:255',
            'tanggal_keputusan_pemberhentian' => 'nullable|date',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'nullable|in:aktif,tidak_aktif',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';

        AnggotaBpd::create($validated);

        return redirect()->route('sekretariat.anggota-bpd.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Anggota BPD berhasil ditambahkan.']);
    }

    public function show(AnggotaBpd $anggotaBpd)
    {
        return Inertia::render('Tenant/Sekretariat/AnggotaBPD/Show', [
            'anggota' => $anggotaBpd,
        ]);
    }

    public function edit(AnggotaBpd $anggotaBpd)
    {
        return Inertia::render('Tenant/Sekretariat/AnggotaBPD/Form', [
            'anggota' => $anggotaBpd,
            'is_edit' => true
        ]);
    }

    public function update(Request $request, AnggotaBpd $anggotaBpd)
    {
        $validated = $request->validate([
            'nik' => ['nullable', 'string', 'size:16', Rule::unique('anggota_bpds', 'nik')->ignore($anggotaBpd->id)],
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'no_keputusan_pengangkatan' => 'nullable|string|max:255',
            'tanggal_keputusan_pengangkatan' => 'nullable|date',
            'no_keputusan_pemberhentian' => 'nullable|string|max:255',
            'tanggal_keputusan_pemberhentian' => 'nullable|date',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'dusun' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'nullable|in:aktif,tidak_aktif',
            'keterangan' => 'nullable|string',
            'foto' => 'nullable|string',
        ]);

        $anggotaBpd->update($validated);

        return redirect()->route('sekretariat.anggota-bpd.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Anggota BPD berhasil diperbarui.']);
    }

    public function destroy(AnggotaBpd $anggotaBpd)
    {
        $anggotaBpd->delete();

        return redirect()->route('sekretariat.anggota-bpd.index')
            ->with('message', ['type' => 'success', 'text' => 'Data Anggota BPD berhasil dihapus.']);
    }

    public function checkNik(Request $request)
    {
        $nik = $request->query('nik');
        $ignoreId = $request->query('ignore_id');

        $query = AnggotaBpd::where('nik', $nik);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $exists = $query->exists();
        $anggota = $exists ? $query->first(['nama', 'jabatan', 'status']) : null;

        return response()->json([
            'exists' => $exists,
            'anggota' => $anggota,
        ]);
    }
}
