<?php

namespace App\Services\Keuangan;

use App\Models\Apbdes;
use App\Models\ProyekDesa;
use App\Models\HistoriPengeluaran;
use App\Models\PeraturanDesa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AnggaranService
{
    /**
     * Store annual budget
     */
    public function storeAnggaranTahunan(array $data): Apbdes
    {
        if (PeraturanDesa::isLocked($data['tahun'])) {
            throw ValidationException::withMessages([
                'error' => 'Gagal: APBDes Tahun ' . $data['tahun'] . ' sudah disahkan oleh BPD (Terkunci).'
            ]);
        }

        $data['realisasi']     = 0;
        $data['sisa_anggaran'] = $data['anggaran'];
        $data['status']        = 'disetujui';

        return Apbdes::create($data);
    }

    /**
     * Store expenditure and deduct budget
     */
    public function storePengeluaran(array $data, ?UploadedFile $fileBukti): HistoriPengeluaran
    {
        return DB::transaction(function () use ($data, $fileBukti) {
            $apbdes = Apbdes::findOrFail($data['apbdes_id']);
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;

            if ($data['jumlah'] > $sisaAnggaran) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah pengeluaran melebihi sisa anggaran (Rp ' . number_format($sisaAnggaran, 0, ',', '.') . ')'
                ]);
            }

            // Upload bukti file
            $filePath = null;
            $namaFileBukti = null;
            if ($fileBukti) {
                $namaFileBukti = $fileBukti->getClientOriginalName();
                $filePath      = $fileBukti->store('keuangan/bukti', 'public');
            }

            // Auto-generate no_bukti jika tidak diisi
            $tahunPengeluaran = (int) date('Y', strtotime($data['tanggal_pengeluaran']));
            $noBukti = ($data['no_bukti'] ?? null) ?: HistoriPengeluaran::generateNoBukti($tahunPengeluaran);

            $pengeluaran = HistoriPengeluaran::create([
                'nama_pengeluaran'    => $data['nama_pengeluaran'],
                'apbdes_id'           => $apbdes->id,
                'jumlah'              => $data['jumlah'],
                'tanggal_pengeluaran' => $data['tanggal_pengeluaran'],
                'keterangan'          => $data['keterangan'] ?? null,
                'user_id'             => auth()->id() ?? 1,
                'no_bukti'            => $noBukti,
                'jenis_bukti'         => $data['jenis_bukti'] ?? 'kwitansi',
                'file_bukti'          => $filePath,
                'nama_file_bukti'     => $namaFileBukti,
                'spj_status'          => 'belum',
                'pajak_ppn'           => $data['pajak_ppn'] ?? 0,
                'pajak_pph21'         => $data['pajak_pph21'] ?? 0,
                'pajak_pph22'         => $data['pajak_pph22'] ?? 0,
                'pajak_pph23'         => $data['pajak_pph23'] ?? 0,
            ]);

            // Update realisasi APBDes
            $apbdes->realisasi      += $data['jumlah'];
            $apbdes->sisa_anggaran   = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            return $pengeluaran;
        });
    }

    /**
     * Update expenditure and adjust budget
     */
    public function updatePengeluaran(HistoriPengeluaran $pengeluaran, array $data, ?UploadedFile $fileBukti): HistoriPengeluaran
    {
        return DB::transaction(function () use ($pengeluaran, $data, $fileBukti) {
            $apbdes = $pengeluaran->apbdes;

            // Calculate amount difference
            $oldJumlah    = $pengeluaran->jumlah;
            $newJumlah    = $data['jumlah'];
            $selisih      = $newJumlah - $oldJumlah;
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;

            if ($selisih > $sisaAnggaran) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah pengeluaran melebihi sisa anggaran (Rp ' . number_format((float)$sisaAnggaran, 0, ',', '.') . ')'
                ]);
            }

            // Handle file update
            $filePath      = $pengeluaran->file_bukti;
            $namaFileBukti = $pengeluaran->nama_file_bukti;

            $hapusFile = filter_var($data['hapus_file'] ?? false, FILTER_VALIDATE_BOOLEAN);
            if ($hapusFile && $filePath) {
                Storage::disk('public')->delete($filePath);
                $filePath = $namaFileBukti = null;
            }

            if ($fileBukti) {
                // Delete old file
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
                $namaFileBukti = $fileBukti->getClientOriginalName();
                $filePath      = $fileBukti->store('keuangan/bukti', 'public');
            }

            $pengeluaran->update([
                'nama_pengeluaran'    => $data['nama_pengeluaran'],
                'jumlah'              => $newJumlah,
                'tanggal_pengeluaran' => $data['tanggal_pengeluaran'],
                'keterangan'          => $data['keterangan'] ?? null,
                'no_bukti'            => $data['no_bukti'] ?? $pengeluaran->no_bukti,
                'jenis_bukti'         => $data['jenis_bukti'] ?? $pengeluaran->jenis_bukti,
                'file_bukti'          => $filePath,
                'nama_file_bukti'     => $namaFileBukti,
                'pajak_ppn'           => $data['pajak_ppn'] ?? $pengeluaran->pajak_ppn,
                'pajak_pph21'         => $data['pajak_pph21'] ?? $pengeluaran->pajak_pph21,
                'pajak_pph22'         => $data['pajak_pph22'] ?? $pengeluaran->pajak_pph22,
                'pajak_pph23'         => $data['pajak_pph23'] ?? $pengeluaran->pajak_pph23,
            ]);

            // Update APBDes realisasi
            $apbdes->realisasi      = $apbdes->realisasi + $selisih;
            $apbdes->sisa_anggaran  = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            return $pengeluaran;
        });
    }

    /**
     * Delete expenditure and restore budget
     */
    public function deletePengeluaran(HistoriPengeluaran $pengeluaran): void
    {
        DB::transaction(function () use ($pengeluaran) {
            $apbdes = $pengeluaran->apbdes;

            // Rollback realisasi APBDes
            $apbdes->realisasi      = $apbdes->realisasi - $pengeluaran->jumlah;
            $apbdes->sisa_anggaran  = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            // Delete bukti file from storage
            if ($pengeluaran->file_bukti) {
                Storage::disk('public')->delete($pengeluaran->file_bukti);
            }

            $pengeluaran->delete();
        });
    }

    /**
     * Store project and link to APBDes
     */
    public function storeProyek(array $data): ProyekDesa
    {
        return DB::transaction(function () use ($data) {
            // Get selected APBDes entry
            $apbdes = Apbdes::findOrFail($data['apbdes_id']);

            // Check if project budget exceeds remaining APBDes budget
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;
            if ($data['anggaran'] > $sisaAnggaran) {
                throw ValidationException::withMessages([
                    'anggaran' => 'Anggaran proyek melebihi sisa anggaran APBDes yang tersedia (Rp ' . number_format((float)$sisaAnggaran, 0, ',', '.') . ')'
                ]);
            }

            // Create project
            $proyekData = [
                'nama_proyek'      => $data['nama_proyek'],
                'deskripsi'        => $data['deskripsi'] ?? null,
                'jenis'            => $data['jenis'],
                'anggaran'         => $data['anggaran'],
                'tanggal_mulai'    => $data['tanggal_mulai'],
                'tanggal_selesai'  => $data['tanggal_selesai'] ?? null,
                'lokasi'           => $data['lokasi'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'kontraktor'       => $data['kontraktor'] ?? null,
                'realisasi'        => 0,
                'status'           => 'perencanaan',
                'progress'         => 0,
                'apbdes_id'        => $apbdes->id
            ];

            $proyek = ProyekDesa::create($proyekData);

            // Update APBDes realisasi
            $apbdes->realisasi += $data['anggaran'];
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            return $proyek;
        });
    }

    /**
     * Update project realization and sync with APBDes
     */
    public function updateRealisasiProyek(ProyekDesa $proyek, array $data): array
    {
        return DB::transaction(function () use ($proyek, $data) {
            $oldRealisasi = $proyek->realisasi;
            $newRealisasi = $data['realisasi'];
            $selisih = $newRealisasi - $oldRealisasi;

            // Update project realization
            $proyek->realisasi = $newRealisasi;
            $proyek->progress = $proyek->anggaran > 0 ? round(($newRealisasi / $proyek->anggaran) * 100) : 0;

            // Update status based on progress
            if ($proyek->progress >= 100) {
                $proyek->status = 'selesai';
            } elseif ($proyek->progress > 0) {
                $proyek->status = 'berjalan';
            }

            $proyek->save();

            // Find and update related APBDes (using same logic as original controller)
            $apbdes = Apbdes::where('nama_rekening', 'LIKE', '%' . $proyek->nama_proyek . '%')
                ->where('jenis', 'belanja')
                ->first();

            if ($apbdes) {
                $apbdes->realisasi += $selisih;
                $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
                $apbdes->save();
            }

            return [
                'realisasi'      => $proyek->realisasi,
                'progress'       => $proyek->progress,
                'status'         => $proyek->status,
                'apbdes_updated' => (bool)$apbdes
            ];
        });
    }

    /**
     * Update APBDes
     */
    public function updateApbdes(Apbdes $apbdes, array $data): Apbdes
    {
        return DB::transaction(function () use ($apbdes, $data) {
            if (PeraturanDesa::isLocked($apbdes->tahun)) {
                throw ValidationException::withMessages([
                    'error' => 'Gagal: APBDes Tahun ' . $apbdes->tahun . ' sudah disahkan oleh BPD (Terkunci).'
                ]);
            }

            // Check if anggaran is being reduced below current realisasi
            if ($data['anggaran'] < $apbdes->realisasi) {
                throw ValidationException::withMessages([
                    'anggaran' => 'Anggaran tidak boleh kurang dari realisasi yang sudah ada (Rp ' . number_format((float)$apbdes->realisasi, 0, ',', '.') . ')'
                ]);
            }

            $apbdes->update([
                'kode_rekening' => $data['kode_rekening'],
                'nama_rekening' => $data['nama_rekening'],
                'jenis'         => $data['jenis'],
                'sumber_dana'   => $data['sumber_dana'],
                'anggaran'      => $data['anggaran'],
                'keterangan'    => $data['keterangan'] ?? null,
                'sisa_anggaran' => $data['anggaran'] - $apbdes->realisasi,
            ]);

            return $apbdes;
        });
    }

    /**
     * Delete APBDes
     */
    public function deleteApbdes(Apbdes $apbdes): void
    {
        if (PeraturanDesa::isLocked($apbdes->tahun)) {
            throw ValidationException::withMessages([
                'error' => 'Gagal: APBDes Tahun ' . $apbdes->tahun . ' sudah disahkan oleh BPD (Terkunci).'
            ]);
        }

        DB::transaction(function () use ($apbdes) {
            // Check if there are any expenditures for this APBDes
            if ($apbdes->historiPengeluarans()->count() > 0) {
                throw ValidationException::withMessages([
                    'error' => 'Tidak dapat menghapus rekening APBDes yang sudah memiliki histori pengeluaran.'
                ]);
            }

            // Check if there are any projects linked to this APBDes
            if ($apbdes->proyekDesas()->count() > 0) {
                throw ValidationException::withMessages([
                    'error' => 'Tidak dapat menghapus rekening APBDes yang sudah terhubung dengan proyek desa.'
                ]);
            }

            $apbdes->delete();
        });
    }
}
