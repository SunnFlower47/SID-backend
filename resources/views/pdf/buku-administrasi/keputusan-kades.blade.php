<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Keputusan Kepala Desa</title>
    <style>
        @page { size: Legal landscape; margin: 12mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 10px; margin: 0; padding: 0; }
        h3, h4 { text-align: center; margin: 3px 0; text-transform: uppercase; letter-spacing: 1px; }
        .subtitle { font-size: 10px; text-align: center; margin: 2px 0; }
        .header { margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
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
        <h3>Buku Keputusan Kepala Desa</h3>
        <p class="subtitle">(Lampiran V — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">NO</th>
                <th style="width:18%">NOMOR KEPUTUSAN</th>
                <th style="width:10%">TANGGAL DITETAPKAN</th>
                <th>TENTANG / JUDUL KEPUTUSAN</th>
                <th style="width:12%">DITETAPKAN OLEH</th>
                <th style="width:14%">KETERANGAN</th>
            </tr>
            <tr class="col-number">
                <td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center" style="font-family: monospace; font-size: 8px;">{{ $row->nomor_keputusan ?? '-' }}</td>
                <td class="center">
                    {{ $row->tanggal_ditetapkan ? \Carbon\Carbon::parse($row->tanggal_ditetapkan)->translatedFormat('d F Y') : '-' }}
                </td>
                <td class="left">{{ $row->judul_keputusan ?? '-' }}</td>
                <td class="center">{{ $row->author->name ?? 'Kepala Desa' }}</td>
                <td class="left">{{ $row->keterangan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center" style="padding:14px; font-style:italic; color:#666;">
                    Nihil — Tidak ada data keputusan kepala desa.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container">
        <div class="ttd-left">
            <p>Mengetahui,</p>
            <p><strong>KEPALA DESA CIBATU</strong></p>
            <br><br><br><br>
            <p>( .......................................... )</p>
        </div>
        <div class="ttd-right">
            <p>Cibatu, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p><strong>SEKRETARIS DESA</strong></p>
            <br><br><br><br>
            <p>( .......................................... )</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
