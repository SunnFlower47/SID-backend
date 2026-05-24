<?php

namespace App\Http\Controllers\Tenant\Konten;

use App\Http\Controllers\Controller;
use App\Models\MasterJabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MasterJabatanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:surat.view']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Tenant/MasterJabatan/Index', [
            'jabatans' => MasterJabatan::orderBy('urutan')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'is_struktur' => 'boolean',
            'is_kontak' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MasterJabatan::create($request->all());

        return redirect()->back()->with('success', 'Jabatan baru berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterJabatan $masterJabatan)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'is_struktur' => 'boolean',
            'is_kontak' => 'boolean',
            'urutan' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $masterJabatan->update($request->all());

        return redirect()->back()->with('success', 'Data jabatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterJabatan $masterJabatan)
    {
        // Check if used in StrukturDesa or KontakDesa (optional, but good for safety)
        // For now, let's just delete
        $masterJabatan->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }

    /**
     * Reorder jabatans
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:master_jabatans,id'
        ]);

        foreach ($request->ids as $index => $id) {
            MasterJabatan::where('id', $id)->update(['urutan' => $index + 1]);
        }

        return redirect()->back()->with('success', 'Urutan jabatan berhasil diperbarui.');
    }
}
