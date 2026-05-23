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
    <title>Surat Keputusan Penghapusan Aset</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
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

        .sk-title {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .nomor-surat {
            text-align: center;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .section-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .section-table td {
            vertical-align: top;
            padding: 4px;
        }

        .label-col {
            width: 15%;
            font-weight: bold;
        }

        .separator-col {
            width: 3%;
            text-align: center;
        }

        .value-col {
            width: 82%;
            text-align: justify;
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
        }

        .signature-space {
            height: 80px;
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
                <p class="desa-name">KEPALA DESA {{ strtoupper($desa) }}</p>
                <p class="desa-address">{{ $alamat_desa }}</p>
            </div>
        </div>
    </div>

    <!-- Judul SK -->
    <div class="sk-title">
        KEPUTUSAN KEPALA DESA {{ strtoupper($desa) }}<br>
        NOMOR: {{ $nomor_surat }}
    </div>
    <div class="sk-title" style="margin-top: 5px; margin-bottom: 20px; font-size: 12pt;">
        TENTANG<br>
        PENGHAPUSAN ASET INVENTARIS DESA {{ strtoupper($desa) }}
    </div>

    <div class="sk-title" style="font-size: 12pt; margin-bottom: 15px;">
        KEPALA DESA {{ strtoupper($desa) }},
    </div>

    <!-- Konsideran -->
    <table class="section-table">
        <tr>
            <td class="label-col">Menimbang</td>
            <td class="separator-col">:</td>
            <td class="value-col">
                a. bahwa untuk melaksanakan tertib administrasi pengelolaan Barang Milik Desa serta kepastian hukum terhadap status aset desa yang rusak berat/hilang/dicuri/dihibahkan, perlu dilakukan penghapusan dari Buku Inventaris;<br>
                b. bahwa berdasarkan Berita Acara Penghapusan Aset Desa tanggal {{ \Carbon\Carbon::parse($tanggal_surat)->isoFormat('D MMMM Y') }}, beberapa aset desa telah memenuhi syarat untuk dihapus dari daftar inventaris;<br>
                c. bahwa berdasarkan pertimbangan sebagaimana dimaksud pada huruf a dan b, perlu menetapkan Keputusan Kepala Desa tentang Penghapusan Aset Inventaris Desa {{ $desa }}.
            </td>
        </tr>
        <tr>
            <td class="label-col">Mengingat</td>
            <td class="separator-col">:</td>
            <td class="value-col">
                1. Undang-Undang Nomor 6 Tahun 2014 tentang Desa;<br>
                2. Peraturan Pemerintah Nomor 43 Tahun 2014 tentang Peraturan Pelaksanaan Undang-Undang Nomor 6 Tahun 2014 tentang Desa;<br>
                3. Peraturan Menteri Dalam Negeri Nomor 110 Tahun 2016 tentang Badan Permusyawaratan Desa;<br>
                4. Peraturan Menteri Dalam Negeri Nomor 1    Tahun 2016 tentang Pengelolaan Aset Desa.
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center; font-weight: bold; padding: 15px 0;">MEMUTUSKAN:</td>
        </tr>
        <tr>
            <td class="label-col">Menetapkan</td>
            <td class="separator-col">:</td>
            <td class="value-col" style="font-weight: bold;">
                KEPUTUSAN KEPALA DESA {{ strtoupper($desa) }} TENTANG PENGHAPUSAN ASET INVENTARIS DESA {{ strtoupper($desa) }}.
            </td>
        </tr>
        <tr>
            <td class="label-col">KESATU</td>
            <td class="separator-col">:</td>
            <td class="value-col">
                Menghapus dari Buku Inventaris Barang Milik Desa {{ $desa }}, aset berupa <strong>{{ $data_tambahan['nama_aset'] ?? '-' }}</strong> sebanyak <strong>{{ $data_tambahan['jumlah_dihapus'] ?? 0 }} {{ $data_tambahan['satuan'] ?? 'Unit' }}</strong> dengan nilai perolehan sebesar <strong>Rp {{ number_format($data_tambahan['nilai_dihapus'] ?? 0, 0, ',', '.') }}</strong> karena alasan <strong>{{ $data_tambahan['alasan'] ?? 'Rusak berat / tidak dapat dipergunakan' }}</strong>.
            </td>
        </tr>
        <tr>
            <td class="label-col">KEDUA</td>
            <td class="separator-col">:</td>
            <td class="value-col">
                Segala konsekuensi hukum dan pembebanan administrasi keuangan atas penghapusan barang inventaris sebagaimana dimaksud pada Diktum KESATU sepenuhnya menjadi tanggung jawab Pemerintah Desa {{ $desa }}.
            </td>
        </tr>
        <tr>
            <td class="label-col">KETIGA</td>
            <td class="separator-col">:</td>
            <td class="value-col">
                Keputusan ini mulai berlaku pada tanggal ditetapkan dengan ketentuan apabila di kemudian hari terdapat kekeliruan, akan diadakan perbaikan sebagaimana mestinya.
            </td>
        </tr>
    </table>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%; text-align: center;">
                    Ditetapkan di : Desa {{ $desa }}<br>
                    Pada tanggal : {{ \Carbon\Carbon::parse($tanggal_surat)->isoFormat('D MMMM Y') }}<br><br>
                    <strong>{{ $kepala_desa['jabatan'] ?? 'KEPALA DESA CIBATU' }}</strong>
                    <div class="signature-space"></div>
                    <strong><u>{{ $kepala_desa['nama'] ?? '-' }}</u></strong><br>
                    NIP. {{ $kepala_desa['nip'] ?? '-' }}
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
