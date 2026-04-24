<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Penduduk;
use Carbon\Carbon;

class CheckDataConsistency extends Command
{
    protected $signature = 'data:check-consistency';
    protected $description = 'Check data consistency and report inconsistencies';

    public function handle()
    {
        $this->info('🔍 Memeriksa konsistensi data...');
        $this->newLine();

        $issues = [];
        $totalPenduduk = Penduduk::count();
        $this->info("Total penduduk: {$totalPenduduk}");
        $this->newLine();

        // 1. Check age vs marital status
        $this->info('1. Memeriksa konsistensi umur vs status perkawinan...');
        $ageMaritalIssues = $this->checkAgeVsMaritalStatus();
        $issues = array_merge($issues, $ageMaritalIssues);

        // 2. Check gender vs family position
        $this->info('2. Memeriksa konsistensi jenis kelamin vs kedudukan keluarga...');
        $genderFamilyIssues = $this->checkGenderVsFamilyPosition();
        $issues = array_merge($issues, $genderFamilyIssues);

        // 3. Check family position vs marital status
        $this->info('3. Memeriksa konsistensi kedudukan keluarga vs status perkawinan...');
        $familyMaritalIssues = $this->checkFamilyPositionVsMaritalStatus();
        $issues = array_merge($issues, $familyMaritalIssues);

        // 4. Check duplicate NIK
        $this->info('4. Memeriksa duplikasi NIK...');
        $duplicateNikIssues = $this->checkDuplicateNik();
        $issues = array_merge($issues, $duplicateNikIssues);

        // 5. Check invalid dates
        $this->info('5. Memeriksa tanggal lahir yang tidak valid...');
        $dateIssues = $this->checkInvalidDates();
        $issues = array_merge($issues, $dateIssues);

        // 6. Check empty required fields
        $this->info('6. Memeriksa field wajib yang kosong...');
        $emptyFieldIssues = $this->checkEmptyRequiredFields();
        $issues = array_merge($issues, $emptyFieldIssues);

        // Summary
        $this->newLine();
        $this->info('📊 RINGKASAN KONSISTENSI DATA');
        $this->info('==============================');
        $this->info("Total penduduk: {$totalPenduduk}");
        $this->info("Total masalah ditemukan: " . count($issues));

        if (count($issues) > 0) {
            $this->newLine();
            $this->warn('⚠️  MASALAH YANG DITEMUKAN:');
            $this->warn('==========================');

            foreach ($issues as $index => $issue) {
                $this->line(($index + 1) . ". {$issue}");
            }

            $this->newLine();
            $this->warn('💡 REKOMENDASI:');
            $this->warn('===============');
            $this->warn('1. Periksa data yang bermasalah di atas');
            $this->warn('2. Lakukan perbaikan data melalui interface admin');
            $this->warn('3. Jalankan command ini lagi untuk memverifikasi perbaikan');
        } else {
            $this->newLine();
            $this->info('✅ SEMUA DATA KONSISTEN!');
            $this->info('Tidak ditemukan masalah konsistensi data.');
        }

        return Command::SUCCESS;
    }

    private function checkAgeVsMaritalStatus()
    {
        $issues = [];

        $penduduks = Penduduk::whereNotNull('tanggal_lahir')
            ->whereNotNull('status_perkawinan')
            ->get();

        foreach ($penduduks as $penduduk) {
            $usia = Carbon::parse($penduduk->tanggal_lahir)->age;

            // Anak di bawah 10 tahun tidak boleh menikah
            if ($usia < 10 && in_array($penduduk->status_perkawinan, ['Kawin', 'Cerai Hidup', 'Cerai Mati'])) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Anak berusia {$usia} tahun memiliki status '{$penduduk->status_perkawinan}'";
            }

            // Anak di bawah 16 tahun tidak boleh menikah
            if ($usia < 16 && in_array($penduduk->status_perkawinan, ['Kawin', 'Cerai Hidup', 'Cerai Mati'])) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Anak berusia {$usia} tahun belum memenuhi syarat usia pernikahan";
            }

