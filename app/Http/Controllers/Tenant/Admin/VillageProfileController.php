<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\DesaSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Gate;

class VillageProfileController extends Controller
{
    /**
     * Tampilkan halaman Profil Desa terpusat
     */
    public function index()
    {
        Gate::authorize('settings.view');

        return Inertia::render('Tenant/Admin/VillageProfile/Index', [
            'profile' => DesaSetting::getFullProfile(),
        ]);
    }

    /**
     * Update data profil desa secara masal
     */
    public function update(Request $request)
    {
        Gate::authorize('settings.view');

        $validated = $request->validate([
            // General
            'nama_desa' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kode_pos' => 'required|string|max:10',
            'alamat_lengkap' => 'required|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            
            // Geography
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'luas_total' => 'nullable|numeric',
            
            // Additional Info
            'visi' => 'nullable|string',
            'misi' => 'nullable|string',
            'sejarah_desa' => 'nullable|string',
            
            // Social Media
            'link_facebook' => 'nullable|url|max:255',
            'link_instagram' => 'nullable|url|max:255',
            'link_youtube' => 'nullable|url|max:255',
            'link_whatsapp' => 'nullable|url|max:255',
        ]);

        // Mapping keys to groups
        $groups = [
            'nama_desa' => DesaSetting::GROUP_GENERAL,
            'kecamatan' => DesaSetting::GROUP_GENERAL,
            'kabupaten' => DesaSetting::GROUP_GENERAL,
            'provinsi' => DesaSetting::GROUP_GENERAL,
            'kode_pos' => DesaSetting::GROUP_GENERAL,
            'alamat_lengkap' => DesaSetting::GROUP_GENERAL,
            'telepon' => DesaSetting::GROUP_GENERAL,
            'email' => DesaSetting::GROUP_GENERAL,
            'website' => DesaSetting::GROUP_GENERAL,
            'latitude' => DesaSetting::GROUP_GENERAL,
            'longitude' => DesaSetting::GROUP_GENERAL,
            'luas_total' => DesaSetting::GROUP_GEOGRAPHY,
            'visi' => DesaSetting::GROUP_PROFILE,
            'misi' => DesaSetting::GROUP_PROFILE,
            'sejarah_desa' => DesaSetting::GROUP_PROFILE,
            'link_facebook' => DesaSetting::GROUP_SOCIAL,
            'link_instagram' => DesaSetting::GROUP_SOCIAL,
            'link_youtube' => DesaSetting::GROUP_SOCIAL,
            'link_whatsapp' => DesaSetting::GROUP_SOCIAL,
        ];

        foreach ($validated as $key => $value) {
            DesaSetting::setValue($key, $value ?? '', 'text', $groups[$key] ?? DesaSetting::GROUP_GENERAL);
        }

        return Redirect::back()->with('success', 'Profil desa berhasil diperbarui!');
    }

    /**
     * Update Logo & Branding
     */
    public function updateLogos(Request $request)
    {
        Gate::authorize('settings.view');

        $request->validate([
            'logo_desa' => 'nullable|image|max:2048',
            'logo_kabupaten' => 'nullable|image|max:2048',
            'logo_provinsi' => 'nullable|image|max:2048',
        ]);

        $keys = ['logo_desa', 'logo_kabupaten', 'logo_provinsi'];

        foreach ($keys as $key) {
            if ($request->hasFile($key)) {
                $file = $request->file($key);
                $filename = $key . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/logos', $filename);
                DesaSetting::setValue($key, Storage::url($path), 'image', DesaSetting::GROUP_LOGO);
            }
        }

        return Redirect::back()->with('success', 'Logo branding berhasil diperbarui!');
    }
}
