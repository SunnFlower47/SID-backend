@php
    $logoPath = public_path('logo desa cibatu.png');
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
    <title>Surat Pengantar</title>
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
            margin-bottom: 30px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin-right: 20px;
        }

        .desa-info {
            text-align: center;
        }

        .desa-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .desa-address {
            font-size: 11pt;
            margin-bottom: 5px;
        }

        .kode-pos {
            font-size: 10pt;
        }

        .surat-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 30px 0;
            text-decoration: underline;
        }

        .nomor-surat {
            text-align: right;
            margin-bottom: 20px;
            font-size: 11pt;
        }

        .content {
            text-align: justify;
            margin-bottom: 20px;
        }

        .data-penduduk {
            margin: 20px 0;
        }

        .data-row {
            display: flex;
            margin-bottom: 8px;
        }

        .data-label {
            width: 150px;
            font-weight: bold;
        }

        .data-value {
            flex: 1;
        }

        .keperluan-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .keperluan-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10pt;
        }

        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <img src="{{ $logoBase64 }}" alt="Logo Desa" class="logo">
            <div class="desa-info">
                <div class="desa-name">{{ $desa['nama_desa'] }}</div>
                <div class="desa-address">{{ $desa['alamat_lengkap'] }}</div>
                <div class="kode-pos">Kode Pos: {{ $desa['kode_pos'] }}</div>
            </div>
        </div>
    </div>

    <!-- Nomor Surat -->
    <div class="nomor-surat">
        Nomor: {{ $nomor_surat }}<br>
        Tanggal: {{ $tanggal_surat->format('d F Y') }}
    </div>

    <!-- Judul Surat -->
    <div class="surat-title">
        Surat Pengantar
    </div>

    <!-- Isi Surat -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, {{ $kepala_desa['jabatan'] }} {{ $desa['nama_desa'] }}, Kecamatan {{ $desa['kecamatan'] }}, Kabupaten {{ $desa['kabupaten'] }}, Provinsi {{ $desa['provinsi'] }}, menerangkan bahwa:</p>
    </div>

    <!-- Data Penduduk -->
    <div class="data-penduduk">
        <div class="data-row">
            <div class="data-label">Nama Lengkap</div>
            <div class="data-value">: {{ $penduduk->nama }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">NIK</div>
            <div class="data-value">: {{ $penduduk->nik }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Tempat, Tanggal Lahir</div>
            <div class="data-value">: {{ $penduduk->tempat_lahir }}, {{ \Carbon\Carbon::parse($penduduk->tanggal_lahir)->format('d F Y') }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Jenis Kelamin</div>
            <div class="data-value">: {{ $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'Laki-laki' : 'Perempuan' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Status Perkawinan</div>
            <div class="data-value">: {{ $penduduk->status_perkawinan }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Pekerjaan</div>
            <div class="data-value">: {{ $penduduk->pekerjaan ?? '-' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Alamat</div>
            <div class="data-value">: {{ $penduduk->alamat ?? '-' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">RT/RW</div>
            <div class="data-value">: {{ $penduduk->rt_label ?? '-' }}/{{ $penduduk->rw_label ?? '-' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Dusun</div>
            <div class="data-value">: {{ $penduduk->dusun_label ?? '-' }}</div>
        </div>
    </div>

    <!-- Keperluan -->
    @if($keperluan)
    <div class="keperluan-section">
        <div class="keperluan-title">Keperluan:</div>
        <p>{{ strtoupper($keperluan) }}</p>
    </div>
    @endif

    <!-- Tujuan -->
    @if($tujuan)
    <div class="content">
        <p>Surat pengantar ini diberikan untuk keperluan di: <strong>{{ $tujuan }}</strong></p>
    </div>
    @endif

    <!-- Keterangan Tambahan -->
    @if($keterangan_tambahan)
    <div class="content">
        <p>{{ $keterangan_tambahan }}</p>
    </div>
    @endif

    <!-- Keterangan -->
    <div class="content">
        <p>Adalah benar penduduk yang berdomisili di {{ $desa['nama_desa'] }}, Kecamatan {{ $desa['kecamatan'] }}, Kabupaten {{ $desa['kabupaten'] }}, Provinsi {{ $desa['provinsi'] }} dan berkelakuan baik.</p>

        <p>{{ $template_settings['footer'] }}</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature">
            <div class="signature-line">
                <strong>{{ $kepala_desa['nama'] }}</strong><br>
                {{ $kepala_desa['jabatan'] }}<br>
                NIP. {{ $kepala_desa['nip'] }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Surat ini berlaku selama 30 (tiga puluh) hari sejak tanggal dikeluarkan.</p>
    </div>
</body>
</html>


