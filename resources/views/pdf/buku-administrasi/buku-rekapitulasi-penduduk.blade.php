<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Rekapitulasi Jumlah Penduduk</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; margin: 10px; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .signature-area { width: 100%; margin-top: 30px; }
        .signature-table { width: 100%; border: none; }
        .signature-table td { border: none; text-align: center; vertical-align: top; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-top: 60px; }
    </style>
</head>
<body>
    @php
        $desa = strtoupper(\App\Models\DesaSetting::getValue('nama_desa', 'CIBATU'));
        $kecamatan = strtoupper(\App\Models\DesaSetting::getValue('kecamatan', 'KECAMATAN'));
        $kabupaten = strtoupper(\App\Models\DesaSetting::getValue('kabupaten', 'KABUPATEN'));
        $kades = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Kepala Desa')->value('nama') ?? '..................');
        $sekdes = strtoupper(\App\Models\StrukturDesa::where('jabatan', 'Sekretaris Desa')->value('nama') ?? '..................');
        $tanggalCetak = \Carbon\Carbon::now()->translatedFormat('d F Y');
    @endphp

    <div class="header">
        <h3>BUKU REKAPITULASI JUMLAH PENDUDUK DESA</h3>
        <p style="font-size: 10px; font-weight: normal; margin-top: 5px; text-transform: none;">(Lampiran XIII — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="3">NOMOR URUT</th>
                <th rowspan="3">NAMA DUSUN / LINGKUNGAN / KEL</th>
                <th colspan="5">JUMLAH PENDUDUK AWAL BULAN</th>
                <th colspan="4">TAMBAHAN BULAN INI</th>
                <th colspan="4">PENGURANGAN BULAN INI</th>
                <th colspan="5">JUMLAH PENDUDUK AKHIR BULAN</th>
                <th rowspan="3">KET</th>
            </tr>
            <tr>
                <th colspan="2">WNA</th>
                <th colspan="2">WNI</th>
                <th rowspan="2">JML</th>
                <th colspan="2">LAHIR</th>
                <th colspan="2">DATANG</th>
                <th colspan="2">MATI</th>
                <th colspan="2">PINDAH</th>
                <th colspan="2">WNA</th>
                <th colspan="2">WNI</th>
                <th rowspan="2">JML</th>
            </tr>
            <tr>
                <th>L</th><th>P</th><th>L</th><th>P</th>
                <th>L</th><th>P</th><th>L</th><th>P</th>
                <th>L</th><th>P</th><th>L</th><th>P</th>
                <th>L</th><th>P</th><th>L</th><th>P</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $no = 1;
                // Accumulators for total bottom row
                $t_awal_wna_l = 0; $t_awal_wna_p = 0; $t_awal_wni_l = 0; $t_awal_wni_p = 0; $t_awal_jml = 0;
                $t_lahir_l = 0; $t_lahir_p = 0; $t_datang_l = 0; $t_datang_p = 0;
                $t_mati_l = 0; $t_mati_p = 0; $t_pindah_l = 0; $t_pindah_p = 0;
                $t_akhir_wna_l = 0; $t_akhir_wna_p = 0; $t_akhir_wni_l = 0; $t_akhir_wni_p = 0; $t_akhir_jml = 0;
            @endphp
            
            @forelse ($data as $item)
                @php
                    $awalJml = $item['awal_wna_l'] + $item['awal_wna_p'] + $item['awal_wni_l'] + $item['awal_wni_p'];
                    $akhirJml = $item['akhir_wna_l'] + $item['akhir_wna_p'] + $item['akhir_wni_l'] + $item['akhir_wni_p'];

                    $t_awal_wna_l += $item['awal_wna_l']; $t_awal_wna_p += $item['awal_wna_p'];
                    $t_awal_wni_l += $item['awal_wni_l']; $t_awal_wni_p += $item['awal_wni_p'];
                    $t_awal_jml += $awalJml;

                    $t_lahir_l += $item['tambah_lahir_l']; $t_lahir_p += $item['tambah_lahir_p'];
                    $t_datang_l += $item['tambah_datang_l']; $t_datang_p += $item['tambah_datang_p'];
                    
                    $t_mati_l += $item['kurang_mati_l']; $t_mati_p += $item['kurang_mati_p'];
                    $t_pindah_l += $item['kurang_pindah_l']; $t_pindah_p += $item['kurang_pindah_p'];
                    
                    $t_akhir_wna_l += $item['akhir_wna_l']; $t_akhir_wna_p += $item['akhir_wna_p'];
                    $t_akhir_wni_l += $item['akhir_wni_l']; $t_akhir_wni_p += $item['akhir_wni_p'];
                    $t_akhir_jml += $akhirJml;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ strtoupper($item['nama_dusun']) }}</td>
                    <td>{{ $item['awal_wna_l'] }}</td>
                    <td>{{ $item['awal_wna_p'] }}</td>
                    <td>{{ $item['awal_wni_l'] }}</td>
                    <td>{{ $item['awal_wni_p'] }}</td>
                    <td>{{ $awalJml }}</td>
                    <td>{{ $item['tambah_lahir_l'] }}</td>
                    <td>{{ $item['tambah_lahir_p'] }}</td>
                    <td>{{ $item['tambah_datang_l'] }}</td>
                    <td>{{ $item['tambah_datang_p'] }}</td>
                    <td>{{ $item['kurang_mati_l'] }}</td>
                    <td>{{ $item['kurang_mati_p'] }}</td>
                    <td>{{ $item['kurang_pindah_l'] }}</td>
                    <td>{{ $item['kurang_pindah_p'] }}</td>
                    <td>{{ $item['akhir_wna_l'] }}</td>
                    <td>{{ $item['akhir_wna_p'] }}</td>
                    <td>{{ $item['akhir_wni_l'] }}</td>
                    <td>{{ $item['akhir_wni_p'] }}</td>
                    <td>{{ $akhirJml }}</td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="21">Tidak ada data rekapitulasi.</td>
                </tr>
            @endforelse
            
            {{-- Total Row --}}
            @if(count($data) > 0)
                <tr style="font-weight: bold; background-color: #f2f2f2;">
                    <td colspan="2">JUMLAH TOTAL</td>
                    <td>{{ $t_awal_wna_l }}</td>
                    <td>{{ $t_awal_wna_p }}</td>
                    <td>{{ $t_awal_wni_l }}</td>
                    <td>{{ $t_awal_wni_p }}</td>
                    <td>{{ $t_awal_jml }}</td>
                    <td>{{ $t_lahir_l }}</td>
                    <td>{{ $t_lahir_p }}</td>
                    <td>{{ $t_datang_l }}</td>
                    <td>{{ $t_datang_p }}</td>
                    <td>{{ $t_mati_l }}</td>
                    <td>{{ $t_mati_p }}</td>
                    <td>{{ $t_pindah_l }}</td>
                    <td>{{ $t_pindah_p }}</td>
                    <td>{{ $t_akhir_wna_l }}</td>
                    <td>{{ $t_akhir_wna_p }}</td>
                    <td>{{ $t_akhir_wni_l }}</td>
                    <td>{{ $t_akhir_wni_p }}</td>
                    <td>{{ $t_akhir_jml }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="signature-area">
        <table class="signature-table">
            <tr>
                <td style="width: 40%;">
                    MENGETAHUI<br>
                    KEPALA DESA {{ $desa }}<br>
                    <div class="signature-name">{{ $kades }}</div>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%;">
                    Desa {{ $desa }}, {{ $tanggalCetak }}<br>
                    SEKRETARIS DESA<br>
                    <div class="signature-name">{{ $sekdes }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
