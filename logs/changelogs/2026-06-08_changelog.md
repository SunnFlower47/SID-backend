# Changelog - 8 Juni 2026

## 🚀 Fitur & Peningkatan
- **UI & Tampilan Tabel**: Memperbarui gaya tampilan tabel pada menu Layanan Surat menggunakan komponen `DataGrid`. Menambahkan garis pembatas yang elegan dan menyesuaikan ketebalan huruf pada bagian *header* agar lebih proporsional.
- **Formulir Keterangan Domisili**: Menyelaraskan *field* form input domisili manual di Layanan Surat agar sama persis dengan form pada menu Domisili utama (menambahkan field Alamat Asal, Alamat Tinggal, Keperluan, dsb).
- **Pengelolaan Variabel Template Word**: Menerapkan sistem kompatibilitas ganda (*backward & forward compatibility*) untuk variabel surat Domisili. Sistem kini otomatis mendeteksi dan mendukung penggunaan variabel standar (contoh: `${nama}`) maupun variabel khusus pendatang (contoh: `${dm_nama}`).
- **Manajemen File Word (Clean Up)**: Menambahkan fungsionalitas tombol hapus pada form untuk membersihkan file template Word lama yang menumpuk di server (storage) ketika admin mengunggah file template baru.

## 🐛 Perbaikan Bug (Bug Fixes)
- **Call to undefined relationship [templates]**: Memperbaiki error *500 Internal Server Error* saat pembuatan surat di Layanan Surat karena relasi tabel yang belum terdefinisi di model `SuratType`.
- **Form Edit Domisili Kosong**: Memperbaiki masalah di mana data tidak termuat (form kosong) saat mengedit surat domisili di Layanan Surat. Masalah disebabkan oleh perbedaan awalan kunci (`dm_`) dari `PendudukDomisiliService`. Kini data terpetakan dengan benar ke komponen React.
- **Dropdown RT/RW/Dusun Kosong saat Edit**: Memperbaiki masalah saat mengedit surat domisili, nilai dropdown tujuan (Dusun, RW, RT) kosong. Diperbaiki dengan mencocokkan teks string dari database ke ID *integer* master wilayah secara dinamis di `Edit.jsx`.
- **Duplikasi Data Spesifik Surat**: Mencegah duplikasi data spesifik berawalan `dm_` saat mengedit form domisili dengan membuang *keys* `dm_` yang dimuat ke dalam `form state` React sebelum di-POST kembali ke backend.
- **Keterangan Nama & NIK Tidak Tersedia**: Memperbaiki keterangan NIK dan Nama yang tampil kosong atau bernilai "-" pada kartu Detail Penduduk di halaman `Show.jsx` ketika jenis surat adalah domisili manual.
- **Angka 0 Muncul di UI**: Memperbaiki masalah munculnya angka "0" secara acak di antarmuka (UI) akibat penggunaan kondisional React (`condition && <Component />`) yang merender angka 0 dari pengecekan array *length*. Diganti menjadi ternary operator (`condition ? <Component /> : null`).
