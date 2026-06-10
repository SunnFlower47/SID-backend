<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Bank Desa - {{ $filters['tahun'] ?? date('Y') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 0; padding: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
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
        <div class="font-bold" style="font-size: 14px;">BUKU BANK DESA</div>
        <div>(Lampiran C.6 — Permendagri No. 47 Tahun 2016)</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>NOMOR URUT</th>
                <th>TANGGAL TRANSAKSI</th>
                <th>URAIAN TRANSAKSI</th>
                <th>BUKTI TRANSAKSI</th>
                <th>PEMASUKAN / SETORAN</th>
                <th>PENGELUARAN / PENARIKAN</th>
                <th>SALDO</th>
            </tr>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_mutasi)->format('d/m/Y') }}</td>
                <td>{{ $item->uraian ?? '-' }}</td>
                <td>{{ $item->no_bukti ?? '-' }}</td>
                <td class="text-right">{{ $item->pemasukan ? number_format($item->pemasukan, 2, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $item->pengeluaran ? number_format($item->pengeluaran, 2, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $item->saldo ? number_format($item->saldo, 2, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data transaksi Bank Desa</td>
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
            <div class="font-bold">KAUR KEUANGAN</div>
            <br><br><br><br>
            @php $kaurKeuangan = \App\Models\StrukturDesa::where('kategori', 'kaur_keuangan')->where('status_aktif', true)->first(); @endphp
            <div class="font-bold" style="text-decoration: underline;">{{ $kaurKeuangan ? $kaurKeuangan->nama : '..........................................' }}</div>
        </div>
    </div>
</body>
</html>
