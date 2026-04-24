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
    <title>Surat Keterangan Domisili</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #000;
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
    @php
        // Normalisasi Variabel Desa
        $desaData = $desa ?? $desa_info ?? [];
        $nama_desa = $desaData['nama_desa'] ?? 'Cibatu';
        $nama_kecamatan = $desaData['kecamatan'] ?? $desaData['nama_kecamatan'] ?? 'Cibatu';
        $nama_kabupaten = $desaData['kabupaten'] ?? $desaData['nama_kabupaten'] ?? 'Purwakarta';
        $desa_provinsi = $desaData['provinsi'] ?? 'Jawa Barat';
        $alamat_desa = $desaData['alamat_lengkap'] ?? $desaData['alamat_desa'] ?? '';

        // Normalisasi Variabel Kepala Desa / Penandatangan
        $kades = $kepala_desa ?? [];
        $nama_kepala = $kades['nama'] ?? '';
        $jabatan_kepala = $kades['jabatan'] ?? 'Kepala Desa';
        $nip_kepala = $kades['nip'] ?? '-';
        
        $is_sekdes = $is_sekdes ?? false;
        
        // Tanggal
        $tgl_surat = $data['tanggal_surat'] ?? $tanggal_surat ?? now();

        // Footer settings
        $footer_text = $template_settings['footer'] ?? '';
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

    <!-- Nomor Surat -->
    <div class="nomor-surat">
        Nomor: {{ $nomor_surat }}<br>
        Tanggal: {{ \Carbon\Carbon::parse($tgl_surat)->isoFormat('D MMMM Y') }}
    </div>

    <!-- Judul Surat -->
    <div class="surat-title">
        Surat Keterangan Domisili
    </div>

    <!-- Isi Surat -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini, {{ $is_sekdes ? 'Sekretaris Desa' : 'Kepala Desa' }} {{ $nama_desa }}, Kecamatan {{ $nama_kecamatan }}, Kabupaten {{ $nama_kabupaten }}, Provinsi {{ $desa_provinsi }}, menerangkan bahwa:</p>
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
            <div class="data-value">: {{ $penduduk->tempat_lahir }}, {{ \Carbon\Carbon::parse($penduduk->tanggal_lahir)->isoFormat('D MMMM Y') }}</div>
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
            <div class="data-value">: {{ $penduduk->rt ?? '-' }}/{{ $penduduk->rw ?? '-' }}</div>
        </div>
        <div class="data-row">
            <div class="data-label">Dusun</div>
            <div class="data-value">: {{ $penduduk->dusun ?? '-' }}</div>
        </div>
    </div>

    <!-- Keterangan -->
    <div class="content">
        <p>Adalah benar penduduk yang berdomisili di {{ $nama_desa }}, Kecamatan {{ $nama_kecamatan }}, Kabupaten {{ $nama_kabupaten }}, Provinsi {{ $desa_provinsi }}.</p>

        @if($keperluan)
            <p>Surat keterangan ini diberikan untuk keperluan: <strong>{{ strtoupper($keperluan) }}</strong></p>
        @endif

        @if($tujuan)
            <p>Dan akan dipergunakan di: <strong>{{ $tujuan }}</strong></p>
        @endif

        @if($keterangan_tambahan)
            <p>{{ $keterangan_tambahan }}</p>
        @endif

        <p>{{ $footer_text }}</p>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section" style="justify-content: flex-end;">
        <div class="signature">
            <p style="margin-bottom: 20px;">
                {{ $nama_desa }}, {{ \Carbon\Carbon::parse($tgl_surat)->isoFormat('D MMMM Y') }}<br>
                @if($is_sekdes)
                    a.n. Kepala Desa {{ $nama_desa }}<br>
                    Sekretaris Desa
                @else
                    Kepala Desa {{ $nama_desa }}
                @endif
            </p>
            <div class="signature-line" style="margin-top: 60px;">
                <strong>{{ $nama_kepala }}</strong><br>
                @if($nip_kepala && $nip_kepala != '-')
                NIP. {{ $nip_kepala }}
                @endif
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Surat ini berlaku selama 30 (tiga puluh) hari sejak tanggal dikeluarkan.</p>
    </div>
</body>
</html>

