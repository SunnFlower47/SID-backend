<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Aparat Pemerintah Desa</title>
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
        .status-aktif    { color: #065f46; font-weight: bold; }
        .status-nonaktif { color: #991b1b; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h3>Buku Aparat Pemerintah Desa</h3>
        <p class="subtitle">(Lampiran VIII — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">NO</th>
                <th style="width:20%">NAMA LENGKAP</th>
                <th style="width:16%">NIK / NO. HP</th>
                <th style="width:14%">JABATAN</th>
                <th style="width:12%">KATEGORI</th>
                <th style="width:10%">TGL PENGANGKATAN</th>
                <th style="width:10%">TGL BERAKHIR</th>
                <th style="width:8%">STATUS</th>
                <th>ALAMAT</th>
            </tr>
            <tr class="col-number">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="left" style="font-weight: bold;">{{ $row->nama ?? '-' }}</td>
                <td class="center" style="font-family: monospace; font-size: 8px;">
                    {{ $row->nik ?? '-' }}
                    @if($row->no_hp)
                        <br><small>{{ $row->no_hp }}</small>
                    @endif
                </td>
                <td class="center" style="font-weight: bold; text-transform: uppercase; font-size: 8px;">{{ $row->jabatan ?? '-' }}</td>
                <td class="center" style="font-size: 8px;">{{ $row->kategori_label ?? $row->kategori ?? '-' }}</td>
                <td class="center">
                    {{ $row->tanggal_pengangkatan ? \Carbon\Carbon::parse($row->tanggal_pengangkatan)->translatedFormat('d F Y') : '-' }}
                </td>
                <td class="center">
                    {{ $row->tanggal_berakhir ? \Carbon\Carbon::parse($row->tanggal_berakhir)->translatedFormat('d F Y') : '—' }}
                </td>
                <td class="center {{ $row->status_aktif ? 'status-aktif' : 'status-nonaktif' }}">
                    {{ $row->status_aktif ? 'AKTIF' : 'NON-AKTIF' }}
                </td>
                <td class="left" style="font-size: 8px;">{{ $row->alamat ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="center" style="padding:14px; font-style:italic; color:#666;">
                    Nihil — Tidak ada data aparat pemerintah desa.
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
