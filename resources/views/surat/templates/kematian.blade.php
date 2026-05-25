<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kematian - {{ $penduduk->nama ?? 'Penduduk' }}</title>
<style>
    @page {
        size: 330mm 215mm; /* F4 Landscape */
        margin: 20mm;
    }
    body {
        font-family: "Times New Roman", serif;
        font-size: 10pt; 
        background: #fff;
    }

    .container {
        width: 11.5cm; 
        min-height: 670px;
        margin-left: 30px; 
        border: 2px solid #000;
        padding: 10px 20px; 
    }


    .header-table {
        width: 100%;             
        margin-bottom: 5px;
        border-bottom: none; /* Removed border */
        padding-bottom: 0;
    }

    .header-table td {
        font-size: 8pt;
        padding: 0 2px;
        vertical-align: top;
    }

    .title {
        text-align: center;
        font-weight: bold;
        text-decoration: underline;
        margin-top: 5px;
        font-size: 11pt;
        text-transform: uppercase;
    }

    .subtitle {
        text-align: center;
        margin-bottom: 10px;
        font-size: 10pt;
    }

    .content {
        font-size: 10pt;
        text-align: justify;
    }

    .content p {
        margin: 2px 0;
        text-indent: 20px;
    }

    .data-table {
        width: 100%;             
        margin-left: 0;
        border-collapse: collapse;
        margin-bottom: 5px;
    }

    .data-table td {
        padding: 1px 2px;
        vertical-align: top;
        font-size: 10pt;
        line-height: 1.2;
    }

    .label {
        width: 120px; /* Reduced label width for 380px container */
        white-space: nowrap;
    }

    .colon {
        width: 10px;
        text-align: center;
    }

    .footer {
    margin-top: 10px;
    text-align: right;
    width: 100%;
    font-size: 10pt;
}

    .signature-box {
    display: inline-block;
    width: 220px;
    text-align: center;
}

    .signature {
        margin-top: 70px;     
        font-weight: bold;
        text-decoration: underline;
    }


    @media print {
        body {
            margin: 0;
            background: white;
            -webkit-print-color-adjust: exact;
        }
        .container {
            width: 400px;
            margin-left: 30px;
            border: 2px solid #000;
        }
        @page {
            margin: 10mm; /* Reduced print margin */    
        }
    }
</style>

</head>
<body>

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

    // Data Preparation
    if (isset($mutasi)) {
        $detail = is_array($mutasi->detail_tambahan) ? $mutasi->detail_tambahan : (json_decode($mutasi->detail_tambahan, true) ?? []);
        $kematian = $detail['kematian'] ?? [];
        $pemakaman = $detail['pemakaman'] ?? [];
        $tanggal_kejadian = $mutasi->tanggal_mutasi; 
        $alasan = $detail['alasan'] ?? ($mutasi->alasan ?? '-');

        // Pelapor
        $pelapor_nama = $detail['pelapor_nama'] ?? '....................';
        $pelapor_umur = $detail['pelapor_umur'] ?? '...';
        $pelapor_pekerjaan = $detail['pelapor_pekerjaan'] ?? '....................';
        $pelapor_alamat = $detail['pelapor_alamat'] ?? '..................................................';
        $pelapor_hubungan = $detail['pelapor_hubungan'] ?? '....................';

    } else {
        // Fallback for SuratPengajuan
        $kematian = $kematian ?? ($data_tambahan['kematian'] ?? []);
        $pemakaman = $pemakaman ?? ($data_tambahan['pemakaman'] ?? []);
        
        $tanggal_kejadian = $kematian['tanggal'] ?? ($tanggal_surat ?? date('Y-m-d'));
        $alasan = $data_tambahan['alasan'] ?? '-';

        $pelapor_nama = $data_tambahan['pelapor_nama'] ?? '....................';
        $pelapor_umur = $data_tambahan['pelapor_umur'] ?? '...';
        $pelapor_pekerjaan = $data_tambahan['pelapor_pekerjaan'] ?? '....................';
        $pelapor_alamat = $data_tambahan['pelapor_alamat'] ?? '..................................................';
        $pelapor_hubungan = $data_tambahan['pelapor_hubungan'] ?? '....................';
    }

    // Header Variables
    $tanggal_surat_safe = isset($tanggal_surat) ? $tanggal_surat : \Carbon\Carbon::now();
    $bulanSurat = \Carbon\Carbon::parse($tanggal_surat_safe)->format('n');
    $tahunSurat = \Carbon\Carbon::parse($tanggal_surat_safe)->format('Y');

    // Dynamic Desa Info
    $nama_kabupaten = $desa['nama_kabupaten'] ?? $desa->nama_kabupaten ?? 'Purwakarta';
    $nama_kecamatan = $desa['nama_kecamatan'] ?? $desa->nama_kecamatan ?? 'Cibatu';
    $nama_desa = $desa['nama_desa'] ?? $desa->nama_desa ?? 'Cibatu';

    // Normalize Kepala Desa
    // Handle object, array, or string
    $kades_val = $kepala_desa ?? null;
    $nama_kepala = '';
    
    if (is_object($kades_val)) {
        $nama_kepala = $kades_val->nama ?? '';
    } elseif (is_array($kades_val)) {
        $nama_kepala = $kades_val['nama'] ?? '';
    } else {
        $nama_kepala = (string) $kades_val;
    }
    
    if (empty($nama_kepala)) $nama_kepala = '....................';
