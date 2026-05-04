<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Models\SuratType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SuratTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin_sistem']);
    }

    public function index()
    {
        $suratTypes = SuratType::orderBy('nama')->get();
        return Inertia::render('Tenant/SuratType/Index', [
            'suratTypes' => $suratTypes
        ]);
    }

    public function create()
    {
        Gate::authorize('admin_sistem');
        return Inertia::render('Tenant/SuratType/Form');
    }

    public function store(Request $request)
    {
        Gate::authorize('admin_sistem');

        $validated = $request->validate([
            'id' => 'required|string|unique:surat_types,id|max:50',
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'has_template' => 'boolean',
            'template_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'form_json' => 'nullable|array',
            'file_template' => 'nullable|file|mimes:docx|max:2048',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        }

        SuratType::create($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(SuratType $suratType)
    {
        Gate::authorize('admin_sistem');
        return Inertia::render('Tenant/SuratType/Form', [
            'suratType' => $suratType
        ]);
    }

    public function update(Request $request, SuratType $suratType)
    {
        Gate::authorize('admin_sistem');

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:20',
            'deskripsi' => 'nullable|string',
            'persyaratan' => 'nullable|string',
            'has_template' => 'boolean',
            'template_code' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'form_json' => 'nullable|array',
            'file_template' => 'nullable|file|mimes:docx|max:2048',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        }

        $suratType->update($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(SuratType $suratType)
    {
        Gate::authorize('admin_sistem');
        $suratType->delete();

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
