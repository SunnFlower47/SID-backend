# Changelog - 18 Mei 2026

## Fitur Baru (Keuangan Desa Fase 4 - Persetujuan BPD)
- **Model & Database:** Menambahkan tabel `peraturan_desas` dan model `PeraturanDesa` untuk mengelola alur persetujuan BPD. Model ini memiliki fungsi static `isLocked($tahun)` untuk proteksi data.
- **Controller:** Membuat `PeraturanDesaController` untuk menghandle logika CRUD, perubahan status pengajuan (Draft -> Diajukan -> Dibahas -> Disetujui/Ditolak), dan upload PDF dokumen resmi Perdes.
- **Proteksi Anggaran (Backend):** Memperbarui `AnggaranController`. Jika status Perdes untuk suatu tahun aktif sudah `disetujui`, maka proses tambah, edit, dan hapus Rekening APBDes akan ditolak otomatis oleh server (Soft Lock).
- **Proteksi UI (Frontend):** Menambahkan logika `is_locked` di halaman `APBDes/Index.jsx`. Jika terkunci, tombol "Tambah Rekening" serta aksi edit/hapus di baris tabel akan disembunyikan. Muncul peringatan Banner Hijau (Lock Banner) di atas tabel.
- **Menu Persetujuan BPD:** Membangun `Peraturan/Index.jsx` yang elegan untuk melihat pengajuan, melakukan update status, dan mengunggah dokumen Final PDF Perdes.
- **Dashboard:** Menambahkan Quick Action Link "Persetujuan BPD" di `Dashboard.jsx`.

## Perbaikan Bug (Bugfixes)
- **Form Focus Bug:** Memperbaiki isu di mana cursor input selalu lepas setelah mengetik 1 karakter. Hal ini terjadi karena komponen `<InputField>` sebelumnya di-define di dalam render komponen utama, sehingga selalu di-mount ulang setiap kali state form berubah. `<InputField>` telah dipindahkan ke luar komponen utama di file:
  - `APBDes/Create.jsx`
  - `APBDes/Edit.jsx`
  - `APBDes/AddExpenditure.jsx`
  - `APBDes/EditExpenditure.jsx`
  - `Proyek/Create.jsx`
