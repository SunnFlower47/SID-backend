<?php

namespace App\Http\Controllers\ApiAdminPanel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\DesaSetting;
use Illuminate\Http\JsonResponse;

class DesaSettingsController extends Controller
{
    /**
     * Display all settings grouped.
     */
    public function index(): JsonResponse
    {
        Gate::authorize('settings.view');

        $groups = [
            'general' => 'Informasi Umum Desa',
            'logo' => 'Logo dan Branding',
            'surat' => 'Pengaturan Surat',
            'template' => 'Template Surat'
        ];

        $settings = [];
        foreach ($groups as $group => $name) {
            $settings[$group] = DesaSetting::getByGroup($group);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'groups' => $groups,
                'settings' => $settings
            ]
        ]);
    }

    /**
     * Update multiple settings.
     */
    public function update(Request $request): JsonResponse
    {
        Gate::authorize('settings.edit');

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string',
            'settings.*.type' => 'required|string|in:text,image,json',
            'settings.*.group' => 'required|string'
        ]);

        foreach ($validated['settings'] as $setting) {
            $value = $setting['value'] ?? null;

            // Handle image upload if provided as file
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

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan desa berhasil diperbarui'
        ]);
    }

    /**
     * Update a single setting (Useful for logo upload).
     */
    public function updateSetting(Request $request, $key): JsonResponse
    {
        Gate::authorize('settings.edit');

        $setting = DesaSetting::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'value' => 'nullable|string',
            'type' => 'sometimes|string|in:text,image,json',
            'group' => 'sometimes|string',
        ]);

        if ($setting->type === 'image' && $request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/logos', $filename);
            $validated['value'] = Storage::url($path);
        }

        $setting->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengaturan berhasil diperbarui',
            'data' => $setting
        ]);
    }
}
