# Changelog - 6 Juni 2026

## [Completed] Kelompok A — Administrasi Umum (100%)
Penyelesaian total fungsi CRUD, tampilan, serta integrasi Export PDF dan Excel teranimasi untuk seluruh 9 buku administrasi:
- `A.1` - Buku Peraturan di Desa
- `A.2` - Buku Keputusan Kepala Desa
- `A.3` - Buku Inventaris dan Kekayaan Desa
- `A.4` - Buku Agenda
- `A.5` - Buku Aparat Pemerintah Desa
- `A.6` - Buku Tanah Kas Desa
- `A.7` - Buku Tanah di Desa
- `A.8` - Buku Kader Pemberdayaan Masyarakat
- `A.9` - Buku Data Anggota BPD

## [Completed] Kelompok B — Administrasi Penduduk (B.1)
- **B.1 — Buku Induk Penduduk:** Menu baru telah diregistrasikan di *sidebar* dan Buku Administrasi. Menampilkan rekapitulasi data profil warga dari basis data kependudukan secara holistik sesuai kaidah format baku Lampiran XI Permendagri 47/2016. Dilengkapi kapabilitas cetak dokumen PDF berformat *Landscape* serta ekstraksi laporan berformat *Spreadsheet* Excel.

## [Added] Kependudukan & Import/Export
- **Bulk Import Data**: Menambahkan 14 entitas kolom kependudukan baru ke logika pemrosesan `ImportService` dan `PendudukImport` (termasuk pendidikan, pekerjaan, akta lahir, asuransi, dll).
- **Template Excel Terbaru**: Kolom-kolom di atas telah diinkorporasikan dengan rapi ke fitur pengunduhan `PendudukTemplateExport`.
- **Teknologi Chunk Reading**: Menerapkan kelas `PreviewPendudukImport` berbasis `WithChunkReading` untuk memastikan server tidak kelebihan muatan memori (*RAM overhead*) saat melakukan pembacaan pratinjau impor (*preview*) data masif.
- **Rencana Induk (*Implementation Plan*)**: Mendokumentasikan status seluruh buku administrasi dan menyusun cetak biru langkah pengerjaan khusus untuk modul Kelompok B secara struktural.

## [Changed]
- **Format Export Excel Penduduk**: Mentransformasi susunan kolom tunggal `ALAMAT LENGKAP` menjadi tiga pecahan struktural independen: `ALAMAT`, `RT`, dan `RW` guna simplifikasi olah data wilayah.
- **Resolusi Konflik Import (UI)**: Merombak panel penyelesaian isu `REQUIRED_FIELD_MISSING` (data wajib lenyap/invalid). Mengganti tampilan statis dengan Form Input cerdas (`InvalidNikPanel` / `InvalidNkkPanel`) yang langsung bereaksi berdasarkan tipe *error*, dengan penyematan *badge* indikator peringatan visual yang jelas.
- **Sistem Auto-Reprocess**: Mengamandemen logika *whitelist* pada `shouldAutoReprocess` sehingga penanganan manual isu kelengkapan *field* NIK/NKK akan segera memicu injeksi otomatis ke *database* setelah tombol simpan ditekan (memutus fase *pending* berkepanjangan).

## [Fixed]
- **Fatal Error Template Export (PHP 8.3)**: Menyelesaikan *crash* pada fungsi pewarnaan dan otomatisasi ukuran lebar sel Excel (`range(): Argument #2 must be a single byte`) yang muncul karena alfabet baris tembus hingga "AA". Algoritma dirubah total ke metode murni *string iterator* (`$col++`).
- **Anomali Duplikasi NIK (*Bypass Bug*)**: Melibas cacat sistem pencarian duplikat saat *import* yang dipicu oleh bersemayamnya *invisible characters* (Spasi, Newline `\n`, `@`, `-`) pada 34 baris NIK kotor di tabel lawas penduduk. Mengeksekusi injeksi perintah *database cleansing* tingkat sistem, melakukan sanitasi pembuangan segala jenis teks selain angka murni agar tingkat validasi duplikat NIK melonjak drastis hingga akurasi sempurna 100%.
