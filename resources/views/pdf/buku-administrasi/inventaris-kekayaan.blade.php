<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Inventaris dan Kekayaan Desa {{ $tahun }}</title>
    <style>
        @page { size: A3 landscape; margin: 10mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 9px; margin: 0; padding: 0; }
        h3, h4 { text-align: center; margin: 3px 0; text-transform: uppercase; }
        .header { margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table, th, td { border: 1px solid black; }
        th { text-align: center; font-weight: bold; font-size: 8px; padding: 3px 2px; vertical-align: middle; }
        td { padding: 3px 2px; text-align: center; vertical-align: top; font-size: 8px; }
        td.left { text-align: left; }
        .col-number { font-style: italic; font-size: 7px; background-color: #f3f3f3; }
        .ttd-container { width: 100%; margin-top: 20px; page-break-inside: avoid; }
        .ttd-left { float: left; width: 50%; text-align: center; }
        .ttd-right { float: right; width: 50%; text-align: center; }
        .clear { clear: both; }
        .text-center { text-align: center; }
        .subtitle { font-size: 10px; text-align: center; margin-bottom: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <h3>BUKU INVENTARIS DAN KEKAYAAN DESA</h3>
        <p class="subtitle">Tahun Anggaran: <strong>{{ $tahun }}</strong></p>
        <p class="subtitle">(Format Permendagri No. 47 Tahun 2016 — Lampiran Buku Administrasi Umum)</p>
    </div>

    <table>
        <thead>
            {{-- Row 1: Group headers --}}
            <tr>
                <th rowspan="3" style="width:3%">NO</th>
                <th rowspan="3" style="width:14%">JENIS BARANG /<br>BANGUNAN</th>
                <th colspan="5" style="">ASAL BARANG / BANGUNAN</th>
                <th colspan="2">KEADAAN BARANG /<br>BANGUNAN AWAL TAHUN</th>
                <th colspan="4">PENGHAPUSAN BARANG DAN BANGUNAN</th>
                <th colspan="2">KEADAAN BARANG /<br>BANGUNAN AKHIR TAHUN</th>
                <th rowspan="3" style="width:6%">KET.</th>
            </tr>
            {{-- Row 2: Sub-headers --}}
            <tr>
                <th rowspan="2" style="width:5%">DIBELI<br>SENDIRI</th>
                <th colspan="3" style="">BANTUAN</th>
                <th rowspan="2" style="width:5%">SUMBANGAN</th>
                <th rowspan="2" style="width:4%">BAIK</th>
                <th rowspan="2" style="width:4%">RUSAK</th>
                <th rowspan="2" style="width:4%">RUSAK</th>
                <th rowspan="2" style="width:4%">DIJUAL</th>
                <th rowspan="2" style="width:5%">DISUMBANG<br>KAN</th>
                <th rowspan="2" style="width:6%">TGL<br>PENGHAPUSAN</th>
                <th rowspan="2" style="width:4%">BAIK</th>
                <th rowspan="2" style="width:4%">RUSAK</th>
            </tr>
            {{-- Row 3: Bantuan sub-headers --}}
            <tr>
                <th style="width:5%">PEMERINTAH<br>(PUSAT)</th>
                <th style="width:5%">PROVINSI</th>
                <th style="width:5%">KAB/<br>KOTA</th>
            </tr>
            {{-- Row 4: Column numbers (Permendagri) --}}
            <tr class="col-number">
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
                <td>5</td>
                <td>6</td>
                <td>7</td>
                <td>8</td>
                <td>9</td>
                <td>10</td>
                <td>11</td>
                <td>12</td>
                <td>13</td>
                <td>14</td>
                <td>15</td>
                <td>16</td>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="left">
                    {{ $row['nama_barang'] }}
                    @if($row['kode_barang'] !== '-')
                        <br><small style="color:#666">{{ $row['kode_barang'] }}</small>
                    @endif
                </td>
                {{-- Kolom 3-7: Asal Barang --}}
                <td>{{ $row['asal_dibeli'] > 0 ? number_format($row['asal_dibeli'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['asal_bantuan_pusat'] > 0 ? number_format($row['asal_bantuan_pusat'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['asal_bantuan_prov'] > 0 ? number_format($row['asal_bantuan_prov'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['asal_bantuan_kab'] > 0 ? number_format($row['asal_bantuan_kab'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['asal_sumbangan'] > 0 ? number_format($row['asal_sumbangan'], 0, ',', '.') : '-' }}</td>
                {{-- Kolom 8-9: Kondisi Awal Tahun --}}
                <td>{{ $row['awal_baik'] > 0 ? number_format($row['awal_baik'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['awal_rusak'] > 0 ? number_format($row['awal_rusak'], 0, ',', '.') : '-' }}</td>
                {{-- Kolom 10-13: Penghapusan --}}
                <td>{{ $row['hapus_rusak'] > 0 ? number_format($row['hapus_rusak'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['hapus_dijual'] > 0 ? number_format($row['hapus_dijual'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['hapus_disumbangkan'] > 0 ? number_format($row['hapus_disumbangkan'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['tgl_penghapusan'] }}</td>
                {{-- Kolom 14-15: Kondisi Akhir Tahun --}}
                <td>{{ $row['akhir_baik'] > 0 ? number_format($row['akhir_baik'], 0, ',', '.') : '-' }}</td>
                <td>{{ $row['akhir_rusak'] > 0 ? number_format($row['akhir_rusak'], 0, ',', '.') : '-' }}</td>
                {{-- Kolom 16: Keterangan --}}
                <td class="left">{{ $row['keterangan'] ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="16" class="text-center" style="padding: 12px;">
                    Nihil — Tidak ada data inventaris pada tahun {{ $tahun }}
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
