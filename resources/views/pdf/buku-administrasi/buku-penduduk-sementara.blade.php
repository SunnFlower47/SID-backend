<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Penduduk Sementara</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .signature-area { width: 100%; margin-top: 30px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; vertical-align: top; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 60px; }
    </style>
</head>
<body>
    @php
        $desa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
        $kades = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Kepala Desa')->value('nama') ?? '..................');
        $sekdes = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Sekretaris Desa')->value('nama') ?? '..................');
        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
    @endphp

    <div class="header">
        <h3>BUKU PENDUDUK SEMENTARA DESA</h3>
        <p style="font-size: 10px; font-weight: normal; margin-top: 5px; text-transform: none;">(Lampiran XIV — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NOMOR URUT</th>
                <th>NAMA LENGKAP</th>
                <th>JENIS<br>KELAMIN</th>
                <th>TEMPAT DAN<br>TANGGAL LAHIR</th>
                <th>PEKERJAAN</th>
                <th>KEWARGA-<br>NEGARAAN</th>
                <th>DATANG DARI</th>
                <th>MAKSUD DAN<br>TUJUAN DATANG</th>
                <th>NAMA DAN ALAMAT<br>YANG DIDATANGI</th>
                <th>DATANG<br>TANGGAL</th>
                <th>PERGI<br>TANGGAL</th>
                <th>KET</th>
            </tr>
            <tr style="background-color: #f2f2f2; font-size: 10px;">
                @for($i=1; $i<=12; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse ($data as $item)
                @php
                    $ttl = '-';
                    if ($item->tempat_lahir || $item->tanggal_lahir) {
                        $ttl = ($item->tempat_lahir ?? '') . ', ' . ($item->tanggal_lahir ? date('d-m-Y', strtotime($item->tanggal_lahir)) : '');
                    }

                    $datangTgl = $item->tanggal_masuk ? date('d-m-Y', strtotime($item->tanggal_masuk)) : '-';
                    $pergiTgl = $item->tanggal_berlaku ? date('d-m-Y', strtotime($item->tanggal_berlaku)) : '-';
                    $jk = ($item->jenis_kelamin == 'Laki-Laki' || $item->jenis_kelamin == 'LAKI-LAKI' || $item->jenis_kelamin == 'L') ? 'L' : 'P';
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $item->nama }}</td>
                    <td>{{ $jk }}</td>
                    <td class="text-left">{{ $ttl }}</td>
                    <td class="text-left">{{ $item->pekerjaan ?? '-' }}</td>
                    <td>WNI</td>
                    <td class="text-left">{{ $item->asal_daerah ?? ($item->alamat_asal ?? '-') }}</td>
                    <td class="text-left">{{ $item->keperluan_domisili ?? '-' }}</td>
                    <td class="text-left">{{ $item->alamat_tinggal ?? '-' }}</td>
                    <td>{{ $datangTgl }}</td>
                    <td>{{ $pergiTgl }}</td>
                    <td class="text-left">{{ $item->catatan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12">Tidak ada data penduduk sementara.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-area">
        <table class="signature-table">
            <tr>
                <td style="width: 40%;">
                    MENGETAHUI<br>
                    KEPALA DESA {{ $desa }}<br>
                    <div class="signature-name">{{ $kades }}</div>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%;">
                    Desa {{ $desa }}, {{ $tanggalCetak }}<br>
                    SEKRETARIS DESA<br>
                    <div class="signature-name">{{ $sekdes }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
