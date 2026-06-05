<?php

namespace App\Http\Controllers\Tenant\Pelayanan;

use App\Http\Controllers\Controller;
use App\Models\SuratType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use PhpOffice\PhpWord\TemplateProcessor;

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
        } else {
            unset($validated['file_template']);
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

    /**
     * Baca variabel ${...} yang ada di dalam file .docx template.
     * Menggunakan PhpWord TemplateProcessor::getVariables() — sangat ringan,
     * hanya scan XML di dalam zip file, tidak ada rendering.
     */
    public function previewTemplate(SuratType $suratType)
    {
        Gate::authorize('settings.view');

        if (!$suratType->file_template) {
            return response()->json(['error' => 'Jenis surat ini belum memiliki template Word.'], 422);
        }

        $templatePath = Storage::disk('local')->path('templates/surat/' . $suratType->file_template);

        if (!file_exists($templatePath)) {
            return response()->json(['error' => 'File template tidak ditemukan di storage.'], 404);
        }

        try {
            $processor  = new TemplateProcessor($templatePath);
            $variables  = $processor->getVariables(); // ['nama', 'nik', 'alamat', ...]
            $variables  = array_values(array_unique($variables));
            sort($variables);

            // Variabel yang selalu tersedia dari sistem (data penduduk + desa)
            $systemVars = [
                'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
                'agama', 'pekerjaan', 'kewarganegaraan', 'status_perkawinan',
                'alamat', 'rt', 'rw', 'dusun', 'desa', 'kecamatan', 'kabupaten',
                'provinsi', 'kode_pos', 'nama_desa', 'nama_kecamatan', 'nama_kabupaten',
                'alamat_desa', 'nomor_surat', 'tanggal_surat', 'tahun_surat',
                'bulan_romawi', 'keperluan', 'tujuan', 'ttd_atas', 'ttd_bawah', 'umur',
            ];

            // Variabel dari form_json (field tambahan yang dibuat admin)
            $formVars = collect($suratType->form_json ?? [])->pluck('name')->toArray();

            // Kategorisasi setiap variabel yang ditemukan di template
            $categorized = array_map(function ($var) use ($systemVars, $formVars) {
                if (in_array($var, $systemVars)) {
                    $category = 'system';
                    $label    = 'Sistem (otomatis)';
                } elseif (in_array($var, $formVars)) {
                    $category = 'form';
                    $label    = 'Form custom';
                } else {
                    $category = 'unknown';
                    $label    = 'Tidak dikenali';
                }
                return ['name' => $var, 'category' => $category, 'label' => $label];
            }, $variables);

            return response()->json([
                'file'        => $suratType->file_template,
                'total'       => count($variables),
                'variables'   => $categorized,
                'form_vars'   => $formVars,
                'system_vars' => $systemVars,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membaca template: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────
    //  SUB-TEMPLATE CRUD (JSON API)
    // ─────────────────────────────────────────────────────────

    /**
     * List semua sub-template untuk jenis surat tertentu.
     */
    public function listTemplates(SuratType $suratType)
    {
        return response()->json(
            $suratType->templates()->orderBy('urutan')->get()
        );
    }

    /**
     * Tambah sub-template baru (+ upload file .docx opsional).
     */
    public function storeTemplate(Request $request, SuratType $suratType)
    {
        Gate::authorize('settings.view');

        // Handle FormData form_json string
        if (is_string($request->form_json)) {
            $request->merge(['form_json' => json_decode($request->form_json, true)]);
        }

        $validated = $request->validate([
            'kode'          => 'required|string|max:20',
            'nama'          => 'required|string|max:150',
            'deskripsi'     => 'nullable|string',
            'file_template' => 'nullable|file|mimes:docx|max:5120',
            'form_json'     => 'nullable|array',
            'urutan'        => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
            'gender_filter' => 'nullable|in:all,L,P',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        }

        $validated['surat_type_id'] = $suratType->id;
        $validated['is_active']     = $validated['is_active'] ?? true;
        $validated['gender_filter'] = $validated['gender_filter'] ?? 'all';
        $validated['urutan']        = $validated['urutan'] ?? 0;

        $template = \App\Models\SuratTypeTemplate::create($validated);

        return response()->json($template, 201);
    }

    /**
     * Update sub-template (+ opsional re-upload file .docx).
     */
    public function updateTemplate(Request $request, SuratType $suratType, $templateId)
    {
        Gate::authorize('settings.view');

        $template = \App\Models\SuratTypeTemplate::where('surat_type_id', $suratType->id)
            ->findOrFail($templateId);

        // Handle FormData form_json string
        if (is_string($request->form_json)) {
            $request->merge(['form_json' => json_decode($request->form_json, true)]);
        }

        $validated = $request->validate([
            'kode'          => 'sometimes|string|max:20',
            'nama'          => 'sometimes|string|max:150',
            'deskripsi'     => 'nullable|string',
            'file_template' => 'nullable|file|mimes:docx|max:5120',
            'form_json'     => 'nullable|array',
            'urutan'        => 'nullable|integer',
            'is_active'     => 'nullable|boolean',
            'gender_filter' => 'nullable|in:all,L,P',
        ]);

        if ($request->hasFile('file_template')) {
            $path = $request->file('file_template')->store('templates/surat', 'local');
            $validated['file_template'] = basename($path);
        } else {
            unset($validated['file_template']);
        }

        $template->update($validated);

        return response()->json($template->fresh());
    }

    /**
     * Hapus sub-template (+ cleanup file dari storage).
     */
    public function destroyTemplate(SuratType $suratType, $templateId)
    {
        Gate::authorize('settings.view');

        $template = \App\Models\SuratTypeTemplate::where('surat_type_id', $suratType->id)
            ->findOrFail($templateId);

        $template->delete();

        return response()->json(['message' => 'Sub-template berhasil dihapus.']);
    }
}
