<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Artisan;
use App\Models\DesaSetting;

class DesaSettingsController extends Controller
{
        public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display settings page (admin or web-desa)
     */
    public function index()
    {
        return redirect()->route('settings.index');
    }


    /**
     * Update settings
     */
    public function update(Request $request)
    {
        Gate::authorize('settings.view');

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'required|string|in:text,image,json',
            'settings.*.group' => 'required|string'
        ]);

        foreach ($validated['settings'] as $setting) {
            $value = $setting['value'] ?? null;

            // Handle image and json (GeoJSON) file upload
            if (in_array($setting['type'], ['image', 'json']) && $request->hasFile("files.{$setting['key']}")) {
                $file = $request->file("files.{$setting['key']}");
                $extension = $file->getClientOriginalExtension();
                $filename = $setting['key'] . '_' . time() . '.' . $extension;
                
                $folder = $setting['type'] === 'image' ? 'logos' : 'geojson';

                // Hapus file lama agar tidak menumpuk
                $existingSetting = DesaSetting::where('key', $setting['key'])->first();
                if ($existingSetting) {
                    $this->deleteOldFile($existingSetting->value);
                }

                $path = $file->storeAs($folder, $filename);
                $value = Storage::url($path);

                // Hapus cache API GeoJSON agar data terbaru langsung tersaji
                if ($setting['type'] === 'json') {
                    Cache::forget('api_geojson_batas_wilayah');
                }
            }

            DesaSetting::setValue(
                $setting['key'],
                $value,
                $setting['type'],
                $setting['group']
            );
        }

        return Redirect::back()
            ->with('success', 'Pengaturan desa berhasil diperbarui.');
    }

    /**
     * Update specific setting
     */
    public function updateSetting(Request $request, $key)
    {
        Gate::authorize('settings.view');

        $setting = DesaSetting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'nullable|string',
            'type' => 'sometimes|string|in:text,image,json',
            'group' => 'sometimes|string',
            'description' => 'nullable|string'
        ]);

        // Handle image and json (GeoJSON) file upload
        if (in_array($setting->type, ['image', 'json']) && $request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = $key . '_' . time() . '.' . $extension;
            
            $folder = $setting->type === 'image' ? 'logos' : 'geojson';

            // Hapus file lama agar tidak menumpuk
            $this->deleteOldFile($setting->value);

            $path = $file->storeAs($folder, $filename);
            $validated['value'] = Storage::url($path);

            // Hapus cache API GeoJSON agar data terbaru langsung tersaji
            if ($setting->type === 'json') {
                Cache::forget('api_geojson_batas_wilayah');
            }
        }

        $setting->update($validated);

        return Response::json([
            'success' => true,
            'message' => 'Pengaturan berhasil diperbarui.',
            'setting' => $setting
        ]);
    }

    /**
     * Reset settings to default
     */
    public function reset()
    {
        Gate::authorize('settings.view');

        // Delete all current settings
        DesaSetting::truncate();

        // Run migration to insert default settings
        Artisan::call('migrate:refresh', ['--path' => 'database/migrations/2025_09_28_080756_create_desa_settings_table.php']);

        return Redirect::back()
            ->with('success', 'Pengaturan desa berhasil direset ke default.');
    }

    /**
     * Export settings
     */
    public function export()
    {
        Gate::authorize('settings.view');

        $settings = DesaSetting::all();

        $data = $settings->map(function ($setting) {
            return [
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'group' => $setting->group,
                'description' => $setting->description
            ];
        });

        $filename = 'desa_settings_' . date('Y-m-d_H-i-s') . '.json';

        return Response::json($data)
            ->header('Content-Disposition', "attachment; filename={$filename}");
    }

    /**
     * Import settings
     */
    public function import(Request $request)
    {
        Gate::authorize('settings.view');

        $validated = $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file->getPathname());
        $data = json_decode($content, true);

        if (!$data) {
            return Redirect::back()
                ->with('error', 'File JSON tidak valid.');
        }

        foreach ($data as $setting) {
            DesaSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'] ?? 'text',
                    'group' => $setting['group'] ?? 'general',
                    'description' => $setting['description'] ?? null
                ]
            );
        }

        return Redirect::back()
            ->with('success', 'Pengaturan desa berhasil diimpor.');
    }

    /**
     * Delete old file from storage safely.
     */
    private function deleteOldFile($value): void
    {
        if (!$value) {
            return;
        }

        try {
            $path = parse_url($value, PHP_URL_PATH);
            if (!$path) {
                return;
            }

            $path = ltrim($path, '/');

            // Strip S3/R2 bucket name if it's the first segment (in path-style URL)
            $bucket = env('AWS_BUCKET');
            if ($bucket && (str_starts_with($path, $bucket . '/') || $path === $bucket)) {
                $path = substr($path, strlen($bucket . '/'));
            }

            // Strip 'storage/' if it's local public storage path
            if (str_starts_with($path, 'storage/')) {
                $path = substr($path, strlen('storage/'));
            }

            if ($path && Storage::exists($path)) {
                Storage::delete($path);
            }
        } catch (\Exception $e) {
            // Log warning but do not crash the request
            \Illuminate\Support\Facades\Log::warning("Failed to delete old file: " . $e->getMessage(), [
                'value' => $value,
                'resolved_path' => $path ?? null
            ]);
        }
    }
}
