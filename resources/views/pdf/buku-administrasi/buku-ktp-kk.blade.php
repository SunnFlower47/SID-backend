<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku KTP dan KK</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 10px; margin: 10px; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 3px; text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .signature-area { width: 100%; margin-top: 30px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; vertical-align: top; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 50px; }
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
        <h3>BUKU KARTU TANDA PENDUDUK DAN BUKU KARTU KELUARGA</h3>
        <p style="font-size: 10px; font-weight: normal; margin-top: 5px; text-transform: none;">(Lampiran XV — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NO. KK</th>
                <th rowspan="2">NAMA LENGKAP</th>
                <th rowspan="2">NIK</th>
                <th rowspan="2">JENIS<br>KELAMIN</th>
                <th rowspan="2">TEMPAT /<br>TGL LAHIR</th>
                <th rowspan="2">GOL<br>DARAH</th>
                <th rowspan="2">AGAMA</th>
                <th rowspan="2">PENDIDIKAN</th>
                <th rowspan="2">PEKERJAAN</th>
                <th rowspan="2">ALAMAT</th>
                <th rowspan="2">STATUS<br>PERKAWINAN</th>
                <th rowspan="2">TEMPAT/TGL<br>DIKELUARKAN</th>
                <th rowspan="2">STATUS<br>HUBUNGAN</th>
                <th rowspan="2">KEWARGA-<br>NEGARAAN</th>
                <th colspan="2">ORANG TUA</th>
                <th rowspan="2">TGL MULAI<br>TINGGAL</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr>
                <th>AYAH</th>
                <th>IBU</th>
            </tr>
            <tr style="background-color: #f2f2f2; font-size: 9px;">
                @for($i=1; $i<=19; $i++)
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

                    $jk = ($item->jenis_kelamin == 'Laki-Laki' || $item->jenis_kelamin == 'LAKI-LAKI' || $item->jenis_kelamin == 'L') ? 'L' : 'P';
                    $tanggalMasuk = $item->created_at ? date('d-m-Y', strtotime($item->created_at)) : '-';

                    $tanggalDikeluarkan = '-';
                    if ($item->kartuKeluarga && ($item->kartuKeluarga->tempat_dikeluarkan || $item->kartuKeluarga->tanggal_dikeluarkan)) {
                        $tanggalDikeluarkan = ($item->kartuKeluarga->tempat_dikeluarkan ?? '') . ', ' . ($item->kartuKeluarga->tanggal_dikeluarkan ? date('d-m-Y', strtotime($item->kartuKeluarga->tanggal_dikeluarkan)) : '');
                    }
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->kartuKeluarga ? $item->kartuKeluarga->nkk : '-' }}</td>
                    <td class="text-left">{{ $item->nama }}</td>
                    <td>{{ $item->nik }}</td>
                    <td>{{ $jk }}</td>
                    <td class="text-left">{{ $ttl }}</td>
                    <td>{{ $item->golongan_darah ?? '-' }}</td>
                    <td>{{ $item->agama ?? '-' }}</td>
                    <td>{{ $item->pendidikan ?? '-' }}</td>
                    <td>{{ $item->pekerjaan ?? '-' }}</td>
                    <td class="text-left">{{ $item->kartuKeluarga ? $item->kartuKeluarga->alamat : '-' }}</td>
                    <td>{{ $item->status_perkawinan ?? '-' }}</td>
                    <td>{{ $tanggalDikeluarkan }}</td>
                    <td>{{ $item->kedudukan_keluarga ?? '-' }}</td>
                    <td>{{ $item->kewarganegaraan ?? ($item->warganegara ?? 'WNI') }}</td>
                    <td class="text-left">{{ $item->nama_ayah ?? '-' }}</td>
                    <td class="text-left">{{ $item->nama_ibu ?? '-' }}</td>
                    <td>{{ $tanggalMasuk }}</td>
                    <td class="text-left">{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="19">Tidak ada data.</td>
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
