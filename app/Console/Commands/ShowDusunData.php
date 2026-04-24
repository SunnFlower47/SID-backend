<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;

class ShowDusunData extends Command
{
    protected $signature = 'show:dusun-data';
    protected $description = 'Show dusun data distribution';

    public function handle()
    {
        $this->info('Data per Dusun:');

        $dusunCounts = Penduduk::selectRaw('dusun, COUNT(*) as count')
            ->groupBy('dusun')
            ->orderBy('dusun')
            ->get();

        foreach ($dusunCounts as $dusun) {
            $dusunName = $dusun->dusun ?: 'KOSONG';
            $this->line("- {$dusunName}: {$dusun->count} penduduk");
        }

        $this->info("\nData per RT dan Dusun:");
        $rtCounts = Penduduk::selectRaw('rt, dusun, COUNT(*) as count')
            ->groupBy('rt', 'dusun')
            ->orderBy('rt')
            ->get();

        foreach ($rtCounts as $rt) {
            $dusunName = $rt->dusun ?: 'KOSONG';
            $this->line("- RT {$rt->rt} ({$dusunName}): {$rt->count} penduduk");
        }

        return 0;
    }
}

