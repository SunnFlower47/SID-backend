@php
    $logoPath = public_path('assets/images/logo-desa-cibatu.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoData = file_get_contents($logoPath);
        $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Surat Keterangan Tidak Mampu</title>
  <style>
    @media print {
      body {
        margin: 0;
        padding: 20mm;
      }
      .no-print {
        display: none !important;
      }
      /* Hide all browser elements */
      *[href*="sistem-desa-cibatu"],
      *[href*="preview"],
      *[href*="_token"],
      *[href*="surat"],
      *[href*="tidak-mampu"],
      *[href*="test"],
      *[href*="localhost"],
      *[href*="127.0.0.1"],
      *[href*="http"],
      *[href*="https"],
      *[title*="sistem-desa-cibatu"],
      *[title*="preview"],
      *[title*="_token"],
      *[class*="debug"],
      *[class*="browser"],
      *[id*="debug"],
      *[id*="browser"],
      *[data*="debug"],
      *[data*="browser"] {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
      }
    }
    /* Hide debug info and browser elements */
    body::after,
    body::before {
      display: none !important;
    }
    /* Hide any URL or debug text */
    *[href*="sistem-desa-cibatu"],
    *[href*="preview"],
    *[href*="_token"] {
      display: none !important;
    }
    /* Hide browser print elements */
    @media print {
      body::after,
      body::before,
      *[href*="sistem-desa-cibatu"],
      *[href*="preview"],
      *[href*="_token"],
      .no-print {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        overflow: hidden !important;
      }
    }
    body {
      font-family: "Times New Roman", serif;
      margin: 0;
      padding: 48px;
      line-height: 1.6;
      font-size: 12px;
    }
    .content {
      font-size: 12px;
    }
    .kop {
      display: flex;
      align-items: center;
      justify-content: center;
      padding-bottom: 8px;
      margin-bottom: 8px;
      position: relative;
    }
    .logo {
      width: 96px;
      height: auto;
      position: absolute;
      left: 0;
    }
    .kop-content {
      text-align: center;
      width: 100%;
    }
    .pemerintah {
      font-size: 16px;
      font-weight: normal;
      text-transform: uppercase;
      margin: 0;
      line-height: 1.2;
    }
    .kecamatan {
      font-size: 20px;
      font-weight: normal;
      text-transform: uppercase;
      margin: 0;
      line-height: 1.2;
    }
    .desa {
      font-size: 24px;
      font-weight: bold;
      text-transform: uppercase;
      margin: 0;
      line-height: 1.2;
    }
    .alamat {
      font-size: 14px;
      margin: 5px 0 0 0;
    }
    .garis-double {
      border-bottom: 1px solid black;
      margin-bottom: 2px;
    }
    .garis-tebal {
      border-bottom: 4px solid black;
      margin-bottom: 24px;
    }
    .judul-surat {
      text-align: center;
      margin-bottom: 24px;
    }
    .judul-surat h3 {
      font-weight: bold;
      text-decoration: underline;
      text-transform: uppercase;
      margin: 0 0 2px 0;
    }
    .judul-surat p {
      margin: 0;
    }
    .content {
      line-height: 1.6;
      text-align: justify;
    }
    .content p:first-of-type {
      text-indent: 20px;
    }
    .content table {
      margin: 16px 0;
      padding-left: 20px;
    }
    .content table td {
      padding: 2px 8px 2px 0;
    }
    .ttd {
  margin-top: 48px;
  text-align: right;
}

.ttd-content {
  display: inline-block;   /* biar ukurannya ngikut isi aja */
  text-align: center;      /* default center */
}

.ttd-content p {
  margin: 0;
  text-align: right;       /* override, jadi rata kanan untuk teks atas */
}

.ttd-nama {
  font-weight: bold;
  text-transform: uppercase;
  text-decoration: underline;
  display: inline-block;
  margin-left: -80px; /* atur sesuai kebutuhan biar pas tengah */
}
  </style>
</head>
<body>

<!-- KOP SURAT -->
<div class="kop">
  <img src="{{ $logoBase64 }}" alt="Logo Desa" class="logo">
  <div class="kop-content">
    <h1 class="pemerintah">Pemerintah Kabupaten Purwakarta</h1>
    <h2 class="kecamatan">Kecamatan Cibatu</h2>
    <h2 class="desa">Desa Cibatu</h2>
    <p class="alamat">Jl. Raya Cibatu KM. 15, Cibatu - Purwakarta 41183</p>
  </div>
</div>

<!-- Garis Double -->
<div class="garis-double"></div>
<div class="garis-tebal"></div>

  <!-- Judul Surat -->
  <div class="judul-surat">
    <h3>Surat Keterangan Tidak Mampu</h3>
    @php
        function intToRoman($number) {
            $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
            $returnValue = '';
            while ($number > 0) {
                foreach ($map as $roman => $int) {
                    if($number >= $int) {
                        $number -= $int;
                        $returnValue .= $roman;
                        break;
                    }
                }
            }
            return $returnValue;
        }
        $bulan = $tanggal_surat ? \Carbon\Carbon::parse($tanggal_surat)->format('n') : \Carbon\Carbon::now()->format('n');
        $tahun = $tanggal_surat ? \Carbon\Carbon::parse($tanggal_surat)->format('Y') : \Carbon\Carbon::now()->format('Y');
    @endphp
    <p style="margin-top: 0;">Nomor : {{ $kode_surat }} / {{ $nomor_urut }} / {{ $kode_desa }} / {{ $bulan_romawi }} / {{ $tahun_surat }}</p>
  </div>

  <p>Yang bertanda tangan di bawah ini Kepala Desa {{ $desa['nama_desa'] ?? 'Cibatu' }} Kecamatan {{ $desa['kecamatan'] ?? 'Cibatu' }} Kabupaten {{ $desa['kabupaten'] ?? 'Purwakarta' }}, menerangkan :</p>
  <!-- Isi Surat -->
  <div class="content">
    <table>
      <tr><td>Nama</td><td>:</td><td><strong>{{ $penduduk->nama ?? 'SUSI SUSANTI' }}</strong></td></tr>
      <tr><td>NIK</td><td>:</td><td>{{ $penduduk->nik ?? '3214145707900001' }}</td></tr>
      <tr><td>Tempat Tgl Lahir</td><td>:</td><td>{{ $penduduk->tempat_lahir ?? 'Purwakarta' }}, {{ $penduduk->tanggal_lahir ? \Carbon\Carbon::parse($penduduk->tanggal_lahir)->format('d-m-Y') : '17-07-1990' }}</td></tr>
      <tr><td>Jenis Kelamin</td><td>:</td><td>{{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
      <tr><td>Bangsa/Agama</td><td>:</td><td>Indonesia / {{ $penduduk->agama ?? 'Islam' }}</td></tr>
      <tr><td>Pekerjaan</td><td>:</td><td>{{ $penduduk->pekerjaan ?? 'Mengurus Rumah Tangga' }}</td></tr>
      <tr><td>Alamat</td><td>:</td><td>{{ $penduduk->alamat }}, RT. {{ $penduduk->rt_label }}/RW. {{ $penduduk->rw_label }} Desa {{ $desa['nama_desa'] }} Kec. {{ $desa['kecamatan'] }} Kab. {{ $desa['kabupaten'] }}</td></tr>
    </table>
    <p>
      Nama tersebut diatas adalah benar Penduduk Desa {{ $desa['nama_desa'] ?? 'Cibatu' }} yang tercantum dalam Buku Induk Kependudukan Desa {{ $desa['nama_desa'] ?? 'Cibatu' }}
      dan berdasarkan Catatan serta Penelitian, bahwa nama tersebut berasal dari Keluarga
      <strong><u>TIDAK MAMPU</u></strong> dan terdaftar dalam Daftar Keluarga Miskin (GAKIN).
    </p>

    @if(isset($nama_wali) && $nama_wali)
    <p style="margin-top: 8px;">Adapun data Orang Tua / Wali adalah sebagai berikut:</p>
    <table>
      <tr><td>Nama Wali</td><td>:</td><td>{{ $nama_wali }}</td></tr>
      <tr><td>Pekerjaan</td><td>:</td><td>{{ $pekerjaan_wali ?? '-' }}</td></tr>
      <tr><td>Alamat</td><td>:</td><td>{{ $alamat_wali ?? '-' }}</td></tr>
      @if(isset($penghasilan) && $penghasilan)
      <tr><td>Penghasilan</td><td>:</td><td>Rp. {{ number_format($penghasilan, 0, ',', '.') }} / Bulan</td></tr>
      @endif
    </table>
    @endif
    <p style="margin-top: 8px;">
      Surat keterangan ini dibuat dengan benar untuk pengajuan <strong>{{ strtoupper($keperluan ?? 'PEMASANGAN LISTRIK GRATIS') }}</strong>.
    </p>
    <p style="margin-top: 8px;">
      Demikian surat keterangan ini kami buat dengan sebenarnya agar yang berkepentingan mengetahui dan untuk dipergunakan sebagaimana mestinya.
    </p>
  </div>

  <!-- Tanda Tangan -->
  <div class="ttd">
    <div class="ttd-content">
      <p>{{ $desa['nama_desa'] ?? 'Cibatu' }}, {{ $tanggal_surat ? \Carbon\Carbon::parse($tanggal_surat)->format('d F Y') : \Carbon\Carbon::now()->format('d F Y') }}</p>
      @if($is_sekdes)
      <p>a.n. Kepala Desa {{ $desa['nama_desa'] ?? 'Cibatu' }}</p>
      @else
      <p>Kepala Desa {{ $desa['nama_desa'] ?? 'Cibatu' }}</p>
      @endif
      <br><br><br>
      <p class="ttd-nama">{{ strtoupper($kepala_desa['nama_kepala_desa'] ?? $kepala_desa['nama'] ?? 'FAJAR FERYANTO') }}</p>
    </div>
  </div>
</body>
</html>

