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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Penghapusan Aset</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .logo-section {
            display: block;
            margin: 0 auto;
            text-align: center;
        }

        .logo {
            width: 70px;
            height: 70px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 15px;
        }

        .desa-info {
            display: inline-block;
            vertical-align: middle;
            text-align: center;
        }

        .desa-title-gov {
            font-size: 11pt;
            text-transform: uppercase;
            font-weight: bold;
            margin: 0;
        }

        .desa-name {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .desa-address {
            font-size: 10pt;
            margin: 0;
            font-style: italic;
        }

        .surat-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 25px;
            font-size: 11pt;
        }

        .content {
            text-align: justify;
            margin-bottom: 15px;
            text-indent: 40px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .details-table th, .details-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
            border: none;
        }

        .signature-table td {
            border: none;
            width: 50%;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo Desa" class="logo">
            @endif
            <div class="desa-info">
                <p class="desa-title-gov">PEMERINTAH KABUPATEN {{ strtoupper($kabupaten) }}</p>
                <p class="desa-title-gov">KECAMATAN {{ strtoupper($kecamatan) }}</p>
                <p class="desa-name">KANTOR KEPALA DESA {{ strtoupper($desa) }}</p>
                <p class="desa-address">{{ $alamat_desa }}</p>
            </div>
        </div>
    </div>

    <!-- Judul Surat -->
    <div class="surat-title">
        BERITA ACARA PENGHAPUSAN ASET DESA
    </div>
    <div class="nomor-surat">
        Nomor: {{ $nomor_surat }}
    </div>

    <!-- Isi Surat -->
    <div class="content">
        Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($tanggal_surat)->isoFormat('D MMMM Y') }}</strong>, bertempat di Kantor Desa {{ $desa }}, Kecamatan {{ $kecamatan }}, Kabupaten {{ $kabupaten }}, yang bertanda tangan di bawah ini menerangkan bahwa telah dilakukan penghapusan/pengurangan aset milik Pemerintah Desa {{ $desa }} sebagai berikut:
    </div>

    <!-- Tabel Aset -->
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Kode Barang</th>
                <th style="width: 35%;">Nama Aset</th>
                <th style="width: 15%;">Jumlah</th>
                <th style="width: 20%;">Nilai Aset (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>{{ $data_tambahan['kode_barang'] ?? '-' }}</td>
                <td>{{ $data_tambahan['nama_aset'] ?? '-' }}</td>
                <td style="text-align: center;">{{ $data_tambahan['jumlah_dihapus'] ?? 0 }} {{ $data_tambahan['satuan'] ?? 'Unit' }}</td>
                <td style="text-align: right;">{{ number_format($data_tambahan['nilai_dihapus'] ?? 0, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 15px; margin-bottom: 15px;">
        <strong>Keterangan / Alasan Penghapusan:</strong><br>
        {{ $data_tambahan['alasan'] ?? 'Tidak ada keterangan tambahan.' }}
    </div>

    <div class="content">
        Aset tersebut di atas dinyatakan telah dihapus/dikurangi dari Buku Pencatatan Inventaris Desa {{ $desa }} dikarenakan kondisi fisik <strong>{{ strtoupper(str_replace('_', ' ', $data_tambahan['kondisi_baru'] ?? 'Rusak Berat')) }}</strong> atau alasan operasional lainnya. Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    Mengetahui,<br>
                    <strong>{{ $kepala_desa['jabatan'] ?? 'Kepala Desa Cibatu' }}</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ $kepala_desa['nama'] ?? '-' }}</u></strong><br>
                    NIP. {{ $kepala_desa['nip'] ?? '-' }}
                </td>
                <td>
                    Desa {{ $desa }}, {{ \Carbon\Carbon::parse($tanggal_surat)->isoFormat('D MMMM Y') }}<br>
                    <strong>Petugas/Admin Aset</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ $pengajuan->nama_pengaju }}</u></strong><br>
                    Staf Administrasi Desa
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
