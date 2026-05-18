<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Realisasi APBDes {{ $tahun }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8pt;
            color: #1a1a2e;
            background: #fff;
        }

        /* ── KOP SURAT ─────────────────────────────── */
        .kop {
            display: table;
            width: 100%;
            border-bottom: 3px solid #166534;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .kop-logo { display: table-cell; width: 60px; vertical-align: middle; text-align: center; }
        .kop-logo img { width: 50px; height: 50px; object-fit: contain; }
        .kop-text { display: table-cell; vertical-align: middle; text-align: center; padding: 0 10px; }
        .kop-text .pemerintah { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #166534; }
        .kop-text .nama-desa  { font-size: 14pt; font-weight: bold; text-transform: uppercase; color: #14532d; letter-spacing: 1px; }
        .kop-text .kecamatan  { font-size: 8pt; color: #4b5563; }
        .kop-text .alamat     { font-size: 7pt; color: #6b7280; margin-top: 2px; }

        /* ── JUDUL LAPORAN ─────────────────────────── */
        .judul-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 4px;
            text-align: center;
            padding: 8px 16px;
            margin-bottom: 10px;
        }
        .judul-box h1 { font-size: 10pt; font-weight: bold; text-transform: uppercase; color: #14532d; letter-spacing: 0.5px; }
        .judul-box h2 { font-size: 9pt; font-weight: bold; color: #166534; margin-top: 2px; }
        .judul-box p  { font-size: 7pt; color: #6b7280; margin-top: 3px; }

        /* ── META INFO ─────────────────────────────── */
        .meta { font-size: 7.5pt; color: #374151; margin-bottom: 8px; }
        .meta span { margin-right: 24px; }
        .meta b { color: #111827; }

        /* ── TABEL UTAMA ─────────────────────────── */
        table.main { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.main thead tr th {
            background: #14532d;
            color: #fff;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            text-align: center;
            border: 1px solid #166534;
        }
        table.main tbody tr td {
            padding: 4px 6px;
            border: 1px solid #e5e7eb;
            font-size: 7.5pt;
            vertical-align: top;
        }
        table.main tbody tr:nth-child(even) td { background: #f9fafb; }

        /* Row Bidang */
        .row-bidang td {
            background: #dcfce7 !important;
            font-weight: bold;
            font-size: 8pt;
            color: #14532d;
            border-top: 1.5px solid #166534;
        }
        /* Row Jenis Sub-total */
        .row-subtotal td {
            background: #f0fdf4 !important;
            font-weight: bold;
            font-size: 7.5pt;
            color: #166534;
            font-style: italic;
        }
        /* Row Grand Total */
        .row-total td {
            background: #14532d !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 8pt;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .nowrap      { white-space: nowrap; }

        /* Progress bar */
        .progress-wrap { background: #e5e7eb; height: 5px; border-radius: 3px; width: 60px; display: inline-block; vertical-align: middle; }
        .progress-bar  { height: 5px; border-radius: 3px; }

        /* ── TTD ─────────────────────────────────── */
        .ttd-section {
            display: table;
            width: 100%;
            margin-top: 16px;
        }
        .ttd-cell { display: table-cell; width: 50%; text-align: center; }
        .ttd-title { font-size: 7.5pt; font-weight: bold; color: #374151; margin-bottom: 50px; text-transform: uppercase; }
        .ttd-name  { font-size: 8pt; font-weight: bold; color: #111827; text-decoration: underline; }
        .ttd-jabatan { font-size: 7pt; color: #6b7280; }

        /* ── FOOTER ──────────────────────────────── */
        .footer { font-size: 6.5pt; color: #9ca3af; text-align: center; margin-top: 10px; border-top: 1px solid #e5e7eb; padding-top: 6px; }

        /* Page break */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">
            @if(!empty($logos['desa']))
                <img src="{{ public_path('storage/' . ltrim(parse_url($logos['desa'], PHP_URL_PATH), '/storage/')) }}" alt="Logo">
            @else
                <div style="width:50px;height:50px;background:#dcfce7;border-radius:50%;line-height:50px;text-align:center;font-weight:bold;color:#166534;font-size:12pt;">D</div>
            @endif
        </div>
        <div class="kop-text">
            <div class="pemerintah">Pemerintah Desa</div>
            <div class="nama-desa">{{ strtoupper($desaInfo['nama_desa']) }}</div>
            <div class="kecamatan">Kecamatan {{ $desaInfo['kecamatan'] }} · Kabupaten {{ $desaInfo['kabupaten'] }} · Provinsi {{ $desaInfo['provinsi'] }}</div>
            <div class="alamat">{{ $desaInfo['alamat_lengkap'] }} | Telp: {{ $desaInfo['telepon'] }}</div>
        </div>
        <div class="kop-logo">
            @if(!empty($logos['kabupaten']))
                <img src="{{ public_path('storage/' . ltrim(parse_url($logos['kabupaten'], PHP_URL_PATH), '/storage/')) }}" alt="Kab">
            @endif
        </div>
    </div>

    <!-- JUDUL -->
    <div class="judul-box">
        <h1>Laporan Realisasi Anggaran Pendapatan &amp; Belanja Desa (APBDes)</h1>
        <h2>Tahun Anggaran {{ $tahun }}{{ $jenis ? ' · ' . strtoupper($jenis) : ' · Semua Jenis' }}</h2>
        <p>Sesuai Permendagri No. 20 Tahun 2018 tentang Pengelolaan Keuangan Desa</p>
    </div>

    <!-- META -->
    <div class="meta">
        <span>Desa : <b>{{ $desaInfo['nama_desa'] }}</b></span>
        <span>Kec : <b>{{ $desaInfo['kecamatan'] }}</b></span>
        <span>Kab : <b>{{ $desaInfo['kabupaten'] }}</b></span>
        <span>Dicetak : <b>{{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</b></span>
    </div>

    <!-- TABEL REALISASI -->
    <table class="main">
        <thead>
            <tr>
                <th style="width:4%">No.</th>
                <th style="width:10%">Kode Rek.</th>
                <th style="width:28%">Uraian / Rekening</th>
                <th style="width:8%">Jenis</th>
                <th style="width:8%">Sumber Dana</th>
                <th style="width:14%">Anggaran (Rp)</th>
                <th style="width:14%">Realisasi (Rp)</th>
                <th style="width:8%">Serapan (%)</th>
                <th style="width:14%">Sisa (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $bidangLabels = [
                    1 => 'Penyelenggaraan Pemerintahan Desa',
                    2 => 'Pelaksanaan Pembangunan Desa',
                    3 => 'Pembinaan Kemasyarakatan Desa',
                    4 => 'Pemberdayaan Masyarakat Desa',
                    5 => 'Penanggulangan Bencana, Kedaruratan & Mendesak',
                ];
                $jenisLabels = [
                    'pendapatan' => 'Pendapatan',
                    'belanja'    => 'Belanja',
                    'pembiayaan' => 'Pembiayaan',
                ];
            @endphp

            @foreach($grouped as $bidang => $items)
                @php
                    $bidangAnggaran  = $items->sum('anggaran');
                    $bidangRealisasi = $items->sum('realisasi');
                    $bidangSisa      = $items->sum('sisa_anggaran');
                    $bidangPct       = $bidangAnggaran > 0 ? round(($bidangRealisasi / $bidangAnggaran) * 100, 1) : 0;
                @endphp

                <!-- ROW BIDANG -->
                <tr class="row-bidang">
                    <td colspan="2" class="text-center">
                        Bidang {{ $bidang ?? '—' }}
                    </td>
                    <td colspan="3">{{ $bidangLabels[$bidang] ?? 'Bidang Lainnya' }}</td>
                    <td class="text-right nowrap">{{ number_format($bidangAnggaran, 0, ',', '.') }}</td>
                    <td class="text-right nowrap">{{ number_format($bidangRealisasi, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $bidangPct }}%</td>
                    <td class="text-right nowrap">{{ number_format($bidangSisa, 0, ',', '.') }}</td>
                </tr>

                @foreach($items->groupBy('jenis') as $jenisKey => $jenisItems)
                    @foreach($jenisItems as $item)
                        @php
                            $pct = $item->anggaran > 0 ? round(($item->realisasi / $item->anggaran) * 100, 1) : 0;
                            $color = $pct >= 90 ? '#16a34a' : ($pct >= 60 ? '#2563eb' : ($pct >= 30 ? '#d97706' : '#9ca3af'));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td class="nowrap" style="font-family: monospace; font-size: 7pt;">{{ $item->kode_rekening }}</td>
                            <td>
                                <b>{{ $item->nama_rekening }}</b>
                                @if($item->kegiatan)
                                    <br><span style="font-size:6.5pt;color:#6b7280;">{{ $item->kegiatan }}</span>
                                @endif
                            </td>
                            <td class="text-center" style="font-size:7pt;">{{ $jenisLabels[$item->jenis] ?? $item->jenis }}</td>
                            <td style="font-size:6.5pt;">{{ $item->sumber_dana_label }}</td>
                            <td class="text-right nowrap">{{ number_format($item->anggaran, 0, ',', '.') }}</td>
                            <td class="text-right nowrap">{{ number_format($item->realisasi, 0, ',', '.') }}</td>
                            <td class="text-center">
                                {{ $pct }}%
                                <br>
                                <span class="progress-wrap">
                                    <span class="progress-bar" style="width:{{ min(60, ($pct/100)*60) }}px;background:{{ $color }};display:block;"></span>
                                </span>
                            </td>
                            <td class="text-right nowrap">{{ number_format($item->sisa_anggaran, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    @php
                        $jSubAngg  = $jenisItems->sum('anggaran');
                        $jSubReal  = $jenisItems->sum('realisasi');
                        $jSubSisa  = $jenisItems->sum('sisa_anggaran');
                        $jSubPct   = $jSubAngg > 0 ? round(($jSubReal / $jSubAngg) * 100, 1) : 0;
                    @endphp
                    <tr class="row-subtotal">
                        <td colspan="5" class="text-right">Sub-Total {{ $jenisLabels[$jenisKey] ?? $jenisKey }}</td>
                        <td class="text-right nowrap">{{ number_format($jSubAngg, 0, ',', '.') }}</td>
                        <td class="text-right nowrap">{{ number_format($jSubReal, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $jSubPct }}%</td>
                        <td class="text-right nowrap">{{ number_format($jSubSisa, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach

            <!-- GRAND TOTAL -->
            @php $grandPct = $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0; @endphp
            <tr class="row-total">
                <td colspan="5" class="text-right">TOTAL KESELURUHAN</td>
                <td class="text-right nowrap">Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</td>
                <td class="text-right nowrap">Rp {{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td class="text-center">{{ $grandPct }}%</td>
                <td class="text-right nowrap">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TTD -->
    <div class="ttd-section">
        <div class="ttd-cell">
            <div class="ttd-title">Mengetahui,<br>Sekretaris Desa</div>
            <div class="ttd-name">{{ $kepalaInfo['nama'] ?? '___________________' }}</div>
            <div class="ttd-jabatan">{{ $desaInfo['nama_desa'] }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}</div>
        </div>
        <div class="ttd-cell">
            <div class="ttd-title">{{ $desaInfo['nama_desa'] }}, {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}<br>Kepala Desa</div>
            <div class="ttd-name">{{ $kepalaInfo['nama'] ?? '___________________' }}</div>
            <div class="ttd-jabatan">{{ $kepalaInfo['jabatan'] ?? 'Kepala Desa' }}</div>
        </div>
    </div>

    <div class="footer">
        Laporan ini digenerate secara otomatis oleh Sistem Informasi Desa (SID) · {{ now()->format('d/m/Y H:i') }} WIB ·
        Permendagri No. 20 Tahun 2018 tentang Pengelolaan Keuangan Desa
    </div>
</body>
</html>
