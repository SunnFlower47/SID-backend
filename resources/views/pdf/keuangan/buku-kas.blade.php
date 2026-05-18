<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Kas Umum - {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8pt; color: #1a1a2e; }

        .kop { display: table; width: 100%; border-bottom: 3px solid #166534; padding-bottom: 8px; margin-bottom: 10px; }
        .kop-logo { display: table-cell; width: 60px; vertical-align: middle; text-align: center; }
        .kop-logo img { width: 50px; height: 50px; object-fit: contain; }
        .kop-text { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10px; }
        .kop-text .nama-desa { font-size: 13pt; font-weight: bold; text-transform: uppercase; color: #14532d; }
        .kop-text .info { font-size: 7.5pt; color: #4b5563; }

        .judul-box { background: #f0fdf4; border: 1px solid #bbf7d0; text-align: center; padding: 8px; margin-bottom: 8px; }
        .judul-box h1 { font-size: 9pt; font-weight: bold; text-transform: uppercase; color: #14532d; }
        .judul-box p  { font-size: 7pt; color: #6b7280; margin-top: 2px; }

        .meta { font-size: 7.5pt; color: #374151; margin-bottom: 8px; }
        .meta td { padding: 1px 10px 1px 0; }
        .meta td b { color: #111827; }

        table.main { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.main thead tr th {
            background: #14532d; color: #fff; font-size: 7pt;
            font-weight: bold; text-transform: uppercase;
            padding: 5px 5px; text-align: center; border: 1px solid #166534;
        }
        table.main tbody tr td { padding: 3px 5px; border: 1px solid #e5e7eb; font-size: 7.5pt; vertical-align: top; }
        table.main tbody tr:nth-child(even) td { background: #f9fafb; }
        .row-total td { background: #14532d !important; color: #fff !important; font-weight: bold; font-size: 8pt; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 3px; font-size: 6.5pt; font-weight: bold; text-transform: uppercase; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }

        .ttd-section { display: table; width: 100%; margin-top: 16px; }
        .ttd-cell { display: table-cell; width: 50%; text-align: center; }
        .ttd-title { font-size: 7.5pt; font-weight: bold; color: #374151; margin-bottom: 50px; text-transform: uppercase; }
        .ttd-name  { font-size: 8pt; font-weight: bold; text-decoration: underline; }
        .ttd-jabatan { font-size: 7pt; color: #6b7280; }

        .footer { font-size: 6.5pt; color: #9ca3af; text-align: center; margin-top: 10px; border-top: 1px solid #e5e7eb; padding-top: 6px; }
    </style>
</head>
<body>
    <!-- KOP -->
    <div class="kop">
        <div class="kop-logo">
            @if(!empty($logos['desa'] ?? ''))
                <img src="{{ public_path('storage/' . ltrim(parse_url($logos['desa'], PHP_URL_PATH), '/storage/')) }}" alt="">
            @else
                <div style="width:50px;height:50px;background:#dcfce7;border-radius:50%;line-height:50px;text-align:center;font-weight:bold;color:#166534;font-size:12pt;">D</div>
            @endif
        </div>
        <div class="kop-text">
            <div style="font-size:8pt;color:#166534;font-weight:bold;text-transform:uppercase;">Pemerintah Desa</div>
            <div class="nama-desa">{{ strtoupper($desaInfo['nama_desa']) }}</div>
            <div class="info">Kec. {{ $desaInfo['kecamatan'] }} · Kab. {{ $desaInfo['kabupaten'] }} · {{ $desaInfo['provinsi'] }}</div>
            <div class="info" style="margin-top:1px;">{{ $desaInfo['alamat_lengkap'] }} | Telp: {{ $desaInfo['telepon'] }}</div>
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul-box">
        <h1>Buku Kas Umum — Rekapitulasi Pengeluaran Desa</h1>
        @if($apbdes)
            <p>Rekening: [{{ $apbdes->kode_rekening }}] {{ $apbdes->nama_rekening }} · Tahun Anggaran {{ $tahun }}</p>
        @else
            <p>Seluruh Rekening · Tahun Anggaran {{ $tahun }}</p>
        @endif
    </div>

    <!-- META -->
    <table class="meta">
        <tr>
            <td>Desa</td><td>: <b>{{ $desaInfo['nama_desa'] }}</b></td>
            <td>Tahun</td><td>: <b>{{ $tahun }}</b></td>
            <td>Dicetak</td><td>: <b>{{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</b></td>
        </tr>
        @if($apbdes)
        <tr>
            <td>Rekening</td><td>: <b>{{ $apbdes->kode_rekening }} — {{ $apbdes->nama_rekening }}</b></td>
            <td>Anggaran</td><td>: <b>Rp {{ number_format($apbdes->anggaran, 0, ',', '.') }}</b></td>
            <td>Realisasi</td><td>: <b>Rp {{ number_format($apbdes->realisasi, 0, ',', '.') }}</b></td>
        </tr>
        @endif
    </table>

    <!-- TABEL BUKU KAS -->
    <table class="main">
        <thead>
            <tr>
                <th style="width:4%">No.</th>
                <th style="width:10%">No. Bukti</th>
                <th style="width:11%">Tanggal</th>
                <th style="width:26%">Nama Pengeluaran</th>
                <th style="width:19%">Rekening (Kode)</th>
                <th style="width:8%">Jenis Bukti</th>
                <th style="width:13%">Jumlah (Rp)</th>
                <th style="width:9%">SPJ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($histori as $i => $item)
                @php $pct = $item->apbdes ? round(($item->jumlah / max($item->apbdes->anggaran, 1)) * 100, 1) : 0; @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="nowrap" style="font-family: monospace; font-size: 6.5pt;">{{ $item->no_bukti ?? '—' }}</td>
                    <td class="text-center nowrap">{{ $item->tanggal_pengeluaran ? $item->tanggal_pengeluaran->format('d/m/Y') : '—' }}</td>
                    <td>
                        <b>{{ $item->nama_pengeluaran }}</b>
                        @if($item->keterangan)
                            <br><span style="font-size:6.5pt;color:#6b7280;">{{ $item->keterangan }}</span>
                        @endif
                    </td>
                    <td style="font-size:7pt;">
                        @if($item->apbdes)
                            [{{ $item->apbdes->kode_rekening }}] {{ Str::limit($item->apbdes->nama_rekening, 30) }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge badge-green">{{ ucfirst($item->jenis_bukti ?? '—') }}</span>
                    </td>
                    <td class="text-right nowrap"><b>{{ number_format($item->jumlah, 0, ',', '.') }}</b></td>
                    <td class="text-center">
                        <span class="badge {{ $item->spj_status === 'sudah' ? 'badge-green' : 'badge-yellow' }}">
                            {{ $item->spj_status === 'sudah' ? 'Sudah' : 'Belum' }}
                        </span>
                    </td>
                </tr>
            @endforeach

            <!-- TOTAL -->
            <tr class="row-total">
                <td colspan="6" class="text-right">TOTAL PENGELUARAN</td>
                <td class="text-right nowrap">Rp {{ number_format($histori->sum('jumlah'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <!-- TTD -->
    <div class="ttd-section">
        <div class="ttd-cell">
            <div class="ttd-title">Mengetahui,<br>Sekretaris Desa</div>
            <div class="ttd-name">________________________</div>
        </div>
        <div class="ttd-cell">
            <div class="ttd-title">{{ $desaInfo['nama_desa'] }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}<br>Kepala Desa</div>
            <div class="ttd-name">{{ $kepalaInfo['nama'] ?? '________________________' }}</div>
            <div class="ttd-jabatan">{{ $kepalaInfo['jabatan'] ?? 'Kepala Desa' }}</div>
        </div>
    </div>

    <div class="footer">
        Digenerate oleh Sistem Informasi Desa (SID) · {{ now()->format('d/m/Y H:i') }} WIB · Sesuai Permendagri No. 20 Tahun 2018
    </div>
</body>
</html>
