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
    <title>Surat Keterangan Usaha</title>
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

        .usaha-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #28a745;
        }

        .usaha-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #28a745;
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
        Nomor: {{ $kode_surat }} / {{ $nomor_urut }} / {{ $kode_desa }} / {{ $bulan_romawi }} / {{ $tahun_surat }}<br>
        Tanggal: {{ $tanggal_surat->format('d F Y') }}
    </div>

    <!-- Judul Surat -->
    <div class="surat-title">
        Surat Keterangan Usaha
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
            <div class="data-value">: {{ $penduduk->alamat }}, RT. {{ $penduduk->rt_label }}/RW. {{ $penduduk->rw_label }} Desa {{ $desa['nama_desa'] }} Kec. {{ $desa['kecamatan'] }} Kab. {{ $desa['kabupaten'] }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Dusun</div>
            <div class="data-value">: {{ $penduduk->dusun_label ?? '-' }}</div>
        </div>
    </div>

    <!-- Informasi Usaha -->
    @if(isset($nama_usaha) || isset($jenis_usaha) || isset($alamat_usaha))
    <div class="usaha-section">
        <div class="usaha-title">Informasi Usaha:</div>
        @if(isset($nama_usaha))
        <div class="data-row">
            <div class="data-label">Nama Usaha</div>
            <div class="data-value">: {{ $nama_usaha }}</div>
        </div>
        @endif
        @if(isset($jenis_usaha))
        <div class="data-row">
            <div class="data-label">Jenis Usaha</div>
            <div class="data-value">: {{ $jenis_usaha }}</div>
        </div>
        @endif
        @if(isset($alamat_usaha))
        <div class="data-row">
            <div class="data-label">Alamat Usaha</div>
            <div class="data-value">: {{ $alamat_usaha }}</div>
        </div>
        @endif
        @if(isset($mulai_usaha))
        <div class="data-row">
            <div class="data-label">Mulai Usaha</div>
            <div class="data-value">: {{ $mulai_usaha }}</div>
        </div>
        @endif
    </div>
    @endif

    <!-- Keperluan -->
    @if($keperluan)
    <div class="content">
        <p>Surat keterangan usaha ini diberikan untuk keperluan: <strong>{{ strtoupper($keperluan) }}</strong></p>
    </div>
    @endif

    <!-- Tujuan -->
    @if($tujuan)
    <div class="content">
        <p>Surat keterangan ini diberikan untuk keperluan di: <strong>{{ $tujuan }}</strong></p>
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
        <p>Adalah benar penduduk yang berdomisili di {{ $desa['nama_desa'] }}, Kecamatan {{ $desa['kecamatan'] }}, Kabupaten {{ $desa['kabupaten'] }}, Provinsi {{ $desa['provinsi'] }} dan memiliki usaha sebagaimana tersebut di atas.</p>

        <p>{{ $template_settings['footer'] }}</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature">
            <div class="signature-date">
                {{ $desa['nama_desa'] }}, {{ $tanggal_surat->format('d F Y') }}<br>
                @if($is_sekdes)
                    a.n. Kepala Desa {{ $desa['nama_desa'] }}<br>
                @else
                    Kepala Desa {{ $desa['nama_desa'] }}
                @endif
            </div>
            <div class="signature-line" style="margin-top: 60px;">
                <strong>{{ strtoupper($kepala_desa['nama']) }}</strong><br>
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

