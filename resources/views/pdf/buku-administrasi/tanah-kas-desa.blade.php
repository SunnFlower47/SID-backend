<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Tanah Kas Desa</title>
    <style>
        @page { size: A3 landscape; margin: 12mm; }
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
        .baik        { color: #065f46; font-weight: bold; }
        .rusak-ringan{ color: #92400e; font-weight: bold; }
        .rusak-berat { color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h3>Buku Tanah Kas Desa</h3>
        <p class="subtitle">(Lampiran VI — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">NO</th>
                <th style="width:20%">NAMA / JENIS TANAH</th>
                <th style="width:8%">KODE BARANG</th>
                <th style="width:12%">LOKASI</th>
                <th style="width:8%">LUAS (m²)</th>
                <th style="width:12%">NO. SERTIFIKAT / BUKTI</th>
                <th style="width:10%">TGL PEROLEHAN</th>
                <th style="width:10%">ASAL USUL</th>
                <th style="width:7%">KONDISI</th>
                <th>KETERANGAN</th>
            </tr>
            <tr class="col-number">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td>
            </tr>
        </thead>
        <tbody>
            @php $totalLuas = 0; @endphp
            @forelse($data as $index => $row)
            @php $totalLuas += $row->saldo_kwantitas ?? 0; @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="left" style="font-weight: bold;">
                    {{ $row->nama_barang_override ?: ($row->barang->nama_barang ?? '-') }}
                </td>
                <td class="center" style="font-family: monospace; font-size: 8px;">
                    {{ $row->barang->kode_barang ?? '-' }}
                </td>
                <td class="left">{{ $row->lokasi ?? '-' }}</td>
                <td class="center">
                    {{ $row->saldo_kwantitas > 0 ? number_format($row->saldo_kwantitas, 2, ',', '.') : '-' }}
                </td>
                <td class="center" style="font-family: monospace; font-size: 8px;">
                    {{ $row->no_sertifikat ?? '-' }}
                </td>
                <td class="center">
                    {{ $row->tanggal_perolehan ? \Carbon\Carbon::parse($row->tanggal_perolehan)->translatedFormat('d F Y') : '-' }}
                </td>
                <td class="center">{{ $row->asal_usul ?? '-' }}</td>
                <td class="center {{ str_replace('_', '-', $row->kondisi ?? '') }}">
                    {{ match($row->kondisi) {
                        'baik'         => 'Baik',
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat'  => 'Rusak Berat',
                        default        => '-'
                    } }}
                </td>
                <td class="left">{{ $row->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="center" style="padding:14px; font-style:italic; color:#666;">
                    Nihil — Tidak ada data tanah kas desa.
                </td>
            </tr>
            @endforelse
            @if($data->count() > 0)
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td colspan="4" class="center">JUMLAH TOTAL</td>
                <td class="center">{{ number_format($totalLuas, 2, ',', '.') }} m²</td>
                <td colspan="5"></td>
            </tr>
            @endif
        </tbody>
    </table>

    <div class="ttd-container">
                <div class="ttd-left">
            <p>Mengetahui,</p>
            <p><strong>KEPALA DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}</strong></p>
            <br><br><br><br>
            @php
                $kades = \App\Models\StrukturDesa::where('kategori', 'kepala_desa')->where('status_aktif', true)->first();
            @endphp
            <p><u><strong>{{ $kades ? $kades->nama : '..........................................' }}</strong></u></p>
        </div>
        <div class="ttd-right">
            <p>{{ \App\Models\DesaSetting::getValue('nama_desa', 'Cibatu') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>SEKRETARIS DESA</strong></p>
            <br><br><br><br>
            @php
                $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
            @endphp
            <p><u><strong>{{ $sekdes ? $sekdes->nama : '..........................................' }}</strong></u></p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
