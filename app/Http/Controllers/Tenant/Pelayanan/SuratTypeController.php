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
        $this->middleware(['auth', 'can:settings.view']);
    }

    public function index()
    {
        $suratTypes = SuratType::orderBy('nama')->get();

        // Info storage template
        $disk   = \Illuminate\Support\Facades\Storage::disk('local');
        $folder = 'templates/surat';

        $filesInStorage = $disk->exists($folder)
            ? collect($disk->files($folder))->map(fn($f) => basename($f))
            : collect();

        $filesInDb = $suratTypes->whereNotNull('file_template')->pluck('file_template');
        $orphans   = $filesInStorage->diff($filesInDb);

        $totalSize = $filesInStorage->sum(
            fn($f) => $disk->exists("{$folder}/{$f}") ? $disk->size("{$folder}/{$f}") : 0
        );

        $storageInfo = [
            'total_files'   => $filesInStorage->count(),
            'active_files'  => $filesInDb->count(),
            'orphan_files'  => $orphans->count(),
            'total_size_kb' => round($totalSize / 1024, 1),
        ];

        return Inertia::render('Tenant/SuratType/Index', [
            'suratTypes'  => $suratTypes,
            'storageInfo' => $storageInfo,
        ]);
    }

    public function panduan()
    {
        // Ambil semua surat type yang memiliki form_json untuk ditampilkan di panduan variabel
        $suratTypes = SuratType::whereNotNull('form_json')
            ->where('form_json', '!=', '[]')
            ->get(['id', 'nama', 'form_json']);

        return Inertia::render('Tenant/SuratType/Panduan', [
            'suratTypes' => $suratTypes
        ]);
    }

    public function create()
    {
        Gate::authorize('settings.view');
        return Inertia::render('Tenant/SuratType/Form');
    }

    public function store(Request $request)
    {
        Gate::authorize('settings.view');

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
            'is_public' => 'boolean',
            'form_json' => 'nullable|array',
            'file_template' => 'nullable|file|mimes:docx|max:2048',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        }

        // Pastikan form_json terisi meskipun kosong
        $validated['form_json'] = $request->input('form_json', []);

        SuratType::create($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil ditambahkan.');
    }

    public function edit(SuratType $suratType)
    {
        Gate::authorize('settings.view');
        return Inertia::render('Tenant/SuratType/Form', [
            'suratType' => $suratType
        ]);
    }

    public function update(Request $request, SuratType $suratType)
    {
        Gate::authorize('settings.view');

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
            'is_public' => 'boolean',
            'form_json' => 'nullable|array',
            'file_template' => 'nullable|file|mimes:docx|max:2048',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        }

        // Pastikan form_json terupdate meskipun kosong (dihapus semua)
        $validated['form_json'] = $request->input('form_json', []);

        $suratType->update($validated);

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    public function destroy(SuratType $suratType)
    {
        Gate::authorize('settings.view');
        $suratType->delete();

        return redirect()->route('admin.surat-type.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
