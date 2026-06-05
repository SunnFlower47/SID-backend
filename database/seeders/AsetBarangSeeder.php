<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsetBarangSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil ID kategori dari database
        $kategoris = DB::table('aset_kategoris')->pluck('id', 'kode');

        $tanah          = $kategoris['2'] ?? null;
        $peralatanMesin = $kategoris['3'] ?? null;
        $gedung         = $kategoris['4'] ?? null;
        $jalan          = $kategoris['5'] ?? null;
        $asetLainnya    = $kategoris['6'] ?? null;

        $barangs = [];

        // ── Golongan 2: Tanah ─────────────────────────────────────────────────
        if ($tanah) {
            $barangs = array_merge($barangs, [
                ['aset_kategori_id' => $tanah, 'kode_barang' => '2.01.01.00', 'nama_barang' => 'Tanah Aset Desa',         'satuan_default' => 'm²'],
                ['aset_kategori_id' => $tanah, 'kode_barang' => '2.01.02.00', 'nama_barang' => 'Tanah Sawah Desa',        'satuan_default' => 'm²'],
                ['aset_kategori_id' => $tanah, 'kode_barang' => '2.01.03.00', 'nama_barang' => 'Tanah Lapangan / Taman',  'satuan_default' => 'm²'],
                ['aset_kategori_id' => $tanah, 'kode_barang' => '2.01.04.00', 'nama_barang' => 'Tanah Pemakaman Desa',    'satuan_default' => 'm²'],
                ['aset_kategori_id' => $tanah, 'kode_barang' => '2.01.05.00', 'nama_barang' => 'Tanah Bangunan (Kantor)', 'satuan_default' => 'm²'],
            ]);
        }

        // ── Golongan 3: Peralatan & Mesin ─────────────────────────────────────
        if ($peralatanMesin) {
            $barangs = array_merge($barangs, [
                // 3.01 — Alat Besar
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.01.01.00', 'nama_barang' => 'Generator / Genset',       'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.01.02.00', 'nama_barang' => 'Pompa Air',                'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.01.03.00', 'nama_barang' => 'Traktor / Alat Pertanian', 'satuan_default' => 'unit'],

                // 3.02 — Alat Angkutan (KENDARAAN)
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.02.01.00', 'nama_barang' => 'Kendaraan Roda 2 (Motor Dinas)',  'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.02.02.00', 'nama_barang' => 'Kendaraan Roda 4 (Mobil Dinas)', 'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.02.03.00', 'nama_barang' => 'Kendaraan Roda 6+ (Truck/Elf)', 'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.02.04.00', 'nama_barang' => 'Perahu / Sampan Dinas',         'satuan_default' => 'unit'],

                // 3.05 — Alat Kantor & Rumah Tangga
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.01.00', 'nama_barang' => 'Komputer / PC',            'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.02.00', 'nama_barang' => 'Laptop / Notebook',        'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.03.00', 'nama_barang' => 'Printer',                  'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.04.00', 'nama_barang' => 'Scanner',                  'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.05.00', 'nama_barang' => 'Proyektor',                'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.06.00', 'nama_barang' => 'Televisi',                 'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.07.00', 'nama_barang' => 'Mesin Fotocopy',           'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.08.00', 'nama_barang' => 'Mesin Ketik',              'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.09.00', 'nama_barang' => 'Meja Kerja / Meja Rapat',  'satuan_default' => 'buah'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.10.00', 'nama_barang' => 'Kursi Kerja / Kursi Tamu', 'satuan_default' => 'buah'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.11.00', 'nama_barang' => 'Lemari Arsip / Filing Cabinet', 'satuan_default' => 'buah'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.12.00', 'nama_barang' => 'Lemari Kayu',              'satuan_default' => 'buah'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.13.00', 'nama_barang' => 'Brankas / Khasanah',       'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.14.00', 'nama_barang' => 'AC / Air Conditioner',     'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.15.00', 'nama_barang' => 'Kipas Angin',              'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.16.00', 'nama_barang' => 'Kulkas / Refrigerator',    'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.17.00', 'nama_barang' => 'Sound System / Speaker',   'satuan_default' => 'set'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.18.00', 'nama_barang' => 'Tenda / Terop',            'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.19.00', 'nama_barang' => 'Kursi Plastik',            'satuan_default' => 'buah'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.05.20.00', 'nama_barang' => 'Meja Lipat',               'satuan_default' => 'buah'],

                // 3.06 — Alat Studio & Komunikasi
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.06.01.00', 'nama_barang' => 'Kamera / DSLR',           'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.06.02.00', 'nama_barang' => 'Handy Talky / Radio',     'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.06.03.00', 'nama_barang' => 'Telepon / PABX',         'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.06.04.00', 'nama_barang' => 'Megaphone / TOA',        'satuan_default' => 'unit'],

                // 3.07 — Alat Kesehatan
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.07.01.00', 'nama_barang' => 'Timbangan / Alat Posyandu', 'satuan_default' => 'unit'],
                ['aset_kategori_id' => $peralatanMesin, 'kode_barang' => '3.07.02.00', 'nama_barang' => 'Kotak P3K / Obat-obatan',   'satuan_default' => 'set'],
            ]);
        }

        // ── Golongan 4: Gedung & Bangunan ─────────────────────────────────────
        if ($gedung) {
            $barangs = array_merge($barangs, [
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.01.00', 'nama_barang' => 'Gedung Kantor Desa',          'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.02.00', 'nama_barang' => 'Aula / Balai Pertemuan',      'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.03.00', 'nama_barang' => 'Pos Kamling / Pos Jaga',      'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.04.00', 'nama_barang' => 'Gedung Sekolah / PAUD',       'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.05.00', 'nama_barang' => 'Gedung Posyandu',             'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.01.06.00', 'nama_barang' => 'Gedung BUMDes',               'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.02.01.00', 'nama_barang' => 'Rumah Dinas Kepala Desa',     'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.03.01.00', 'nama_barang' => 'MCK / Toilet Umum',           'satuan_default' => 'unit'],
                ['aset_kategori_id' => $gedung, 'kode_barang' => '4.03.02.00', 'nama_barang' => 'Menara Air / Bak Penampungan', 'satuan_default' => 'unit'],
            ]);
        }

        // ── Golongan 5: Jalan, Jaringan & Irigasi ─────────────────────────────
        if ($jalan) {
            $barangs = array_merge($barangs, [
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.01.01.00', 'nama_barang' => 'Jalan Desa (Aspal)',           'satuan_default' => 'm'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.01.02.00', 'nama_barang' => 'Jalan Desa (Beton / Paving)',  'satuan_default' => 'm'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.01.03.00', 'nama_barang' => 'Jalan Lingkungan',             'satuan_default' => 'm'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.02.01.00', 'nama_barang' => 'Jembatan',                     'satuan_default' => 'unit'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.02.02.00', 'nama_barang' => 'Gorong-gorong',                'satuan_default' => 'unit'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.03.01.00', 'nama_barang' => 'Saluran Irigasi',              'satuan_default' => 'm'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.03.02.00', 'nama_barang' => 'Bendung / Dam',                'satuan_default' => 'unit'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.04.01.00', 'nama_barang' => 'Jaringan Listrik / LPJU',      'satuan_default' => 'titik'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.04.02.00', 'nama_barang' => 'Jaringan Air Bersih / PDAM',   'satuan_default' => 'm'],
                ['aset_kategori_id' => $jalan, 'kode_barang' => '5.04.03.00', 'nama_barang' => 'Jaringan Internet / WiFi',     'satuan_default' => 'titik'],
            ]);
        }

        // ── Golongan 6: Aset Tetap Lainnya ────────────────────────────────────
        if ($asetLainnya) {
            $barangs = array_merge($barangs, [
                ['aset_kategori_id' => $asetLainnya, 'kode_barang' => '6.01.01.00', 'nama_barang' => 'Buku Perpustakaan / Koleksi Buku', 'satuan_default' => 'expl'],
                ['aset_kategori_id' => $asetLainnya, 'kode_barang' => '6.02.01.00', 'nama_barang' => 'Pakaian Dinas / Seragam',          'satuan_default' => 'stel'],
                ['aset_kategori_id' => $asetLainnya, 'kode_barang' => '6.03.01.00', 'nama_barang' => 'Hewan Ternak Desa',                'satuan_default' => 'ekor'],
                ['aset_kategori_id' => $asetLainnya, 'kode_barang' => '6.04.01.00', 'nama_barang' => 'Alat Kesenian / Gamelan',          'satuan_default' => 'set'],
                ['aset_kategori_id' => $asetLainnya, 'kode_barang' => '6.04.02.00', 'nama_barang' => 'Peralatan Olahraga',               'satuan_default' => 'set'],
            ]);
        }

        // Tambahkan timestamps dan hindari duplikat berdasarkan kode_barang
        $now = now();
        foreach ($barangs as $barang) {
            DB::table('aset_barangs')->updateOrInsert(
                ['kode_barang' => $barang['kode_barang']],
                array_merge($barang, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        $this->command->info('✅ AsetBarangSeeder: ' . count($barangs) . ' jenis barang berhasil diisi.');
    }
}
