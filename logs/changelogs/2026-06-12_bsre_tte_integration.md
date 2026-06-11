# Changelog - 12 Juni 2026
## Integrasi Tanda Tangan Elektronik (TTE) BSrE

**Fitur Baru & Penyempurnaan:**
1. **Integrasi API BSrE v2:** Mengimplementasikan `BsreService` yang berkomunikasi langsung dengan API Esign Client v2 (endpoint `/api/v2/user/check/status` dan `/api/v2/sign/pdf`).
2. **Pemindahan Konfigurasi ke `.env`:** Memindahkan `BSRE_API_URL`, `BSRE_USERNAME`, dan `BSRE_PASSWORD` dari database ke file `.env` (melalui `config/services.php`) demi alasan keamanan dan arsitektur *multi-environment*. Default URL otomatis mengarah ke `https://api-bsre.bssn.go.id`.
3. **Database Migration untuk ENUM TTE:** Membuat *database migration* (`add_tte_to_penandatangan_enum`) untuk mengubah batasan ENUM MySQL pada tabel `surat_pengajuans` di kolom `penandatangan` agar mendukung opsi `'tte'`.
4. **Validasi Edit Surat Pengajuan:** Memperbaiki validasi fungsi `update()` pada `SuratPengajuanController` yang sebelumnya menolak *request* `'tte'` saat melakukan *edit* tanda tangan.
5. **Auto-fill NIK Pejabat yang Cerdas:** Fitur TTE kini secara otomatis akan mencari NIK Kepala Desa dari tabel `StrukturDesa` (dimana `kategori = 'kepala_desa'` dan status aktif). NIK ini langsung diinjeksikan secara otomatis ke *Frontend* (Panel TTE) sehingga tidak perlu lagi mengetik manual atau menyetel di pengaturan khusus.
6. **Health Check Koneksi Otomatis:** Panel UI TTE (`TtePanel.jsx`) dilengkapi fitur "Cek Status" (Health Check) yang akan langsung melakukan ping ke server Kominfo/BSSN untuk memastikan koneksi firewall VPS, verifikasi NIK, dan aktivasi sertifikat beroperasi secara normal sebelum menandatangani.
7. **Pipa Konversi Word to PDF (`DocxToPdfService`):** Menggunakan *LibreOffice Headless* yang secara otomatis terdeteksi *path*-nya oleh sistem (mendukung deteksi via `which` command Linux maupun *path* statis Windows/Linux). Sistem ini merubah dokumen rtf/docx hasil *generate* PHPWord menjadi PDF *on-the-fly* sebelum diserahkan ke BSrE API.
