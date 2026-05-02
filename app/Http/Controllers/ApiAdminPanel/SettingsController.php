<?php

namespace App\Http\Controllers\ApiAdminPanel;

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
class SettingsController extends Controller
{
    public function index()
    {
        Gate::authorize('admin_sistem');

        // Real-time data without cache
        $stats = [
            'totalUsers' => User::count(),
            'totalRoles' => Role::count(),
            'totalPenduduk' => Penduduk::count(),
            'totalKK' => Penduduk::select('nkk')->distinct()->count(),
            'totalMutasi' => Mutasi::count(),
        ];

        // Real-time user data
        $users = User::with('roles')->get();

        // Real-time roles data
        $roles = Role::with('permissions')->get();

        // Real-time permissions grouped by module
        $allPermissions = Permission::all();
        $permissions = [
            'penduduk' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'penduduk')),
            'kartu_keluarga' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'kartu_keluarga') || str_starts_with($p->name, 'kartu-keluarga')),
            'mutasi' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'mutasi')),
                'laporan' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'laporan')),
                'statistics' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'statistics')),
                'settings' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'settings')),
                'wilayah' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'wilayah')),
                'surat' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'surat')),
                'backup' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'backup')),
                'audit_log' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'audit_log')),
                'testimoni' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'testimoni')),
                'bantuan_sosial' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'bantuan_sosial')),
                'pengaduan' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'pengaduan')),
                'umkm' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'umkm')),
                'export' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'export')),
                'import' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'import')),
                'struktur_desa' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'struktur-desa')),
                'kontak_desa' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'kontak-desa')),
                'transparansi_desa' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'transparansi-desa')),
                'fasilitas_desa' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'fasilitas-desa')),
                'berita' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'berita')),
                'comparison' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'comparison')),
                'pisah_kk' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'pisah-kk')),
                'contact_messages' => $allPermissions->filter(fn($p) => str_starts_with($p->name, 'contact-messages')),
            ];

        return view('settings.index', compact(
            'users',
            'roles',
            'permissions',
            'stats'
        ));
    }

    public function users()
    {
        Gate::authorize('admin_sistem');

        // Get users with their roles
        $users = User::with('roles')->get();

        // Get all roles
        $roles = Role::all();

        return view('settings.users.index', compact('users', 'roles'));
    }

    public function updateUser(Request $request, User $user)
    {
        try {
            Log::info('Update user request:', $request->all()); // Debug log

            Gate::authorize('admin_sistem');

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:8',
                'password_confirmation' => 'nullable|same:password',
                'role' => 'required|exists:roles,id',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Sync roles (single role) - find role by ID
            $role = Role::find($request->role);
            if ($role) {
                Log::info('Assigning role:', ['role_id' => $role->id, 'role_name' => $role->name]); // Debug log
                $user->syncRoles([$role->name]);
            } else {
                throw new \Exception('Role tidak ditemukan');
            }

            // Clear relevant caches
                                    return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui!'
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk mengedit user.'
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createUser(Request $request)
    {
        try {
            Gate::authorize('admin_sistem');

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8|confirmed',
                'role' => 'required|exists:roles,id',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign role (single role) - find role by ID
            $role = Role::find($request->role);
            if ($role) {
                $user->assignRole($role->name);
            } else {
                throw new \Exception('Role tidak ditemukan');
            }

            // Clear relevant caches
                                    return response()->json([
                'success' => true,
                'message' => 'User berhasil dibuat!'
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat user.'
            ], 403);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteUser(User $user)
    {
        Gate::authorize('admin_sistem');

        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus akun sendiri!'
            ], 400);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus!'
        ]);
    }

    public function updateRole(Request $request, Role $role)
    {
        Gate::authorize('admin_sistem');

        Log::info('Update role request:', $request->all()); // Debug log

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        $role->name = $request->name;
        $role->save();

        // Sync permissions - convert IDs to names
        Log::info('Syncing permissions:', ['role_id' => $role->id, 'permissions' => $request->permissions]); // Debug log

        if ($request->permissions && is_array($request->permissions)) {
            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            Log::info('Permission names:', ['names' => $permissionNames]); // Debug log
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }

        // Clear relevant caches
                        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui!'
        ]);
    }

    public function createRole(Request $request)
    {
        Gate::authorize('admin_sistem');

        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        // Assign permissions - convert IDs to names
        if ($request->permissions && is_array($request->permissions)) {
            // Convert permission IDs to permission names
            $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $role->givePermissionTo($permissionNames);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dibuat!'
        ]);
    }

    public function deleteRole(Role $role)
    {
        Gate::authorize('admin_sistem');

        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak dapat dihapus karena masih digunakan oleh user!'
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus!'
        ]);
    }
}
