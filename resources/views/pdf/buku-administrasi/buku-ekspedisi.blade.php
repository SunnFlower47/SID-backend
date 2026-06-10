<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Ekspedisi - {{ $filters['tahun'] ?? date('Y') }}</title>
    <style>
        @page { size: Legal landscape; margin: 12mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10px; margin: 0; padding: 0; }
        h3, h4 { text-align: center; margin: 3px 0; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 10px; text-align: center; margin: 2px 0; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table, th, td { border: 1px solid black; }
        th { text-align: center; font-weight: bold; font-size: 9px; padding: 4px 3px; vertical-align: middle; background-color: #f0f0f0; }
        td { padding: 4px 3px; vertical-align: top; font-size: 9px; }
        td.center { text-align: center; }
        td.left { text-align: left; }
        .col-number { font-style: italic; font-size: 8px; background-color: #f9f9f9; text-align: center; }
        .ttd-container { width: 100%; margin-top: 24px; }
        .ttd-left { float: left; width: 45%; text-align: center; font-size: 10px; }
        .ttd-right { float: right; width: 45%; text-align: center; font-size: 10px; }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Buku Ekspedisi</h3>
        <p class="subtitle">(Lampiran III — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">NOMOR URUT</th>
                <th style="width:15%">TANGGAL PENGIRIMAN</th>
                <th style="width:20%">TANGGAL DAN NOMOR SURAT</th>
                <th style="width:25%">ISI SINGKAT SURAT YANG DIKIRIM</th>
                <th style="width:20%">DITUJUKAN KEPADA</th>
                <th style="width:15%">KETERANGAN</th>
            </tr>
            <tr class="col-number">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d/m/Y') }}</td>
                <td class="left">Tgl: {{ \Carbon\Carbon::parse($item->tanggal_surat)->format('d/m/Y') }}<br>No: {{ $item->nomor_surat ?? '-' }}</td>
                <td class="left">{{ $item->isi_singkat ?? '-' }}</td>
                <td class="left">{{ $item->tujuan ?? '-' }}</td>
                <td class="left">{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center" style="padding:14px; font-style:italic; color:#666;">
                    Nihil — Tidak ada data Buku Ekspedisi.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-left">
            <p>Mengetahui,</p>
            <p><strong>KEPALA DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}</strong></p>
            <br><br><br><br>
            @php $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first(); @endphp
            <p><u><strong>{{ $kades ? $kades->nama : '..........................................' }}</strong></u></p>
        </div>
        <div class="ttd-right">
            <p>{{ \App\Models\DesaSetting::getValue('nama_desa', 'Cibatu') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>SEKRETARIS DESA</strong></p>
            <br><br><br><br>
            @php $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first(); @endphp
            <p><u><strong>{{ $sekdes ? $sekdes->nama : '..........................................' }}</strong></u></p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
