<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Central\CentralUser;
use App\Models\Central\CentralRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class CentralUserController extends Controller
{
    public function index()
    {
        Gate::authorize('manage-central-users');

        $users = CentralUser::latest()->paginate(10);
        $roles = CentralRole::all();
        
        return Inertia::render('Landlord/Users/Index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('manage-central-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:central_users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:central_roles,name',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        CentralUser::create($validated);

        return redirect()->back()->with('success', 'User Central berhasil dibuat.');
    }

    public function update(Request $request, CentralUser $user)
    {
        Gate::authorize('manage-central-users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:central_users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:central_roles,name',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'Data User Central berhasil diperbarui.');
    }

    public function destroy(CentralUser $user)
    {
        Gate::authorize('manage-central-users');

        if ($user->id === auth('landlord')->id()) {
            return redirect()->back()->withErrors(['error' => 'Anda tidak bisa menghapus akun Anda sendiri!']);
        }

        $user->delete();

        return redirect()->back()->with('success', 'User Central berhasil dihapus.');
    }
}
