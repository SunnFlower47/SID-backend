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

// Schedule Backups
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:clean')->daily()->at('01:00');
Schedule::command('backup:run')->daily()->at('02:00');
Schedule::command('wilayah-backup:prune --days=30')->daily()->at('03:00');
Schedule::command('domisili:check-expiry')->daily()->at('00:00');
Schedule::command('pbb:sync --limit=10')->everyMinute()->withoutOverlapping();
