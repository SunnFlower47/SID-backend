<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Central\UserTenantMap;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class TenantUserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage-tenants');
        $search = $request->input('search');
        $tenantFilter = $request->input('tenant_id');

        $query = UserTenantMap::query();
        
        if ($search) {
            $query->where('email', 'like', "%{$search}%");
        }
        if ($tenantFilter) {
            $query->where('tenant_id', $tenantFilter);
        }

        $mappings = $query->latest()->paginate(15);
        $userData = [];

        foreach ($mappings->items() as $map) {
            $tenant = Tenant::find($map->tenant_id);
            if ($tenant) {
                try {
                    $tenant->run(function () use (&$userData, $map) {
                        $user = \App\Models\User::where('email', $map->email)->first();
                        if ($user) {
                            $userData[] = [
                                'map_id' => $map->id,
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'tenant_id' => $map->tenant_id,
                                'tenant_name' => tenant('name') ?? $map->tenant_id,
                                'role' => $user->getRoleNames()->first() ?? 'Tidak ada role',
                                'created_at' => $user->created_at,
                            ];
                        }
                    });
                } catch (\Exception $e) {
                    $userData[] = [
                        'map_id' => $map->id,
                        'id' => null,
                        'name' => 'Error: DB Offline',
                        'email' => $map->email,
                        'tenant_id' => $map->tenant_id,
                        'tenant_name' => $map->tenant_id,
                        'role' => 'Error',
                        'created_at' => $map->created_at,
                    ];
                }
            }
        }

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $userData,
            $mappings->total(),
            $mappings->perPage(),
            $mappings->currentPage(),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $tenants = Tenant::all();

        return Inertia::render('Landlord/TenantUsers/Index', [
            'users' => $paginatedData,
            'tenants' => $tenants,
            'filters' => $request->only(['search', 'tenant_id']),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-tenants');
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_tenant_map,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:Super Admin,Admin Desa,Staf Desa,Viewer',
        ]);

        $tenant = Tenant::findOrFail($validated['tenant_id']);
        
        try {
            $tenant->run(function () use ($validated) {
                // Check if user limit is reached (using the boot check or directly)
                $user = \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'email_verified_at' => now(),
                ]);

                $user->assignRole($validated['role']);
            });

            // Map user in central DB
            UserTenantMap::create([
                'email' => $validated['email'],
                'tenant_id' => $validated['tenant_id'],
            ]);

            return redirect()->back()->with('success', 'User Tenant berhasil didaftarkan.');
        } catch (\Illuminate\Validation\ValidationException $valEx) {
            return redirect()->back()->withErrors($valEx->errors());
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membuat user tenant: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $mapId)
    {
        Gate::authorize('manage-tenants');
        $map = UserTenantMap::findOrFail($mapId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_tenant_map,email,' . $map->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|in:Super Admin,Admin Desa,Staf Desa,Viewer',
        ]);

        $tenant = Tenant::findOrFail($map->tenant_id);

        try {
            $tenant->run(function () use ($map, $validated) {
                $user = \App\Models\User::where('email', $map->email)->first();
                if ($user) {
                    $updateData = [
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                    ];

                    if (!empty($validated['password'])) {
                        $updateData['password'] = Hash::make($validated['password']);
                    }

                    $user->update($updateData);
                    $user->syncRoles([$validated['role']]);
                }
            });

            // Update mapping
            $map->update(['email' => $validated['email']]);

            return redirect()->back()->with('success', 'Data User Tenant berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui user tenant: ' . $e->getMessage()]);
        }
    }

    public function destroy($mapId)
    {
        Gate::authorize('manage-tenants');
        $map = UserTenantMap::findOrFail($mapId);
        $tenant = Tenant::find($map->tenant_id);

        try {
            if ($tenant) {
                $tenant->run(function () use ($map) {
                    $user = \App\Models\User::where('email', $map->email)->first();
                    if ($user) {
                        $user->delete();
                    }
                });
            }

            $map->delete();

            return redirect()->back()->with('success', 'User Tenant berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus user tenant: ' . $e->getMessage()]);
        }
    }
}
