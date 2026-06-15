<?php

use App\Models\WilayahChangeLog;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('wilayah-backup:prune {--days=30}', function () {
    $days = (int) $this->option('days');
    $cutoff = now()->subDays(max($days, 1));

    $affected = WilayahChangeLog::query()
        ->whereNotNull('backup_payload')
        ->where(function ($q) use ($cutoff) {
            $q->where('applied_at', '<', $cutoff)
              ->orWhere(function ($qq) use ($cutoff) {
                  $qq->whereNull('applied_at')->where('created_at', '<', $cutoff);
              });
        })
        ->update(['backup_payload' => null]);

    $this->info("Prune selesai. Backup payload dibersihkan: {$affected} log (retensi {$days} hari).");
})->purpose('Cleanup backup payload wilayah_change_logs older than retention days');

// Backup Multi-Tenant Command
Artisan::command('app:backup-run {--only-db} {--only-files}', function () {
    $this->info("Menyiapkan backup database & storage multi-tenant...");
    
    $defaultConnection = config('database.default');
    $databases = [$defaultConnection]; // Backup database central landlord
    
    try {
        $tenants = \App\Models\Tenant::where('is_active', true)->get();
        foreach ($tenants as $tenant) {
            $dbName = $tenant->database()->getName();
            $connectionName = "tenant_backup_{$tenant->id}";
            
            // Daftarkan koneksi database tenant secara dinamis
            config(["database.connections.{$connectionName}" => [
                'driver' => 'mysql',
                'host' => env('DB_HOST', '127.0.0.1'),
                'port' => env('DB_PORT', '3306'),
                'database' => $dbName,
                'username' => env('DB_USERNAME', 'root'),
                'password' => env('DB_PASSWORD', ''),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]]);
            
            $databases[] = $connectionName;
            $this->line("Menambahkan database tenant: {$dbName} (koneksi: {$connectionName})");
        }
    } catch (\Exception $e) {
        $this->error("Gagal memuat database tenant: " . $e->getMessage());
    }
    
    // Injeksi daftar database ke config backup spatie sebelum dijalankan
    config(['backup.backup.source.databases' => $databases]);
    
    $options = [];
    if ($this->option('only-db')) {
        $options['--only-db'] = true;
    }
    if ($this->option('only-files')) {
        $options['--only-files'] = true;
    }
    
    $this->info("Memulai proses backup...");
    $exitCode = Artisan::call('backup:run', $options, $this->output);
    
    return $exitCode;
})->purpose('Run database & files backup for central and all active tenant databases');

// Schedule Backups
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('app:backup-run --only-db')->daily()->at('02:00');
Schedule::command('app:backup-run')->monthlyOn(1, '02:30');
Schedule::command('wilayah-backup:prune --days=30')->daily()->at('03:00');
Schedule::command('tenants:run app:cleanup-temp-storage')->daily()->at('04:00');
Schedule::command('tenants:run domisili:check-expiry')->daily()->at('00:00');
Schedule::command('tenants:run pbb:sync --option="limit=15"')->everyMinute()->withoutOverlapping();
