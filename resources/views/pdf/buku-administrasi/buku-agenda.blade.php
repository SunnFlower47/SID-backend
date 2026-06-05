<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Agenda</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: top;
        }
        th {
            text-align: center;
            background-color: #f3f4f6;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        BUKU AGENDA<br>
        DESA {{ strtoupper($tenant->name ?? 'CIBATU') }}<br>
        KECAMATAN CIBATU, KABUPATEN GARUT
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO. URUT</th>
                <th rowspan="2">TANGGAL SURAT MASUK/KELUAR</th>
                <th colspan="2">SURAT MASUK</th>
                <th colspan="2">SURAT KELUAR</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th>NOMOR DAN TANGGAL SURAT</th>
                <th>PENGIRIM, DAN ISI SINGKAT</th>
                <th>NOMOR DAN TANGGAL SURAT</th>
                <th>DITUJUKAN KEPADA, DAN ISI SINGKAT</th>
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
            @forelse($data as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                    
                    @if($row->jenis_surat === 'Masuk')
                        <td>
                            No: {{ $row->nomor_surat ?? '-' }}<br>
                            Tgl: {{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}
                        </td>
                        <td>
                            <strong>Dari:</strong> {{ $row->pengirim_penerima }}<br>
                            <strong>Isi:</strong> {{ $row->isi_singkat }}
                        </td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    @else
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td>
                            No: {{ $row->nomor_surat ?? '-' }}<br>
                            Tgl: {{ \Carbon\Carbon::parse($row->tanggal_surat)->format('d/m/Y') }}
                        </td>
                        <td>
                            <strong>Kepada:</strong> {{ $row->pengirim_penerima }}<br>
                            <strong>Isi:</strong> {{ $row->isi_singkat }}
                        </td>
                    @endif
                    
                    <td>{{ $row->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data surat</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
