<?php

namespace App\Http\Controllers\Tenant\Aset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Aset\StoreAsetMutasiRequest;
use App\Http\Requests\Aset\UpdateAsetMutasiRequest;
use App\Models\AsetInventaris;
use App\Models\AsetMutasi;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AsetMutasiController extends Controller
{
    /**
     * Form tambah mutasi ke aset existing.
     */
    public function create(Request $request, AsetInventaris $inventaris)
    {
        $inventaris->load('barang.kategori', 'mutasis');

        return Inertia::render('Tenant/Aset/TambahMutasi', [
            'inventaris' => [
                'id'              => $inventaris->id,
                'nama_display'    => $inventaris->nama_display,
                'satuan'          => $inventaris->satuan,
                'kondisi'         => $inventaris->kondisi,
                'barang'          => $inventaris->barang,
                'saldo_kwantitas' => $inventaris->saldo_kwantitas,
                'saldo_nilai'     => $inventaris->saldo_nilai,
            ],
            'tahun'    => (int) ($request->tahun    ?? now()->year),
            'semester' => (int) ($request->semester ?? (now()->month <= 6 ? 1 : 2)),
        ]);
    }

    /**
     * Simpan mutasi baru (tambah atau kurang).
     * Validasi saldo melebihi sudah ditangani di StoreAsetMutasiRequest::withValidator().
     */
    public function store(StoreAsetMutasiRequest $request, AsetInventaris $inventaris)
    {
        $validated = $request->validated();

        $beritaAcaraSuratId = null;
        $skSuratId          = null;

        if ($validated['jenis'] === 'kurang') {
            // Pembuat (user login)
            $user = auth()->user();
            $namaPengaju = $user ? $user->name : 'Admin Aset';
            $emailPengaju = $user ? $user->email : null;

            // Detail aset untuk disimpan di data_tambahan
            $dataTambahan = [
                'nama_aset'        => $inventaris->nama_display,
                'kode_barang'      => $inventaris->barang?->kode_barang,
                'satuan'           => $inventaris->satuan,
                'jumlah_dihapus'   => (float)$validated['kwantitas'],
                'nilai_dihapus'    => (float)$validated['nilai'],
                'alasan'           => $validated['keterangan'] ?? 'Tidak ada keterangan',
                'kondisi_baru'     => $validated['kondisi'] ?? $inventaris->kondisi,
                'tanggal_kejadian' => $validated['tanggal'],
            ];

            // 1. Buat Berita Acara
            $nomorBapa = \App\Models\DesaSetting::generateNomorSurat('BAPA');
            $beritaAcara = \App\Models\SuratPengajuan::create([
                'nik_pengaju'         => null,
                'nama_pengaju'        => $namaPengaju,
                'email_pengaju'       => $emailPengaju,
                'jenis_surat'         => 'berita-acara-penghapusan-aset',
                'nomor_surat'         => $nomorBapa,
                'keperluan'           => "Berita Acara Penghapusan Aset: " . $inventaris->nama_display,
                'tanggal_surat'       => $validated['tanggal'],
                'keterangan_tambahan' => "Dihapus sebanyak " . $validated['kwantitas'] . " " . $inventaris->satuan . " senilai Rp " . number_format($validated['nilai'], 0, ',', '.') . " karena: " . ($validated['keterangan'] ?? '-'),
                'data_tambahan'       => $dataTambahan,
                'status'              => 'selesai',
                'admin_id'            => auth()->id(),
                'approved_at'         => now(),
                'completed_at'        => now(),
            ]);
            $beritaAcaraSuratId = $beritaAcara->id;

            // 2. Buat SK Penghapusan
            $nomorSkpa = \App\Models\DesaSetting::generateNomorSurat('SKPA');
            $sk = \App\Models\SuratPengajuan::create([
                'nik_pengaju'         => null,
                'nama_pengaju'        => $namaPengaju,
                'email_pengaju'       => $emailPengaju,
                'jenis_surat'         => 'sk-penghapusan-aset',
                'nomor_surat'         => $nomorSkpa,
                'keperluan'           => "Surat Keputusan Penghapusan Aset: " . $inventaris->nama_display,
                'tanggal_surat'       => $validated['tanggal'],
                'keterangan_tambahan' => "SK Penghapusan aset " . $inventaris->nama_display . " sejumlah " . $validated['kwantitas'] . " " . $inventaris->satuan,
                'data_tambahan'       => $dataTambahan,
                'status'              => 'selesai',
                'admin_id'            => auth()->id(),
                'approved_at'         => now(),
                'completed_at'        => now(),
            ]);
            $skSuratId = $sk->id;
        }

        AsetMutasi::create([
            'aset_inventaris_id'    => $inventaris->id,
            'tahun'                 => $validated['tahun'],
            'semester'              => $validated['semester'],
            'tanggal'               => $validated['tanggal'],
            'jenis'                 => $validated['jenis'],
            'alasan_kurang'         => $validated['alasan_kurang'] ?? null,
            'kondisi'               => $validated['kondisi'] ?? null,
            'kwantitas'             => $validated['kwantitas'],
            'nilai'                 => $validated['nilai'],
            'keterangan'            => $validated['keterangan'] ?? null,
            'berita_acara_surat_id' => $beritaAcaraSuratId,
            'sk_surat_id'           => $skSuratId,
        ]);

        // Update kondisi fisik aset jika diisi
        if (!empty($validated['kondisi'])) {
            $inventaris->update(['kondisi' => $validated['kondisi']]);
        }

        $jenis    = $validated['jenis'] === 'tambah' ? 'Penambahan' : 'Pengurangan';
        $tahun    = $validated['tahun'];
        $semester = $validated['semester'];

        return redirect()
            ->route('aset.inventaris.index', compact('tahun', 'semester'))
            ->with('success', "{$jenis} aset \"{$inventaris->nama_display}\" berhasil dicatat.");
    }

    /**
     * Form edit mutasi existing.
     */
    public function edit(AsetMutasi $mutasi)
    {
        $mutasi->load('inventaris.barang.kategori');
        $inventaris = $mutasi->inventaris;

        return Inertia::render('Tenant/Aset/EditMutasi', [
            'mutasi' => $mutasi,
            'inventaris' => [
                'id'              => $inventaris->id,
                'nama_display'    => $inventaris->nama_display,
                'satuan'          => $inventaris->satuan,
                'kondisi'         => $inventaris->kondisi,
                'barang'          => $inventaris->barang,
                'saldo_kwantitas' => $inventaris->saldo_kwantitas,
                'saldo_nilai'     => $inventaris->saldo_nilai,
            ]
        ]);
    }

    /**
     * Simpan perubahan mutasi.
     */
    public function update(UpdateAsetMutasiRequest $request, AsetMutasi $mutasi)
    {
        $validated = $request->validated();
        $inventaris = $mutasi->inventaris;

        $mutasi->update([
            'tahun'         => $validated['tahun'],
            'semester'      => $validated['semester'],
            'tanggal'       => $validated['tanggal'],
            // jenis tidak diubah secara request form, tapi bisa di set
            'jenis'         => $validated['jenis'] ?? $mutasi->jenis,
            'alasan_kurang' => $validated['alasan_kurang'] ?? null,
            'kondisi'       => $validated['kondisi'] ?? null,
            'kwantitas'     => $validated['kwantitas'],
            'nilai'         => $validated['nilai'],
            'keterangan'    => $validated['keterangan'] ?? null,
        ]);

        // Update kondisi fisik aset jika diisi
        if (!empty($validated['kondisi'])) {
            $inventaris->update(['kondisi' => $validated['kondisi']]);
        }

        // Jika surat-surat perlu diperbarui?
        // Saat ini kita belum memfasilitasi regenerasi surat secara otomatis saat mutasi diedit.
        // Bisa ditambahkan keterangan di BAPA bahwa nilainya berubah. Tapi biarkan dulu.

        $jenis    = $mutasi->jenis === 'tambah' ? 'Penambahan' : 'Pengurangan';
        $tahun    = $validated['tahun'];
        $semester = $validated['semester'];

        return redirect()
            ->route('aset.inventaris.index', compact('tahun', 'semester'))
            ->with('success', "Mutasi {$jenis} aset \"{$inventaris->nama_display}\" berhasil diperbarui.");
    }

    /**
     * Hapus satu record mutasi.
     */
    public function destroy(AsetMutasi $mutasi)
    {
        $nama = $mutasi->inventaris?->nama_display ?? 'aset';

        // Hapus surat terkait
        if ($mutasi->berita_acara_surat_id) {
            $surat1 = \App\Models\SuratPengajuan::find($mutasi->berita_acara_surat_id);
            if ($surat1) {
                $surat1->delete();
            }
        }

        if ($mutasi->sk_surat_id) {
            $surat2 = \App\Models\SuratPengajuan::find($mutasi->sk_surat_id);
            if ($surat2) {
                $surat2->delete();
            }
        }

        $mutasi->delete();

        return back()->with('success', "Mutasi {$nama} berhasil dihapus.");
    }
}
