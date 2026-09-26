<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;

class HardDeleteTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:hard-delete {id} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus tenant desa secara permanen beserta database dan data terkait di tabel central';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('id');
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            $this->error("Tenant dengan ID '{$tenantId}' tidak ditemukan.");
            return 1;
        }

        $this->warn("PERINGATAN: Tindakan ini akan menghapus database tenant 'db_tenant_{$tenantId}' secara permanen!");
        $this->warn("Tindakan ini juga akan menghapus domain, pemetaan user, dan kuota alokasi.");

        if (!$this->option('force')) {
            if (!$this->confirm("Apakah Anda yakin ingin menghapus desa '{$tenant->name}' ({$tenantId}) secara permanen?")) {
                $this->info("Penghapusan dibatalkan.");
                return 0;
            }
        }

        $this->info("Memulai proses penghapusan permanen untuk tenant '{$tenantId}'...");

        try {
            // 1. Drop Database Tenant
            $this->info("Menghapus database tenant...");
            try {
                $tenant->database()->manager()->deleteDatabase($tenant);
                $this->info("Database tenant berhasil di-drop.");
            } catch (\Exception $dbEx) {
                $this->error("Gagal atau database sudah tidak ada saat menghapus database: " . $dbEx->getMessage());
            }

            // 2. Hapus data domain
            $this->info("Menghapus data domain...");
            $tenant->domains()->delete();

            // 3. Hapus pemetaan user
            $this->info("Menghapus data user_tenant_map...");
            \App\Models\Central\UserTenantMap::where('tenant_id', $tenantId)->delete();

            // 4. Hapus data alokasi kuota
            $this->info("Menghapus data alokasi kuota...");
            \App\Models\Central\TenantAllocation::where('tenant_id', $tenantId)->delete();

            // 5. Hapus log aktivitas
            $this->info("Menghapus log aktivitas terkait...");
            \App\Models\Central\TenantActivityLog::where('tenant_id', $tenantId)->delete();

            // 6. Hapus Record Tenant
            $this->info("Menghapus data record tenant dari DB Central...");
            $tenant->delete();

            $this->info("Tenant '{$tenantId}' berhasil dihapus secara permanen dari sistem.");
            return 0;
        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan fatal selama proses penghapusan: " . $e->getMessage());
            return 1;
        }
    }
}
