<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Ekspedisi - {{ $filters['tahun'] ?? date('Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mt-4 { margin-top: 16px; }
        .w-full { width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { background-color: #f3f4f6; text-align: center; }
        .ttd-container { width: 100%; margin-top: 30px; }
        .ttd-box { width: 30%; float: left; text-align: center; }
        .ttd-box.right { float: right; }
        .clearfix::after { content: ""; clear: both; display: table; }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <div class="font-bold" style="font-size: 14px;">BUKU EKSPEDISI</div>
        <div>(Lampiran III — Permendagri No. 47 Tahun 2016)</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>NOMOR URUT</th>
                <th>TANGGAL PENGIRIMAN</th>
                <th>TANGGAL DAN NOMOR SURAT</th>
                <th>ISI SINGKAT SURAT YANG DIKIRIM</th>
                <th>DITUJUKAN KEPADA</th>
                <th>KETERANGAN</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d/m/Y') }}</td>
                <td>Tgl: {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}<br>No: {{ $item->nomor_surat ?? '-' }}</td>
                <td>{{ $item->isi_singkat ?? '-' }}</td>
                <td>{{ $item->tujuan ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data Buku Ekspedisi</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container clearfix mt-4">
        <div class="ttd-box">
            <div>Mengetahui,</div>
            <div class="font-bold">KEPALA DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}</div>
            <br><br><br><br>
            @php $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first(); @endphp
            <div class="font-bold" style="text-decoration: underline;">{{ $kades ? $kades->nama : '..........................................' }}</div>
        </div>
        <div class="ttd-box right">
            <div>{{ \App\Models\DesaSetting::getValue('nama_desa', 'Cibatu') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            <div class="font-bold">SEKRETARIS DESA</div>
            <br><br><br><br>
            @php $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first(); @endphp
            <div class="font-bold" style="text-decoration: underline;">{{ $sekdes ? $sekdes->nama : '..........................................' }}</div>
        </div>
    </div>
</body>
</html>