@endphp

<div class="container">

    <table class="header-table">
        <tr>
            <td width="120">Pemerintah Kab/Kota</td>
            <td>: {{ $nama_kabupaten }}</td>
        </tr>
        <tr>
            <td>Kecamatan</td>
            <td>: {{ $nama_kecamatan }}</td>
        </tr>
        <tr>
            <td>Desa / Kelurahan</td>
            <td>: {{ $nama_desa }}</td>
        </tr>
    </table>

    <div class="title">SURAT KETERANGAN KEMATIAN</div>
    <div class="subtitle">No. {{ $nomor_surat ?? ($kode_surat ?? '') . ' / ' . ($nomor_urut ?? '') . ' / ' . ($kode_desa ?? '') . ' / ' . ($bulan_romawi ?? '') . ' / ' . ($tahun_surat ?? '') }}</div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, menerangkan bahwa :</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td><td class="colon">:</td><td><b>{{ strtoupper($penduduk->nama) }}</b></td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td><td>:</td><td>{{ ucwords(strtolower($penduduk->jenis_kelamin)) }}</td>
            </tr>
            <tr>
                <td>Umur</td><td>:</td><td>{{ (int) \Carbon\Carbon::parse($penduduk->tanggal_lahir)->diffInYears(\Carbon\Carbon::parse($tanggal_kejadian)) }} Tahun</td>
            </tr>
            <tr>
                <td>Agama</td><td>:</td><td>{{ $penduduk->agama }}</td>
            </tr>
            <tr>
                <td>Alamat</td><td>:</td>
                <td>
                    {{ $penduduk->alamat }}
                    RT. {{ $penduduk->rt_label }}/RW. {{ $penduduk->rw_label }},
                    Desa {{ $nama_desa }}, Kec. {{ $nama_kecamatan }}, Kab. {{ $nama_kabupaten }}
                </td>
            </tr>
        </table>    

        <p>telah meninggal dunia pada :</p>

        <table class="data-table">
            <tr>
                <td class="label">Hari</td><td class="colon">:</td><td>{{ $kematian['hari'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td><td>:</td><td>{{ \Carbon\Carbon::parse($tanggal_kejadian)->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td>Jam</td><td>:</td><td>{{ $kematian['jam'] ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td>Bertempat di</td><td>:</td><td>{{ $kematian['bertempat_di'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Penyebab Kematian</td><td>:</td><td>{{ $alasan ?? '-' }}</td>
            </tr>
        </table>

        <p>Dimakamkan pada :</p>

        <table class="data-table">
            <tr>
                <td class="label">Hari</td><td class="colon">:</td><td>{{ $pemakaman['hari'] ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td><td>:</td><td>{{ isset($pemakaman['tanggal']) ? \Carbon\Carbon::parse($pemakaman['tanggal'])->isoFormat('D MMMM Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Jam</td><td>:</td><td>{{ $pemakaman['jam'] ?? '-' }} WIB</td>
            </tr>
            <tr>
                <td>Bertempat di</td><td>:</td><td>{{ $pemakaman['lokasi'] ?? '-' }}</td>
            </tr>
        </table>

        <p>Surat keterangan ini dibuat berdasarkan keterangan pelapor :</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td><td class="colon">:</td><td><b>{{ strtoupper($pelapor_nama) }}</b></td>
            </tr>
            <tr>
                <td>Umur</td><td>:</td><td>{{ $pelapor_umur }} Tahun</td>
            </tr>
            <tr>
                <td>Pekerjaan</td><td>:</td><td>{{ $pelapor_pekerjaan }}</td>
            </tr>
            <tr>
                <td>Alamat</td><td>:</td>
                <td>
                    {{ $pelapor_alamat }}
                </td>
            </tr>
        </table>

        <p>Hubungan pelapor dengan yang meninggal : {{ $pelapor_hubungan }}</p>
    </div>

    <div class="footer">
    <div class="signature-box">
        <p>
            {{ $nama_desa }}, {{ \Carbon\Carbon::parse($tanggal_surat_safe)->isoFormat('D MMMM Y') }}<br>
            @if($is_sekdes ?? false)
                a/n Kepala Desa {{ $nama_desa }}
            @else
                Kepala Desa {{ $nama_desa }}
            @endif
        </p>

        <div class="signature">{{ strtoupper($nama_kepala) }}</div>
    </div>
</div>


</body>
</html>

