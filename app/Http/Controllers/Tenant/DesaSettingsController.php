<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
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
        Gate::authorize('admin_sistem');

        $groups = [
            'general' => 'Informasi Umum Desa',
            'logo' => 'Logo dan Branding'
        ];

        $settings = [];
        foreach ($groups as $group => $name) {
            $settings[$group] = DesaSetting::getByGroup($group);
        }

        return View::make('settings.desa', compact('groups', 'settings'));
    }


    /**
     * Update settings
     */
    public function update(Request $request)
    {
        Gate::authorize('admin_sistem');

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'required|string|in:text,image,json',
            'settings.*.group' => 'required|string'
        ]);

        foreach ($validated['settings'] as $setting) {
            $value = $setting['value'] ?? null;

            // Handle image upload
            if ($setting['type'] === 'image' && $request->hasFile("files.{$setting['key']}")) {
                $file = $request->file("files.{$setting['key']}");
                $filename = $setting['key'] . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/logos', $filename);
                $value = Storage::url($path);
            }

            DesaSetting::setValue(
                $setting['key'],
                $value,
                $setting['type'],
                $setting['group']
            );
        }

        return Redirect::route('settings.desa')
            ->with('success', 'Pengaturan desa berhasil diperbarui.');
    }

    /**
     * Update specific setting
     */
    public function updateSetting(Request $request, $key)
    {
        Gate::authorize('admin_sistem');

        $setting = DesaSetting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'nullable|string',
            'type' => 'sometimes|string|in:text,image,json',
            'group' => 'sometimes|string',
            'description' => 'nullable|string'
        ]);

        // Handle image upload
        if ($setting->type === 'image' && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/logos', $filename);
            $validated['value'] = Storage::url($path);
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
        Gate::authorize('admin_sistem');

        // Delete all current settings
        DesaSetting::truncate();

        // Run migration to insert default settings
        Artisan::call('migrate:refresh', ['--path' => 'database/migrations/2025_09_28_080756_create_desa_settings_table.php']);

        return Redirect::route('settings.desa')
            ->with('success', 'Pengaturan desa berhasil direset ke default.');
    }

    /**
     * Export settings
     */
    public function export()
    {
        Gate::authorize('admin_sistem');

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
        Gate::authorize('admin_sistem');

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

        return redirect()->route('settings.desa')
            ->with('success', 'Pengaturan desa berhasil diimpor.');
    }
}
