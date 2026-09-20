# Penyelesaian Migrasi Storage ke MinIO (S3), Integrasi Profil Desa Dinamis, & Rilis Versi 1.14.1 / 1.0.1

Kita telah menyelesaikan proses refaktorisasi penyimpanan data (MinIO/S3), pembersihan kode surat, serta perluasan pengaturan profil desa terintegrasi pada Admin Panel & Frontend warga.

## 1. Perbaikan Bug URL Gambar Berita & Peraturan Desa
*   **Masalah**: Unggahan gambar berita menggunakan disk `s3` (Cloudflare R2), tetapi ketika diakses di panel admin maupun web warga, gambar tersebut me-resolve ke URL lokal `/storage/berita/...` karena default `FILESYSTEM_DISK` pada `.env` server tidak selalu diatur ke `s3`.
*   **Solusi**:
    *   Memperbarui accessor `getImageUrlAttribute` di `Berita.php` dan `getFileDokumenUrlAttribute` di `PeraturanDesa.php` agar memeriksa keberadaan file secara bertingkat: pertama pada default disk, kemudian disk `s3` (R2), lalu disk `public` (lokal). Hal ini menjamin URL file yang di-resolve selalu valid apa pun nilai `FILESYSTEM_DISK` di `.env` Anda.
    *   Memperbarui `BeritaEksternalController.php` agar memanggil properti accessor `$berita->image_url` alih-alih `Storage::url($berita->gambar)` secara langsung, memastikan integrasi berita gabungan di web warga juga memuat URL gambar secara tepat.

## 2. Integrasi TikTok & Fitur Salin Link Media Sosial
*   **Custom Brand Icons di Admin Panel**: Mengganti ikon generik `Share2` pada tab **Media Sosial** di panel admin dengan brand-specific asset yang telah disediakan:
    *   Facebook: `/assets/icon/facebook/facebook-new-2019-seeklogo-2.svg`
    *   Instagram: `/assets/icon/instagaram/instagram-new-2016-seeklogo.png`
    *   YouTube: `/assets/icon/youtube/youtube-2017-icon-seeklogo-3.svg`
    *   WhatsApp: `/assets/icon/whatsapp/whatsapp-icon-seeklogo.svg`
    *   TikTok: `/assets/icon/tiktok/tiktok-seeklogo.png`
*   **Tombol "Click to Copy"**: Menambahkan tombol salin (`Copy` dari `lucide-react`) di samping kanan setiap input URL media sosial. Ketika ditekan, input URL langsung disalin ke clipboard admin dengan transisi ikon centang hijau (`Check`) selama 2 detik sebagai konfirmasi visual sukses.

## 3. Penambahan Key Konfigurasi & Kustomisasi Tema/SEO Dinamis
Kami telah menambahkan dan menyelaraskan 8 kunci konfigurasi agar dapat dikonfigurasi langsung dari admin panel:
*   **Informasi Umum Tab**:
    *   **Nama Kepala Desa** (`nama_kepala_desa`): Mengatur nama Kades saat ini (dimuat dinamis dari Struktur Organisasi Aktif, dan fallback ke isian settings).
    *   **Jam Operasional** (`jam_operasional`): Mengatur jam operasional kerja yang langsung dimuat dinamis pada halaman **Hubungi Kami (Kontak)**.
*   **Pengaturan Web & AI Tab (Baru)**:
    *   **Warna Utama Website** (`warna_primer`): Menyediakan input teks dan *color picker* interaktif. Pada Next.js, warna ini disuntikkan secara dinamis ke `:root` CSS dengan filter otomatis `color-mix()` untuk menghasilkan variasi warna hover/gelap agar visual tetap harmonis.
    *   **Salam Pembuka Chatbot AI** (`ai_greeting`): Konfigurasi kalimat pembuka saat warga membuka asisten obrolan AI.
    *   **SEO Meta Description** (`meta_description`): Mengatur deskripsi pencarian Google/share link secara dinamis.
    *   **SEO Meta Keywords** (`meta_keywords`): Mengatur kata kunci meta pencarian secara dinamis.
*   **Informasi Sejarah & Visi Misi**:
    *   `tahun_berdiri` dan `kepala_desa_pertama` terintegrasi penuh ke dalam sistem database settings untuk halaman **Tentang Desa**.

## 4. Pembersihan Kode Surat (Format Penomoran Otomatis)
*   **Pembersihan getSuratSettings()**: Kode keras fallback setting surat (`kode_surat_keterangan-domisili`, `kode_surat_sku`, dll) di [DesaSetting.php](file:///d:/SISTEM-DESA-CIBATU/sistem-desa-cibatu/app/Models/DesaSetting.php) dihapus.
*   **Refactor Controller**: `SuratPengajuanController` di sisi panel admin admin dirujuk untuk mengambil kode surat dinamis langsung dari `SuratType` (misalnya: `kode` kolom seperti `SKD`, `SKTM`, dll), menjaga kebersihan dan kestabilan sistem penomoran otomatis.

## 5. Rilis Versi & Push Github
*   **Backend**: Rilis versi `1.14.1`, di-push ke branch `main`, dan ditandai dengan tag `v1.14.1`.
*   **Frontend**: Rilis versi `1.0.1`, di-push ke branch `main`, dan ditandai dengan tag `v1.0.1`.
