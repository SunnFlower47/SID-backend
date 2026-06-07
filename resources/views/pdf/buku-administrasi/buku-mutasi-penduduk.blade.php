<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Mutasi Penduduk</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 5px; }
        th { text-align: center; font-weight: bold; background-color: #f3f4f6; }
        .center { text-align: center; }
        .ttd { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .ttd td { border: none; text-align: center; width: 50%; padding: 0; }
    </style>
</head>
<body>
    <div class="header">
        <h3>BUKU MUTASI PENDUDUK DESA</h3>
        <p class="subtitle">(Lampiran XII — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA LENGKAP</th>
                <th rowspan="2">TEMPAT DAN<br>TGL LAHIR</th>
                <th rowspan="2">JENIS<br>KELAMIN</th>
                <th rowspan="2">KEWARGA-<br>NEGARAAN</th>
                <th colspan="2">DATANG DARI</th>
                <th colspan="2">PINDAH KE</th>
                <th rowspan="2">MENINGGAL<br>(TEMPAT & TGL)</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>ASAL USUL</th>
                <th>TANGGAL</th>
                <th>TUJUAN</th>
                <th>TANGGAL</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
                <th>9</th>
                <th>10</th>
                <th>11</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $mutasi)
                @php
                    $penduduk = $mutasi->penduduk;
                    $ttl = $penduduk ? ($penduduk->tempat_lahir . ', ' . ($penduduk->tanggal_lahir ? \Carbon\Carbon::parse($penduduk->tanggal_lahir)->translatedFormat('d M Y') : '')) : '-';
                    
                    $datangDari = '-';
                    $tglDatang = '-';
                    $pindahKe = '-';
                    $tglPindah = '-';
                    $meninggal = '-';
                    
                    $tglMutasi = $mutasi->tanggal_mutasi ? \Carbon\Carbon::parse($mutasi->tanggal_mutasi)->translatedFormat('d M Y') : '-';

                    if ($mutasi->jenis_mutasi === 'pindah_masuk' || $mutasi->jenis_mutasi === 'kelahiran') {
                        $datangDari = $mutasi->asal_tujuan ?? '-';
                        $tglDatang = $tglMutasi;
                    } elseif ($mutasi->jenis_mutasi === 'pindah_keluar') {
                        $pindahKe = $mutasi->asal_tujuan ?? '-';
                        $tglPindah = $tglMutasi;
                    } elseif ($mutasi->jenis_mutasi === 'kematian') {
                        $lokasi = $mutasi->asal_tujuan ?? 'Tidak diketahui';
                        $meninggal = $tglMutasi . ' di ' . $lokasi;
                    }
                @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $penduduk ? $penduduk->nama : 'Penduduk Terhapus' }}</td>
                    <td>{{ $ttl }}</td>
                    <td class="center">{{ $penduduk ? ($penduduk->jenis_kelamin == 'Laki-Laki' || $penduduk->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P') : '-' }}</td>
                    <td class="center">{{ $penduduk ? ($penduduk->warganegara ?? $penduduk->kewarganegaraan ?? 'WNI') : '-' }}</td>
                    <td>{{ $datangDari }}</td>
                    <td class="center">{{ $tglDatang }}</td>
                    <td>{{ $pindahKe }}</td>
                    <td class="center">{{ $tglPindah }}</td>
                    <td>{{ $meninggal }}</td>
                    <td>{{ $mutasi->alasan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="center">Tidak ada data mutasi penduduk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="ttd">
        <tr>
            <td>
                MENGETAHUI,<br>
                SEKRETARIS DESA<br>
                <br><br><br><br>
                @php
                    $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                @endphp
                <b><u>{{ $sekdes ? $sekdes->nama : '..........................' }}</u></b>
            </td>
            <td>
                {{ \App\Models\DesaSetting::getValue('nama_desa', 'Cibatu') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                KEPALA DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}<br>
                <br><br><br><br>
                @php
                    $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
                @endphp
                <b><u>{{ $kades ? $kades->nama : '..........................' }}</u></b>
            </td>
        </tr>
    </table>
</body>
</html>
