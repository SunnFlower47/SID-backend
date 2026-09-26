<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Central\TenantAllocation;
use Inertia\Inertia;

class TenantAllocationController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-allocations');

        $allocations = TenantAllocation::with('tenant')->paginate(10);
        return Inertia::render('Landlord/Allocations/Index', ['allocations' => $allocations]);
    }

    public function update(Request $request, TenantAllocation $allocation)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-allocations');

        $validated = $request->validate([
            'max_users' => 'required|integer|min:1',
            'storage_limit_mb' => 'required|integer|min:100',
            'is_active' => 'required|boolean',
        ]);

        $allocation->update($validated);

        return redirect()->back()->with('success', 'Alokasi berhasil diperbarui.');
    }
}
