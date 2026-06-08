<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku RKP Desa</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; font-size: 14pt; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 11pt; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9pt; }
        th, td { border: 1px solid black; padding: 6px; }
        th { text-align: center; vertical-align: middle; background-color: #f3f4f6; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer { margin-top: 30px; width: 100%; page-break-inside: avoid; }
        .signature { width: 300px; float: right; text-align: center; }
        .signature-kiri { width: 300px; float: left; text-align: center; }
    </style>
</head>
<body>

    @if(!isset($is_excel) || !$is_excel)
    <div class="header" style="text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px;">
        <h3 style="margin: 0; padding: 0;">BUKU RENCANA KERJA PEMBANGUNAN (RKP) DESA</h3>
        <h4 style="margin: 0; padding: 0; font-weight: normal;">TAHUN {{ request('tahun', date('Y')) }}</h4>
        DESA {{ strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU')) }}<br>
        KECAMATAN {{ strtoupper(\App\Models\DesaSetting::getValue('kecamatan', 'CIBATU')) }}, KABUPATEN {{ strtoupper(\App\Models\DesaSetting::getValue('kabupaten', 'PURWAKARTA')) }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">NAMA PROYEK /<br/>KEGIATAN</th>
                <th rowspan="2">LOKASI</th>
                <th rowspan="2">VOLUME</th>
                <th rowspan="2">SASARAN /<br/>MANFAAT</th>
                <th rowspan="2">SUMBER DANA</th>
                <th rowspan="2">JUMLAH<br/>ANGGARAN (Rp)</th>
                <th rowspan="2">WAKTU<br/>PELAKSANAAN</th>
                <th rowspan="2">PELAKSANA</th>
                <th rowspan="2">SIFAT<br/>PROYEK</th>
                <th rowspan="2">KET</th>
            </tr>
            <tr></tr>
            <tr style="background-color: #f3f4f6;">
                @for ($i = 1; $i <= 11; $i++)
                    <th style="font-size: 8pt; padding: 2px;">{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                @php
                    $waktu = '-';
                    if ($item->tanggal_mulai) {
                        $mulai = \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y');
                        $selesai = $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '?';
                        $waktu = "$mulai - $selesai";
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama_proyek }}</td>
                    <td class="text-center">{{ $item->lokasi ?: '-' }}</td>
                    <td class="text-center">{{ $item->volume ?: '-' }}</td>
                    <td>{{ $item->sasaran ?: '-' }}</td>
                    <td class="text-center">{{ $item->apbdes ? $item->apbdes->sumber_dana : '-' }}</td>
                    <td class="text-right">{{ number_format($item->anggaran, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $waktu }}</td>
                    <td class="text-center">{{ $item->penanggung_jawab ?: '-' }}</td>
                    <td class="text-center" style="text-transform: uppercase;">{{ $item->sifat_proyek ?: 'BARU' }}</td>
                    <td>{{ $item->catatan ?: $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data proyek / pembangunan</td>
                </tr>
            @endforelse
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
