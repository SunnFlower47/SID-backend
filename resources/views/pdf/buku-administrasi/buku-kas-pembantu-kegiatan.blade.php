<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Kas Pembantu Kegiatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @if(!isset($is_excel) || !$is_excel)
    <div class="header" style="text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px;">
        <h3 style="margin: 0; padding: 0;">BUKU KAS PEMBANTU KEGIATAN</h3>
        <h4 style="margin: 0; padding: 0; font-weight: normal;">TAHUN {{ request('tahun', date('Y')) }}</h4>
        DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}<br>
        KECAMATAN {{ strtoupper(\App\Models\DesaSetting::getValue('kecamatan', 'CIBATU')) }}, KABUPATEN {{ strtoupper(\App\Models\DesaSetting::getValue('kabupaten', 'PURWAKARTA')) }}
    </div>
    @endif

    @php
        $kegiatan = null;
        if (!empty($filters['apbdes_id'])) {
            $kegiatan = \App\Models\Apbdes::find($filters['apbdes_id']);
        }
    @endphp

    @if(!isset($is_excel) || !$is_excel)
    <div style="margin-bottom: 10px;">
        <strong>Kegiatan: </strong> {{ $kegiatan ? $kegiatan->kode_rekening . ' - ' . $kegiatan->nama_rekening : 'Semua Kegiatan (Belum Dipilih)' }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="15%">TANGGAL</th>
                <th width="30%">URAIAN</th>
                <th width="15%">PENERIMAAN (Rp)</th>
                <th width="15%">PENGELUARAN (Rp)</th>
                <th width="10%">NOMOR BUKTI</th>
                <th width="10%">SALDO (Rp)</th>
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
            @if(count($data) > 0)
                @foreach($data as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_pengeluaran)->translatedFormat('d M Y') }}</td>
                        <td>{{ $item->nama_pengeluaran }}</td>
                        <td class="text-right">{{ $item->penerimaan > 0 ? number_format($item->penerimaan, 2, ',', '.') : '-' }}</td>
                        <td class="text-right">{{ $item->pengeluaran > 0 ? number_format($item->pengeluaran, 2, ',', '.') : '-' }}</td>
                        <td class="text-center">{{ $item->no_bukti ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->saldo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(!isset($is_excel) || !$is_excel)
    <table style="width: 100%; border: none; margin-top: 30px; text-align: center; page-break-inside: avoid;">
        <tr>
            <td style="border: none; width: 50%;">
                MENGETAHUI,<br>
                SEKRETARIS DESA<br>
                <br><br><br><br>
                @php
                    $sekdes = \App\Models\StrukturDesa::where('kategori', 'sekretaris')->where('status_aktif', true)->first();
                @endphp
                <b><u>{{ $sekdes ? $sekdes->nama : '..........................' }}</u></b>
            </td>
            <td style="border: none; width: 50%;">
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
    @endif

</body>
</html>
