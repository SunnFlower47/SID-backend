# Changelog - 23 Mei 2026 (Malam)

## Desain Dashboard Lebih Kompak & Fitur Ekspor Excel Aset Desa (Multi-Tahun)

Kami telah menerapkan perbaikan tata letak pada halaman Dashboard agar lebih hemat ruang, padat informasi (kompak), serta memperluas fitur Ekspor Data Aset Desa dengan opsi filter berdasarkan tahun.

### Perubahan File & Struktur:

 1. **Front-end UI Components & Pages**:
   - `resources/js/Components/Shared/StatCard.jsx` [MODIFY]:
     - Menipiskan tampilan StatCard dalam mode `compact={true}` dengan mengurangi padding dari `p-3 sm:p-3.5` menjadi `p-2 sm:p-2.5`.
     - Mengubah ukuran border radius menjadi `rounded-xl` agar serasi dengan padding tipis.
     - Mengurangi ukuran box ikon dari `w-9 h-9` menjadi `w-8 h-8` dan ikon menjadi `w-4 h-4`.
     - Mengecilkan ukuran font label dan nilai utama menjadi lebih proporsional (`text-sm sm:text-base` untuk value).
     - Mengubah layout default (non-compact) `StatCard` menjadi horizontal (icon on left, label & value on right) dengan style yang terinspirasi dari layout kartu Domisili (`p-3 sm:p-4`, `w-8 h-8 sm:w-10 sm:h-10`, `text-xl sm:text-2xl`), sehingga seluruh menu mendapatkan tampilan stats card yang seragam, indah, dan konsisten.
     - Mendukung properti `title` sebagai alternatif untuk `label`.
   - `resources/js/Components/Domisili/DomisiliStats.jsx` [MODIFY]:
     - Merefaktorkan inline custom card layout menjadi komponen `<StatCard />` yang terstandarisasi untuk menjamin kesamaan ukuran dan tampilan.
   - `resources/js/Components/Mutasi/MutasiStats.jsx` [MODIFY]:
     - Merefaktorkan inline custom card layout menjadi komponen `<StatCard />`.
   - `resources/js/Components/Umkm/UmkmStats.jsx` [MODIFY]:
     - Merefaktorkan inline custom card layout menjadi komponen `<StatCard />`.
   - `resources/js/Components/BantuanSosial/BansosStats.jsx` [MODIFY]:
     - Merefaktorkan inline custom card layout menjadi komponen `<StatCard />`.
   - `resources/js/Components/Pengaduan/PengaduanStats.jsx` [MODIFY]:
     - Merefaktorkan inline custom card layout menjadi komponen `<StatCard />`.
   - `resources/js/Pages/Tenant/Dashboard/Index.jsx` [MODIFY]:
     - Menghilangkan bug duplikasi tag penutup layout di bagian bawah halaman.
     - Mengurangi jarak baris container utama dari `space-y-5 md:space-y-7` menjadi `space-y-4 md:space-y-5`.
     - Memperkecil gap grid Quick Access menjadi `gap-2.5`.
     - Mengubah styling tombol Quick Access Card agar lebih ringkas (padding `p-2 sm:p-2.5`, box ikon `w-7 h-7`, ikon `w-3.5 h-3.5`) untuk mencegah tombol terlalu besar.
     - Mengurangi ukuran dan padding banner peringatan surat pending.
     - Memperkecil padding tab selector utama (`p-2.5`, box ikon `w-6 h-6`, ikon `w-3 h-3`) serta mengurangi gap menjadi `gap-2.5` dan margin bawah menjadi `mb-4`.
     - Menyesuaikan gap antar layout tab panel dari `gap-6` menjadi `gap-4` dan gap stat card grid dari `gap-4` menjadi `gap-3`.

2. **Aset Desa Export Feature (Completed prior to design cleanup)**:
   - `app/Exports/AsetExport.php` [NEW]: Class ekspor Excel menggunakan Laravel Excel (Maatwebsite) untuk memformat data aset desa.
   - `resources/views/exports/aset.blade.php` [NEW]: Template HTML/Blade untuk formatting dokumen excel yang rapi dan profesional.
   - `app/Http/Controllers/Tenant/Admin/ExportController.php` [MODIFY]: Menambahkan metode ekspor data aset dengan validasi filter tahun dan tipe file.
   - `resources/js/Pages/Tenant/Export/Index.jsx` [MODIFY]: Menambahkan pilihan menu Ekspor Aset Desa dengan form filter Tahun dan tombol unduh.
   - `routes/tenant/admin.php` [MODIFY]: Mendaftarkan endpoint route `/admin/export/aset` untuk memproses download Excel.

3. **Dashboard Controller Error Resolution**:
   - `app/Http/Controllers/Tenant/DashboardController.php` [MODIFY]: Memperbaiki bug `ErrorException: Undefined array key "umkm"` dengan memetakan stats key 'umkm' secara aman di StatsService.
   - `app/Services/Kependudukan/VillageStatisticsService.php` [MODIFY]: Memastikan query stats UMKM mengembalikan default array jika data tidak ditemukan agar dashboard tidak crash.
