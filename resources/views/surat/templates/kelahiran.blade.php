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
    <title>Surat Keterangan Kelahiran</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
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
            font-size: 12px;
            margin: 0;
            line-height: 1.2;
        }
        .garis-double {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 0;
            margin: 5px 0;
        }
        .garis-tebal {
            border-top: 3px solid #000;
            height: 0;
            margin: 5px 0;
        }
        .surat-title {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .content {
            text-align: justify;
            margin-bottom: 20px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .data-table td {
            padding: 5px 10px;
            border: 1px solid #000;
        }
        .data-table .label {
            background-color: #f0f0f0;
            font-weight: bold;
            width: 30%;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            margin-top: 50px;
            text-align: center;
        }
        .date {
            text-align: right;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    @php
        // Normalisasi Variabel Desa
        $desaData = $desa ?? $desa_info ?? [];
        $nama_desa = $desaData['nama_desa'] ?? 'Cibatu';
        $nama_kecamatan = $desaData['kecamatan'] ?? $desaData['nama_kecamatan'] ?? 'Cibatu';
        $nama_kabupaten = $desaData['kabupaten'] ?? $desaData['nama_kabupaten'] ?? 'Purwakarta';
        $alamat_desa = $desaData['alamat_lengkap'] ?? $desaData['alamat_desa'] ?? '';

        // Normalisasi Variabel Kepala Desa / Penandatangan
        $kades = $kepala_desa ?? [];
        $nama_kepala = $kades['nama'] ?? '';
        $jabatan_kepala = $kades['jabatan'] ?? 'Kepala Desa';
        $nip_kepala = $kades['nip'] ?? '-';
        
        $is_sekdes = $is_sekdes ?? false;
        
        // Tanggal
        $tgl_surat = $data['tanggal_surat'] ?? $tanggal_surat ?? now();
    @endphp

    <!-- KOP SURAT -->
    <div class="kop">
        <img src="{{ $logoBase64 }}" alt="Logo Desa" class="logo">
        <div class="kop-content">
            <h1 class="pemerintah">Pemerintah Kabupaten {{ $nama_kabupaten }}</h1>
            <h2 class="kecamatan">Kecamatan {{ $nama_kecamatan }}</h2>
            <h2 class="desa">{{ $nama_desa }}</h2>
            <p class="alamat">{{ $alamat_desa }}</p>
        </div>
    </div>

    <!-- Garis Double -->
    <div class="garis-double"></div>
    <div class="garis-tebal"></div>

    <div class="surat-title">SURAT KETERANGAN KELAHIRAN</div>

    <div class="date">
        {{ $nama_desa }}, {{ \Carbon\Carbon::parse($tgl_surat)->isoFormat('D MMMM Y') }}
    </div>

    <div class="content">
        <p>Yang bertanda tangan di bawah ini, {{ $is_sekdes ? 'Sekretaris Desa' : 'Kepala Desa' }} {{ $nama_desa }}, menerangkan bahwa:</p>
    </div>

    <table class="data-table">
        <tr>
            <td class="label">Nama Bayi</td>
            <td>{{ $nama_bayi ?? $penduduk->nama }}</td>
        </tr>
        <tr>
            <td class="label">Tempat, Tanggal Lahir</td>
            <td>{{ $tempat_lahir ?? $penduduk->tempat_lahir }}, {{ isset($tanggal_lahir) ? \Carbon\Carbon::parse($tanggal_lahir)->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($penduduk->tanggal_lahir)->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td>{{ $jenis_kelamin ?? ($penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan') }}</td>
        </tr>
        <tr>
            <td class="label">Nama Ayah</td>
            <td>{{ $nama_ayah ?? 'Tidak disebutkan' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Ibu</td>
            <td>{{ $nama_ibu ?? 'Tidak disebutkan' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td>{{ $penduduk->alamat ?? 'Tidak tersedia' }}</td>
        </tr>
        <tr>
            <td class="label">Berat Badan</td>
            <td>{{ $berat_badan ?? 'Tidak disebutkan' }} {{ isset($berat_badan) ? 'kg' : '' }}</td>
        </tr>
        <tr>
            <td class="label">Panjang Badan</td>
            <td>{{ $panjang_badan ?? 'Tidak disebutkan' }} {{ isset($panjang_badan) ? 'cm' : '' }}</td>
        </tr>
    </table>

    <div class="content">
        <p>Adalah benar-benar telah lahir di Desa {{ $nama_desa }} dan tercatat dalam data kependudukan.</p>
        <p>Surat keterangan ini dibuat untuk keperluan: <strong>{{ strtoupper($keperluan ?? 'ADMINISTRASI KEPENDUDUKAN') }}</strong></p>
        <p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan seperlunya.</p>
    </div>

    <div class="signature">
        <div class="signature-line">
            <p style="margin-bottom: 20px;">
                @if($is_sekdes)
                    a.n. Kepala Desa {{ $nama_desa }}<br>
                @else
                    Kepala Desa {{ $nama_desa }}
                @endif
            </p>
            <div style="margin-top: 60px; font-weight: bold; text-decoration: underline;">{{ strtoupper($nama_kepala) }}</div>
        </div>
    </div>
</body>
</html>


