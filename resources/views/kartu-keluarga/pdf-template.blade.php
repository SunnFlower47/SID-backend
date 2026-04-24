<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Keluarga - {{ $kk->nkk }}</title>
    <style>
        @page {
            size: Legal landscape;
            margin: 0.3in;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .logo-section {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }

        .title-section {
            text-align: center;
            flex-grow: 1;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .nkk-number {
            font-size: 14px;
            font-weight: bold;
            margin: 8px 0;
        }

        .family-info {
            text-align: right;
            font-size: 10px;
        }

        .family-info div {
            margin: 1px 0;
        }

        .main-tables {
            margin-top: 15px;
        }

        .table-container {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
            font-size: 9px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .table th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8px;
        }

        .table td {
            font-size: 9px;
        }

        .left-table {
            width: 65%;
        }

        .right-table {
            width: 35%;
        }

        .no-column {
            width: 20px;
        }

        .name-column {
            width: 100px;
            text-align: left;
        }

        .nik-column {
            width: 120px;
        }

        .gender-column {
            width: 50px;
        }

        .birthplace-column {
            width: 80px;
            text-align: left;
        }

        .birthdate-column {
            width: 80px;
        }

        .religion-column {
            width: 60px;
        }

        .education-column {
            width: 60px;
        }

        .job-column {
            width: 80px;
        }

        .bloodtype-column {
            width: 50px;
        }

        .marital-column {
            width: 60px;
        }

        .marriage-date-column {
            width: 80px;
        }

        .relationship-column {
            width: 80px;
            text-align: left;
        }

        .nationality-column {
            width: 50px;
        }

        .passport-column {
            width: 60px;
        }

        .kitap-column {
            width: 60px;
        }

        .father-column {
            width: 80px;
            text-align: left;
        }

        .mother-column {
            width: 80px;
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            font-size: 9px;
        }

        .footer-right {
            text-align: center;
            font-size: 8px;
        }

        .signature-space {
            height: 40px;
            margin-top: 10px;
        }

        .qr-code {
            width: 60px;
            height: 60px;
            margin: 5px auto;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6px;
            text-align: center;
        }

        .disclaimer {
            text-align: center;
            font-size: 7px;
            margin-top: 10px;
            font-style: italic;
        }

        .empty-row {
            height: 20px;
        }

        .empty-row td {
            border: 1px solid #000;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo-section">
            <img src="{{ public_path('logo-garuda.png') }}" alt="Garuda Pancasila" class="logo">
            <div>
                <div style="font-size: 8px; font-weight: bold;">REPUBLIK INDONESIA</div>
            </div>
        </div>

        <div class="title-section">
            <h1 class="title">KARTU KELUARGA</h1>
            <div class="nkk-number">No. {{ $kk->nkk }}</div>
        </div>

        <div class="family-info">
            <div><strong>Nama Kepala Keluarga:</strong> {{ $kepalaKeluarga->nama ?? '-' }}</div>
            <div><strong>Alamat:</strong> {{ $kepalaKeluarga->alamat ?? '-' }}</div>
            <div><strong>RT/RW:</strong> {{ $kepalaKeluarga->rt ?? '-' }}/{{ $kepalaKeluarga->rw ?? '-' }}</div>
            <div><strong>Kode Pos:</strong> {{ $desaInfo->kode_pos ?? '-' }}</div>
            <div><strong>Desa/Kelurahan:</strong> {{ $desaInfo->nama_desa ?? '-' }}</div>
            <div><strong>Kecamatan:</strong> {{ $desaInfo->kecamatan ?? '-' }}</div>
            <div><strong>Kabupaten/Kota:</strong> {{ $desaInfo->kabupaten ?? '-' }}</div>
            <div><strong>Provinsi:</strong> {{ $desaInfo->provinsi ?? '-' }}</div>
        </div>
    </div>

    <!-- Main Tables -->
    <div class="main-tables">
        <div class="table-container">
            <!-- Left Table -->
            <table class="table left-table">
                <thead>
                    <tr>
                        <th class="no-column">No</th>
                        <th class="name-column">Nama Lengkap</th>
                        <th class="nik-column">NIK</th>
                        <th class="gender-column">Jenis Kelamin</th>
                        <th class="birthplace-column">Tempat Lahir</th>
                        <th class="birthdate-column">Tanggal Lahir</th>
                        <th class="religion-column">Agama</th>
                        <th class="education-column">Pendidikan</th>
                        <th class="job-column">Jenis Pekerjaan</th>
                        <th class="bloodtype-column">Golongan Darah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anggotaKeluarga as $index => $anggota)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="name-column">{{ $anggota->nama }}</td>
                        <td>{{ $anggota->nik }}</td>
                        <td>{{ $anggota->jenis_kelamin }}</td>
                        <td class="birthplace-column">{{ $anggota->tempat_lahir }}</td>
                        <td>{{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d-m-Y') }}</td>
                        <td>{{ $anggota->agama }}</td>
                        <td>{{ $anggota->pendidikan }}</td>
                        <td>{{ $anggota->pekerjaan }}</td>
                        <td>{{ $anggota->golongan_darah ?? '-' }}</td>
                    </tr>
                    @endforeach

                    @for($i = count($anggotaKeluarga); $i < 10; $i++)
                    <tr class="empty-row">
                        <td>{{ $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Right Table -->
            <table class="table right-table">
                <thead>
                    <tr>
                        <th class="no-column">No</th>
                        <th class="marital-column">Status Perkawinan</th>
                        <th class="marriage-date-column">Tanggal Perkawinan/Perceraian</th>
                        <th class="relationship-column">Status Hubungan Dalam Keluarga</th>
                        <th class="nationality-column">Kewarganegaraan</th>
                        <th class="passport-column">No. Paspor</th>
                        <th class="kitap-column">No. KITAP</th>
                        <th class="father-column">Ayah</th>
                        <th class="mother-column">Ibu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($anggotaKeluarga as $index => $anggota)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $anggota->status_perkawinan }}</td>
                        <td>{{ $anggota->tanggal_perkawinan ? \Carbon\Carbon::parse($anggota->tanggal_perkawinan)->format('d-m-Y') : '-' }}</td>
                        <td class="relationship-column">{{ $anggota->kedudukan_keluarga }}</td>
                        <td>{{ $anggota->kewarganegaraan ?? 'WNI' }}</td>
                        <td>{{ $anggota->no_paspor ?? '-' }}</td>
                        <td>{{ $anggota->no_kitap ?? '-' }}</td>
                        <td class="father-column">{{ $anggota->nama_ayah ?? '-' }}</td>
                        <td class="mother-column">{{ $anggota->nama_ibu ?? '-' }}</td>
                    </tr>
                    @endforeach

                    @for($i = count($anggotaKeluarga); $i < 10; $i++)
                    <tr class="empty-row">
                        <td>{{ $i + 1 }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-left">
            <div><strong>Dikeluarkan Tanggal:</strong> {{ now()->format('d-m-Y') }}</div>
            <div><strong>KEPALA KELUARGA</strong></div>
            <div class="signature-space"></div>
            <div><strong>{{ $kepalaKeluarga->nama ?? '-' }}</strong></div>
        </div>

        <div class="footer-right">
            <div><strong>KEPALA DINAS KEPENDUDUKAN DAN PENCATATAN SIPIL</strong></div>
            <div><strong>{{ $desaInfo->kabupaten ?? 'KABUPATEN' }}</strong></div>
            <div class="qr-code">
                QR CODE
            </div>
            <div><strong>NIP. {{ $desaInfo->nip_kepala_dinas ?? 'XXXXXXXXXXXXXX' }}</strong></div>
        </div>
    </div>

    <!-- Disclaimer -->
    <div class="disclaimer">
        Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan oleh Balai Sertifikasi Elektronik (BSE), BSSN
    </div>
</body>
</html>
