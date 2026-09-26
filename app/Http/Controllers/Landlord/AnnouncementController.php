<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Central\BroadcastAnnouncement;
use Inertia\Inertia;

class AnnouncementController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('broadcast-announcements');

        $announcements = BroadcastAnnouncement::with('creator')->latest()->paginate(10);
        $tenants = \App\Models\Tenant::get(['id', 'data'])->map(function($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->name ?? $tenant->id
            ];
        });

        return Inertia::render('Landlord/Announcements/Index', [
            'announcements' => $announcements,
            'tenants' => $tenants
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('broadcast-announcements');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,warning,danger',
            'expires_at' => 'nullable|date|after:today',
            'sender_name' => 'nullable|string|max:255',
            'target_type' => 'required|string|in:all,specific',
            'target_tenant_ids' => 'required_if:target_type,specific|array',
            'target_tenant_ids.*' => 'string|exists:tenants,id',
        ]);

        $validated['created_by'] = auth('landlord')->id();

        BroadcastAnnouncement::create($validated);

        return redirect()->back()->with('success', 'Pengumuman berhasil disiarkan.');
    }
}
