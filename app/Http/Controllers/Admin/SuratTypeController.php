<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SuratTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:settings.view']);
    }

    public function index()
    {
        $suratTypes = SuratType::orderBy('nama')->get();
        return view('admin.surat-type.index', compact('suratTypes'));
    }

    public function create()
    {
        Gate::authorize('settings.edit');
        return view('admin.surat-type.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('settings.edit');

        $validated = $request->validate([
            'id' => 'required|string|unique:surat_types,id|max:50',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'has_template' => 'boolean',
            'template_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'form_json' => 'nullable|string', // Kita terima sebagai string JSON dari textarea
        ]);

        // Convert string JSON back to array before saving
        if (!empty($validated['form_json'])) {
            $validated['form_json'] = json_decode($validated['form_json'], true);
        } else {
            $validated['form_json'] = [];
        }

        SuratType::create($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(SuratType $suratType)
    {
        Gate::authorize('settings.edit');
        // Convert array to pretty JSON for textarea
        $suratType->form_json_raw = json_encode($suratType->form_json, JSON_PRETTY_PRINT);
        return view('admin.surat-type.edit', compact('suratType'));
    }

    public function update(Request $request, SuratType $suratType)
    {
        Gate::authorize('settings.edit');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'has_template' => 'boolean',
            'template_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'form_json' => 'nullable|string',
        ]);

        if (!empty($validated['form_json'])) {
            $validated['form_json'] = json_decode($validated['form_json'], true);
        } else {
            $validated['form_json'] = [];
        }

        $suratType->update($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(SuratType $suratType)
    {
        Gate::authorize('settings.edit');
        $suratType->delete();

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
