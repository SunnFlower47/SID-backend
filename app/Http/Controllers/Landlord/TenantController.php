<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Central\LandlordAuditLog;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        $tenants = Tenant::with('domains')->latest()->paginate(10);
        return Inertia::render('Landlord/Tenants/Index', ['tenants' => $tenants]);
    }

    public function create()
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        $baseDomain = \App\Models\Central\CentralSetting::get('central_base_domain', 'sistem-desa-cibatu.test');
        return Inertia::render('Landlord/Tenants/Create', [
            'baseDomain' => $baseDomain,
        ]);
    }

    public function store(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        // Hindari timeout karena proses Create DB + Migrate + Seed memakan waktu lama
        set_time_limit(0);

        $request->merge([
            'domain' => strtolower($request->input('domain')),
        ]);

        $validated = $request->validate([
            'id' => 'required|string|unique:tenants,id|regex:/^[a-z0-9\-_]+$/',
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:domains,domain',
            'operator_name' => 'required|string|max:255',
            'operator_email' => 'required|email|unique:user_tenant_map,email',
            'operator_password' => 'required|string|min:8',
        ], [
            'operator_email.unique' => 'Email operator ini sudah terdaftar untuk desa lain. Silakan gunakan email yang unik.',
            'id.unique' => 'ID/Slug desa ini sudah terdaftar.',
            'domain.unique' => 'Domain ini sudah terdaftar.',
        ]);

        \Illuminate\Support\Facades\Log::info("Memulai pembuatan Tenant baru: {$validated['name']} ({$validated['id']})");
        
        try {
            // 1. Create Tenant
            $tenant = Tenant::create([
                'id' => $validated['id'],
                'name' => $validated['name'],
                'is_active' => true,
                'status' => 'pending',
                'operator_name' => $validated['operator_name'],
                'operator_email' => $validated['operator_email'],
                'operator_password' => $validated['operator_password'],
            ]);

            \Illuminate\Support\Facades\Log::info("Database Tenant {$validated['id']} berhasil dibuat, mulai mapping domain...");

            // 2. Create Domain
            $tenant->domains()->create([
                'domain' => $validated['domain'],
            ]);

            // Update status to active
            $tenant->update(['status' => 'active']);

            \Illuminate\Support\Facades\Log::info("Tenant {$validated['id']} sukses dibuat! Proses Migrate & Seed selesai. Mengirim email onboarding...");

            // 3. Kirim Email Onboarding ke Operator Desa
            try {
                \Illuminate\Support\Facades\Mail::to($validated['operator_email'])->send(new \App\Mail\WelcomeTenantMail($tenant));
                \Illuminate\Support\Facades\Log::info("Email onboarding berhasil dikirim ke {$validated['operator_email']}");
            } catch (\Exception $mailEx) {
                \Illuminate\Support\Facades\Log::error("Gagal mengirim email onboarding ke {$validated['operator_email']}: " . $mailEx->getMessage());
            }

            return redirect()->route('tenants.index')->with('success', 'Desa baru berhasil diregistrasi dan database siap digunakan.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal membuat tenant {$validated['id']}: " . $e->getMessage());
            
            // Jika terlanjur terbuat tapi gagal, kita bersihkan secara manual
            if (isset($tenant)) {
                try {
                    // Hapus database secara manual untuk menghindari zombie
                    $tenant->database()->manager()->deleteDatabase($tenant);
                } catch (\Exception $dbEx) {
                    \Illuminate\Support\Facades\Log::error("Gagal membersihkan database tenant: " . $dbEx->getMessage());
                }

                // Hapus maps user, alokasi, dan domain
                try {
                    \App\Models\Central\UserTenantMap::where('tenant_id', $tenant->id)->delete();
                } catch (\Exception $mapEx) {}

                try {
                    \App\Models\Central\TenantAllocation::where('tenant_id', $tenant->id)->delete();
                } catch (\Exception $allocEx) {}

                try {
                    $tenant->domains()->delete();
                } catch (\Exception $domainEx) {}

                $tenant->delete();
            }
            
            return back()->withErrors(['error' => 'Gagal membuat tenant: ' . $e->getMessage()]);
        }
    }

    public function edit(Tenant $tenant)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        $tenant->load('domains');
        return Inertia::render('Landlord/Tenants/Edit', [
            'tenant' => $tenant
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $tenant->update($validated);

        return redirect()->route('tenants.index')->with('success', 'Data desa berhasil diperbarui.');
    }

    public function destroy(Tenant $tenant)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        // Catat sebelum nonaktifkan
        LandlordAuditLog::recordAction(
            event: 'tenant_deactivated',
            subjectType: 'Tenant',
            subjectId: $tenant->id,
            description: "Desa '{$tenant->name}' ({$tenant->id}) dinonaktifkan."
        );

        // Ganti Hapus permanen menjadi Nonaktifkan
        $tenant->update(['is_active' => false]);
        return redirect()->route('tenants.index')->with('success', 'Desa berhasil dinonaktifkan.');
    }

    public function hardDelete(Tenant $tenant)
    {
        \Illuminate\Support\Facades\Gate::authorize('manage-tenants');

        $tenantId = $tenant->id;
        $tenantName = $tenant->name;

        \Illuminate\Support\Facades\Log::info("Memulai hard delete untuk tenant: {$tenantName} ({$tenantId}) via UI");

        try {
            // 1. Drop Database Tenant
            try {
                $tenant->database()->manager()->deleteDatabase($tenant);
            } catch (\Exception $dbEx) {
                \Illuminate\Support\Facades\Log::error("Gagal saat menghapus database tenant {$tenantId}: " . $dbEx->getMessage());
            }

            // 2. Hapus data domain
            $tenant->domains()->delete();

            // 3. Hapus pemetaan user
            \App\Models\Central\UserTenantMap::where('tenant_id', $tenantId)->delete();

            // 4. Hapus alokasi kuota
            \App\Models\Central\TenantAllocation::where('tenant_id', $tenantId)->delete();

            // 5. Hapus log aktivitas
            \App\Models\Central\TenantActivityLog::where('tenant_id', $tenantId)->delete();

            // 6. Hapus record tenant
            $tenant->delete();

            \Illuminate\Support\Facades\Log::info("Tenant {$tenantId} berhasil di-hard delete via UI.");

            LandlordAuditLog::recordAction(
                event: 'tenant_hard_deleted',
                subjectType: 'Tenant',
                subjectId: $tenantId,
                description: "Desa '{$tenantName}' ({$tenantId}) dihapus secara permanen beserta database-nya.",
                metadata: ['tenant_name' => $tenantName]
            );

            return redirect()->route('tenants.index')->with('success', 'Desa berhasil dihapus secara permanen beserta basis datanya.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gagal melakukan hard delete tenant {$tenantId}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus desa secara permanen: ' . $e->getMessage()]);
        }
    }
}
