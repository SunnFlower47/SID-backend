<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Proyek Desa {{ $tahun }}</title>
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

        table.main { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.main thead tr th {
            background: #14532d; color: #fff; font-size: 7pt;
            font-weight: bold; text-transform: uppercase;
            padding: 5px 5px; text-align: center; border: 1px solid #166534;
        }
        table.main tbody tr td { padding: 4px 5px; border: 1px solid #e5e7eb; font-size: 7.5pt; vertical-align: middle; }
        table.main tbody tr:nth-child(even) td { background: #f9fafb; }
        .row-total td { background: #14532d !important; color: #fff !important; font-weight: bold; font-size: 8pt; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }

        .badge { display: inline-block; padding: 2px 6px; border-radius: 3px; font-size: 6.5pt; font-weight: bold; text-transform: uppercase; }
        .s-selesai  { background: #dcfce7; color: #166534; }
        .s-berjalan { background: #dbeafe; color: #1d4ed8; }
        .s-rencana  { background: #fef9c3; color: #854d0e; }
        .s-tunda    { background: #fee2e2; color: #b91c1c; }

        .progress-wrap { background: #e5e7eb; height: 8px; border-radius: 4px; width: 100%; display: block; }
        .progress-bar  { height: 8px; border-radius: 4px; display: block; }

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
            <div class="info">Kec. {{ $desaInfo['kecamatan'] }} · Kab. {{ $desaInfo['kabupaten'] }}</div>
            <div class="info" style="margin-top:1px;">{{ $desaInfo['alamat_lengkap'] }}</div>
        </div>
    </div>

    <div class="judul-box">
        <h1>Laporan Realisasi Proyek Desa</h1>
        <p>Tahun Anggaran {{ $tahun }} {{ $status ? '· Status: ' . strtoupper($status) : '· Semua Status' }}</p>
    </div>

    <div class="meta">
        Dicetak: <b>{{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</b> ·
        Total Proyek: <b>{{ $proyek->count() }}</b> ·
        Selesai: <b>{{ $proyek->where('status', 'selesai')->count() }}</b> ·
        Berjalan: <b>{{ $proyek->where('status', 'berjalan')->count() }}</b>
    </div>

    <table class="main">
        <thead>
            <tr>
                <th style="width:3%">No.</th>
                <th style="width:22%">Nama Proyek</th>
                <th style="width:8%">Jenis</th>
                <th style="width:12%">Lokasi</th>
                <th style="width:10%">Penanggung Jawab</th>
                <th style="width:10%">Periode</th>
                <th style="width:11%">Anggaran (Rp)</th>
                <th style="width:11%">Realisasi (Rp)</th>
                <th style="width:8%">Progress</th>
                <th style="width:7%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proyek as $i => $p)
                @php
                    $prog  = $p->progress ?? 0;
                    $color = $prog >= 100 ? '#16a34a' : ($prog >= 60 ? '#2563eb' : ($prog >= 30 ? '#d97706' : '#9ca3af'));
                    $sClass = 's-' . ($p->status ?? 'rencana');
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>
                        <b>{{ $p->nama_proyek }}</b>
                        @if($p->deskripsi)
                            <br><span style="font-size:6.5pt;color:#6b7280;">{{ Str::limit($p->deskripsi, 60) }}</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size:7pt;">{{ ucfirst($p->jenis ?? '—') }}</td>
                    <td style="font-size:7pt;">{{ $p->lokasi ?? '—' }}</td>
                    <td style="font-size:7pt;">{{ $p->penanggung_jawab ?? '—' }}</td>
                    <td class="text-center" style="font-size:6.5pt;">
                        {{ $p->tanggal_mulai ? $p->tanggal_mulai->format('d/m/Y') : '—' }}<br>
                        s/d {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d/m/Y') : 'Belum selesai' }}
                    </td>
                    <td class="text-right nowrap">{{ number_format($p->anggaran, 0, ',', '.') }}</td>
                    <td class="text-right nowrap">{{ number_format($p->realisasi ?? 0, 0, ',', '.') }}</td>
                    <td>
                        <div style="font-size:7pt;text-align:center;margin-bottom:2px;"><b>{{ $prog }}%</b></div>
                        <span class="progress-wrap">
                            <span class="progress-bar" style="width:{{ $prog }}%;background:{{ $color }};"></span>
                        </span>
                    </td>
                    <td class="text-center"><span class="badge {{ $sClass }}">{{ ucfirst($p->status ?? 'Rencana') }}</span></td>
                </tr>
            @endforeach

            <tr class="row-total">
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right nowrap">Rp {{ number_format($proyek->sum('anggaran'), 0, ',', '.') }}</td>
                <td class="text-right nowrap">Rp {{ number_format($proyek->sum('realisasi'), 0, ',', '.') }}</td>
                <td colspan="2" class="text-center">
                    @php $avgProg = $proyek->count() > 0 ? round($proyek->avg('progress'), 1) : 0; @endphp
                    Avg. Progress: {{ $avgProg }}%
                </td>
            </tr>
        </tbody>
    </table>

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
        Digenerate oleh Sistem Informasi Desa (SID) · {{ now()->format('d/m/Y H:i') }} WIB · Permendagri No. 20 Tahun 2018
    </div>
</body>
</html>
