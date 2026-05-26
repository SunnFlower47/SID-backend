<?php

namespace App\Console\Commands;

use App\Models\SuratType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanSuratTemplates extends Command
{
    protected $signature = 'surat:clean-templates {--force : Hapus file secara permanen, tanpa flag ini hanya tampilkan (dry-run)}';
    protected $description = 'Bersihkan file template Word (.docx) di storage yang sudah tidak terhubung ke Master Jenis Surat.';

    public function handle(): int
    {
        $disk = Storage::disk('local');
        $folder = 'templates/surat';

        // Ambil semua file yang ada di storage
        if (!$disk->exists($folder)) {
            $this->warn('📂 Folder templates/surat tidak ditemukan di storage.');
            return self::SUCCESS;
        }

        $filesInStorage = collect($disk->files($folder))
            ->map(fn($f) => basename($f));

        if ($filesInStorage->isEmpty()) {
            $this->info('✅ Folder template kosong. Tidak ada yang perlu dibersihkan.');
            return self::SUCCESS;
        }

        // Ambil semua nama file yang masih terdaftar di DB
        $filesInDb = SuratType::whereNotNull('file_template')
            ->pluck('file_template');

        // Cari file "yatim piatu" — ada di storage tapi tidak di DB
        $orphans = $filesInStorage->diff($filesInDb);

        if ($orphans->isEmpty()) {
            $this->info('✅ Semua file template di storage masih terpakai. Storage bersih!');
            return self::SUCCESS;
        }

        $isForce = $this->option('force');
        $mode = $isForce ? '🗑️  HAPUS PERMANEN' : '🔍 DRY-RUN (Preview saja, tambah --force untuk hapus)';

        $this->line('');
        $this->line("Mode: <comment>{$mode}</comment>");
        $this->line('');

        $totalSize = 0;
        $this->table(
            ['No', 'Nama File', 'Ukuran'],
            $orphans->values()->map(function ($file, $i) use ($disk, $folder, &$totalSize) {
                $size = $disk->size("{$folder}/{$file}");
                $totalSize += $size;
                return [$i + 1, $file, $this->formatBytes($size)];
            })->toArray()
        );

        $this->line("Total: <error>{$orphans->count()} file</error> | Ukuran: <error>{$this->formatBytes($totalSize)}</error>");
        $this->line('');

        if ($isForce) {
            $deleted = 0;
            foreach ($orphans as $file) {
                if ($disk->delete("{$folder}/{$file}")) {
                    $deleted++;
                }
            }
            $this->info("✅ {$deleted} file berhasil dihapus. Storage dibersihkan!");
        } else {
            $this->warn('💡 Jalankan dengan flag --force untuk menghapus file di atas:');
            $this->line('   <comment>php artisan surat:clean-templates --force</comment>');
        }

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }
}
