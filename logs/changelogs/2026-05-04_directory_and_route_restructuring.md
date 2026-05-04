# Change Log - 2026-05-04
## Topic: Directory & Route Restructuring (Phase 2)

### [ADDED]
- Folder **`routes/tenant/`**:
    - `kependudukan.php`: Rute Penduduk, KK, Mutasi, Domisili.
    - `pelayanan.php`: Rute Surat, Bansos, Pengaduan, Konten.
    - `keuangan.php`: Rute Anggaran, Transparansi.
    - `laporan.php`: Rute Statistik, Laporan.
    - `admin.php`: Rute System Admin, Import, Export.
- Sub-folder di **`app/Http/Controllers/Tenant/`**:
    - `Kependudukan/`, `Pelayanan/`, `Keuangan/`, `Admin/`, `Konten/`, `Laporan/`.

### [CHANGED]
- **`web.php`**: Sekarang menjadi file induk yang bersih (Clean Entry Point) yang memanggil rute modular via `require`.
- **Controller Namespaces**: Semua Controller diupdate namespace-nya sesuai dengan folder baru mereka.
- **Route Imports**: Semua file rute diupdate untuk mengimpor Controller dari namespace yang baru.

### [DELETED]
- `app/Http/Controllers/Tenant/WilayahController_fix.php` (Cleanup file sampah).

### [IMPACT]
- Navigasi file lebih cepat (Domain-Driven).
- `web.php` tidak lagi raksasa (Maintainable).
- Arsitektur project lebih profesional dan skalabel.
