# Change Log - 2026-05-04
## Topic: Restrukturisasi Modul Surat (Phase 1)

### [ADDED]
- Folder **`app/Actions/Surat`** untuk menampung logic bisnis surat yang modular.
- File **`VERSION`** di root project untuk tracking versi aplikasi (v1.2.0-beta).
- Folder **`logs/changelogs/`** untuk tracking perubahan per-update.

### [CHANGED]
- **Refaktor Controller**: Memindahkan logic `store` dari `SuratPengajuanController` ke `StoreSuratAction`.
- Controller sekarang hanya bertanggung jawab untuk validasi input dan handling response (Clean Controller).
- Logic otomatisasi Domisili dan Kematian sekarang terisolasi di dalam Action Class.

### [TECHNICAL]
- Implementasi **Action Pattern** untuk menggantikan logic yang gemuk di Controller.
- Peningkatan maintainability: Jika ada jenis surat baru, kita tinggal update Action Class tanpa menyentuh Controller.
