# Changelog - 22 Mei 2026

## Added
- **Aset Modul Redesign:**
  - `database/migrations/2026_05_22_120000_redesign_aset_inventaris_and_create_aset_mutasi.php`: Migration untuk mendesain ulang skema database aset.
  - `app/Models/AsetInventaris.php`: Model aset utama dengan accessor dinamis (`saldo_kwantitas`, `saldo_nilai`, `perlu_penghapusan`).
  - `app/Models/AsetMutasi.php`: Model riwayat mutasi aset.
  - `app/Services/Aset/AsetInventarisService.php`: Service layer untuk mengkalkulasi saldo awal, mutasi periode berjalan, dan saldo akhir.
  - `app/Http/Requests/Aset/StoreAsetInventarisRequest.php`: Form request validasi pembuatan aset baru dan mutasi awalnya.
  - `app/Http/Requests/Aset/UpdateAsetInventarisRequest.php`: Form request validasi update data fisik aset.
  - `app/Http/Requests/Aset/StoreAsetMutasiRequest.php`: Form request validasi penambahan/pengurangan mutasi (lengkap dengan validasi cegah pengurangan melebihi saldo).
  - `resources/js/Pages/Tenant/Aset/TambahMutasi.jsx`: Halaman UI frontend untuk mencatat mutasi kurang/tambah pada aset.
  - `routes/tenant/aset.php`: Route file khusus untuk namespace aset.

## Changed
- `app/Http/Controllers/Tenant/Aset/AsetInventarisController.php`: Direfactor total menggunakan Service baru dan Form Requests.
- `app/Http/Controllers/Tenant/Aset/AsetMutasiController.php`: Direfactor menggunakan Form Requests dan penanganan redirect yang lebih mulus.
- `app/Models/AsetKategori.php` & `AsetBarang.php`: Penambahan Global Scope `ordered()` dan relasi yang lebih rapi.
- `resources/js/Pages/Tenant/Aset/Index.jsx`: Penyesuaian UI tabel menggunakan layout full-width dan data dinamis dari Service.
- `resources/js/Pages/Tenant/Aset/Create.jsx`: 
  - Redesign form menjadi full-width.
  - Menghapus input tanggal duplikat; input tahun perolehan dibuat fleksibel (bisa input tahun lama `min="1945"`).
  - Menambahkan indikator visual apakah aset masuk sebagai Saldo Awal atau Aset Baru berdasarkan auto-detect tanggal.
- `resources/js/Pages/Tenant/Aset/Edit.jsx`: Redesign UI form edit agar konsisten dengan halaman Create.

## Removed
- Membuang struktur controller aset lama yang berada langsung di root `Controllers/Tenant` dan `Services/AsetInventarisService.php` lama untuk menghindari duplikasi zombie files.
- Menghapus button "Tambah" ganda di tabel Index yang membingungkan pengguna.
