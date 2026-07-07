<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Central\CentralSetting;
use App\Models\Central\CentralRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Show the landlord settings page.
     */
    public function index(\App\Services\System\BackupService $backupService)
    {
        Gate::authorize('manage-central-users');

        $user = auth('landlord')->user();

        $settings = [
            'default_max_users' => CentralSetting::get('default_max_users', '10'),
            'default_storage_limit_mb' => CentralSetting::get('default_storage_limit_mb', '1024'),
            'diskominfo_hotline' => CentralSetting::get('diskominfo_hotline', '081234567890'),
            'diskominfo_email' => CentralSetting::get('diskominfo_email', 'admin@central.go.id'),
            'central_base_domain' => CentralSetting::get('central_base_domain', 'sistem-desa-cibatu.test'),
            'central_admin_domain' => CentralSetting::get('central_admin_domain', 'admin.sistem-desa-cibatu.test'),
        ];

        return Inertia::render('Landlord/Settings/Index', [
            'settings' => $settings,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'roles' => CentralRole::all(),
            'backupFiles' => $backupService->getBackupFiles(),
            'diskSpace' => $backupService->getDiskSpaceInfo(),
            'stats' => $backupService->getBackupStats(),
        ]);
    }

    /**
     * Store a new central role.
     */
    public function storeRole(Request $request)
    {
        Gate::authorize('manage-central-users');

        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:central_roles,name|regex:/^[a-zA-Z0-9\-_]+$/',
            'display_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:manage-central-users,manage-allocations,manage-tenants,broadcast-announcements',
        ]);

        CentralRole::create($validated);

        return redirect()->back()->with('success', 'Role baru berhasil dibuat.');
    }

    /**
     * Update an existing central role.
     */
    public function updateRole(Request $request, CentralRole $role)
    {
        Gate::authorize('manage-central-users');

        $rules = [
            'display_name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:manage-central-users,manage-allocations,manage-tenants,broadcast-announcements',
        ];

        // Jangan izinkan edit slug/name untuk superadmin
        if ($role->name !== 'superadmin') {
            $rules['name'] = 'required|string|max:50|unique:central_roles,name,' . $role->id . '|regex:/^[a-zA-Z0-9\-_]+$/';
        }

        $validated = $request->validate($rules);

        // superadmin must keep all permissions to prevent lockout
        if ($role->name === 'superadmin') {
            $validated['permissions'] = ['manage-central-users', 'manage-allocations', 'manage-tenants', 'broadcast-announcements'];
        }

        $role->update($validated);

        return redirect()->back()->with('success', 'Data role berhasil diperbarui.');
    }

    /**
     * Delete a central role.
     */
    public function destroyRole(CentralRole $role)
    {
        Gate::authorize('manage-central-users');

        if ($role->name === 'superadmin') {
            return redirect()->back()->withErrors(['error' => 'Role Super Admin tidak dapat dihapus!']);
        }

        // Periksa jika ada user yang masih menggunakan role ini
        $userCount = \App\Models\Central\CentralUser::where('role', $role->name)->count();
        if ($userCount > 0) {
            return redirect()->back()->withErrors(['error' => "Role ini tidak dapat dihapus karena masih digunakan oleh {$userCount} user central."]);
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }

    /**
     * Update SaaS settings and/or landlord profile.
     */
    public function update(Request $request)
    {
        Gate::authorize('manage-central-users');

        $user = auth('landlord')->user();

        $rules = [
            // SaaS System Settings
            'default_max_users' => 'required|integer|min:1',
            'default_storage_limit_mb' => 'required|integer|min:1',
            'diskominfo_hotline' => 'required|string|max:50',
            'diskominfo_email' => 'required|email|max:255',
            'central_base_domain' => 'required|string|max:255',
            'central_admin_domain' => 'required|string|max:255',

            // Profile Settings
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:central_users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ];

        $validated = $request->validate($rules);

        // 1. Update SaaS System Settings
        CentralSetting::set('default_max_users', (string) $validated['default_max_users']);
        CentralSetting::set('default_storage_limit_mb', (string) $validated['default_storage_limit_mb']);
        CentralSetting::set('diskominfo_hotline', $validated['diskominfo_hotline']);
        CentralSetting::set('diskominfo_email', $validated['diskominfo_email']);
        CentralSetting::set('central_base_domain', $validated['central_base_domain']);
        CentralSetting::set('central_admin_domain', $validated['central_admin_domain']);

        // 2. Update Personal Profile
        $profileData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $profileData['password'] = Hash::make($validated['password']);
        }

        $user->update($profileData);

        return redirect()->back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