            // Anak di bawah 12 tahun tidak boleh cerai
            if ($usia < 12 && in_array($penduduk->status_perkawinan, ['Cerai Hidup', 'Cerai Mati'])) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Anak berusia {$usia} tahun tidak mungkin sudah cerai";
            }
        }

        $this->info("   Ditemukan " . count($issues) . " masalah umur vs status perkawinan");
        return $issues;
    }

    private function checkGenderVsFamilyPosition()
    {
        $issues = [];

        $penduduks = Penduduk::whereNotNull('jenis_kelamin')
            ->whereNotNull('kedudukan_keluarga')
            ->get();

        foreach ($penduduks as $penduduk) {
            // Istri harus perempuan
            if ($penduduk->kedudukan_keluarga === 'Istri' && $penduduk->jenis_kelamin !== 'P') {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Kedudukan 'Istri' untuk jenis kelamin '{$penduduk->jenis_kelamin}'";
            }
        }

        $this->info("   Ditemukan " . count($issues) . " masalah jenis kelamin vs kedudukan keluarga");
        return $issues;
    }

    private function checkFamilyPositionVsMaritalStatus()
    {
        $issues = [];

        $penduduks = Penduduk::whereNotNull('kedudukan_keluarga')
            ->whereNotNull('status_perkawinan')
            ->get();

        foreach ($penduduks as $penduduk) {
            // Istri harus sudah menikah
            if ($penduduk->kedudukan_keluarga === 'Istri' && $penduduk->status_perkawinan !== 'Kawin') {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Kedudukan 'Istri' dengan status '{$penduduk->status_perkawinan}'";
            }

            // Anak tidak wajar cerai
            if ($penduduk->kedudukan_keluarga === 'Anak' && in_array($penduduk->status_perkawinan, ['Cerai Hidup', 'Cerai Mati'])) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Kedudukan 'Anak' dengan status '{$penduduk->status_perkawinan}'";
            }

            // Cucu tidak wajar cerai
            if ($penduduk->kedudukan_keluarga === 'Cucu' && in_array($penduduk->status_perkawinan, ['Cerai Hidup', 'Cerai Mati'])) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Kedudukan 'Cucu' dengan status '{$penduduk->status_perkawinan}'";
            }
        }

        $this->info("   Ditemukan " . count($issues) . " masalah kedudukan keluarga vs status perkawinan");
        return $issues;
    }

    private function checkDuplicateNik()
    {
        $issues = [];

        $duplicates = Penduduk::selectRaw('nik, COUNT(*) as count')
            ->groupBy('nik')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $penduduks = Penduduk::where('nik', $duplicate->nik)->get();
            $names = $penduduks->pluck('nama')->implode(', ');
            $issues[] = "NIK {$duplicate->nik} duplikat: {$names}";
        }

        $this->info("   Ditemukan " . count($issues) . " NIK duplikat");
        return $issues;
    }

    private function checkInvalidDates()
    {
        $issues = [];

        $penduduks = Penduduk::whereNotNull('tanggal_lahir')->get();

        foreach ($penduduks as $penduduk) {
            try {
                $tanggalLahir = Carbon::parse($penduduk->tanggal_lahir);

                // Tanggal lahir di masa depan
                if ($tanggalLahir->isFuture()) {
                    $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Tanggal lahir di masa depan ({$penduduk->tanggal_lahir})";
                }

                // Tanggal lahir terlalu lama (lebih dari 120 tahun)
                if ($tanggalLahir->age > 120) {
                    $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Umur terlalu tua ({$tanggalLahir->age} tahun)";
                }

                // Tanggal lahir sebelum 1900
                if ($tanggalLahir->year < 1900) {
                    $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Tanggal lahir sebelum 1900 ({$penduduk->tanggal_lahir})";
                }
            } catch (\Exception $e) {
                $issues[] = "ID {$penduduk->id} ({$penduduk->nama}): Format tanggal lahir tidak valid ({$penduduk->tanggal_lahir})";
            }
        }

        $this->info("   Ditemukan " . count($issues) . " masalah tanggal lahir");
        return $issues;
    }

    private function checkEmptyRequiredFields()
    {
        $issues = [];

        $requiredFields = ['nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'status_perkawinan', 'kedudukan_keluarga', 'alamat', 'rt', 'rw'];

        foreach ($requiredFields as $field) {
            $count = Penduduk::whereNull($field)->orWhere($field, '')->count();
            if ($count > 0) {
                $issues[] = "Field '{$field}' kosong pada {$count} data";
            }
        }

        $this->info("   Ditemukan " . count($issues) . " masalah field wajib kosong");
        return $issues;
    }
}
