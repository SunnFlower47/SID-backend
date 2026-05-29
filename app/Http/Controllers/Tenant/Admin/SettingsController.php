<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Penduduk;
use App\Models\Mutasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        Gate::authorize('settings.view');

        $stats = [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalPenduduk' => Penduduk::count(),
            'totalKK' => Penduduk::select('nkk')->distinct()->count(),
            'totalMutasi' => Mutasi::count(),
        ];

        $users = User::with(['roles', 'permissions'])->get();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $desa_settings = \App\Models\DesaSetting::all()->keyBy('key');

        return Inertia::render('Tenant/Settings/Index', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
            'permissions_structure' => config('permissions'),
            'stats' => $stats,
            'desa_settings' => $desa_settings
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            Gate::authorize('users.edit');

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:8',
                'password_confirmation' => 'nullable|same:password',
                'role' => 'required|exists:roles,id',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            $role = Role::find($request->role);
            if ($role) {
                $user->syncRoles([$role->name]);
            } else {
                throw new \Exception('Role tidak ditemukan');
            }

            if ($request->has('permissions')) {
                if (is_array($request->permissions)) {
                    $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                    $user->syncPermissions($permissionNames);
                } else {
                    $user->syncPermissions([]);
                }
            }

            return redirect()->back()->with('success', 'User berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage());
        }
    }

    public function createUser(Request $request)
    {
        try {
            Gate::authorize('users.create');

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
                'role' => 'required|exists:roles,id',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permissions,id',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $role = Role::find($request->role);
            if ($role) {
                $user->assignRole($role->name);
            } else {
                throw new \Exception('Role tidak ditemukan');
            }

            if ($request->has('permissions') && is_array($request->permissions)) {
                $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $user->syncPermissions($permissionNames);
            }

            return redirect()->back()->with('success', 'User berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat user: ' . $e->getMessage());
        }
    }

    public function deleteUser(User $user)
    {
        Gate::authorize('users.delete');

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus!');
    }

    public function updateRole(Request $request, Role $role)
    {
        try {
            Gate::authorize('roles.edit');

            $request->validate([
                'name' => 'required|string|max:255',
                'permissions' => 'array',
            ]);

            $role->name = $request->name;
            $role->save();

            if ($request->permissions && is_array($request->permissions)) {
                $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->syncPermissions($permissionNames);
            } else {
                $role->syncPermissions([]);
            }

            return redirect()->back()->with('success', 'Role berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui role: ' . $e->getMessage());
        }
    }

    public function createRole(Request $request)
    {
        try {
            Gate::authorize('roles.create');

            $request->validate([
                'name' => 'required|string|max:255|unique:roles',
                'permissions' => 'array',
            ]);

            $role = Role::create(['name' => $request->name]);

            if ($request->permissions && is_array($request->permissions)) {
                $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
                $role->givePermissionTo($permissionNames);
            }

            return redirect()->back()->with('success', 'Role berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat membuat role: ' . $e->getMessage());
        }
    }

    public function deleteRole(Role $role)
    {
        Gate::authorize('roles.delete');

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user!');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus!');
    }
}
