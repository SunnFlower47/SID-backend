<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk Penduduk</title>
    <style>
        @page { size: landscape; margin: 1cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; vertical-align: top; }
        th { background-color: #f0f0f0; }
        .header { text-align: center; margin-bottom: 14px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 0; padding: 0; }
        .row-nomer th { font-style: italic; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header" style="margin-bottom: 5px;">
        <h3>BUKU INDUK PENDUDUK DESA</h3>
        <p style="font-size: 10px; font-weight: normal; margin-top: 5px; text-transform: none;">(Lampiran XI — Permendagri No. 47 Tahun 2016)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" width="3%">NOMOR URUT</th>
                <th rowspan="2" width="10%">NAMA LENGKAP / PANGGILAN</th>
                <th rowspan="2" width="5%">JENIS KELAMIN</th>
                <th rowspan="2" width="6%">STATUS PERKAWINAN</th>
                <th colspan="2" width="12%">TEMPAT & TANGGAL LAHIR</th>
                <th rowspan="2" width="6%">AGAMA</th>
                <th rowspan="2" width="8%">PENDIDIKAN TERAKHIR</th>
                <th rowspan="2" width="8%">PEKERJAAN</th>
                <th rowspan="2" width="5%">DAPAT MEMBACA HURUF</th>
                <th rowspan="2" width="6%">KEWARGANEGARAAN</th>
                <th rowspan="2" width="12%">ALAMAT LENGKAP</th>
                <th rowspan="2" width="7%">KEDUDUKAN DLM KELUARGA</th>
                <th rowspan="2" width="7%">NIK</th>
                <th rowspan="2" width="7%">NO. KK</th>
                <th rowspan="2" width="5%">KET</th>
            </tr>
            <tr>
                <th width="6%">TEMPAT LAHIR</th>
                <th width="6%">TGL</th>
            </tr>
            <tr class="row-nomer">
                @for($i=1; $i<=16; $i++)
                    <th>{{ $i }}</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->nama }}</td>
                    <td class="text-center">{{ $item->jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P' }}</td>
                    <td class="text-center">{{ $item->status_perkawinan }}</td>
                    <td>{{ $item->tempat_lahir }}</td>
                    <td class="text-center">{{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->format('d/m/Y') : '' }}</td>
                    <td class="text-center">{{ $item->agama }}</td>
                    <td>{{ $item->pendidikan }}</td>
                    <td>{{ $item->pekerjaan }}</td>
                    <td class="text-center">-</td> <!-- Tidak ada data 'Dapat Membaca Huruf' di DB kita -->
                    <td class="text-center">{{ $item->warganegara ?? 'WNI' }}</td>
                    <td>{{ $item->alamat }} RT {{ $item->rt_label }} / RW {{ $item->rw_label }}</td>
                    <td class="text-center">{{ $item->kedudukan_keluarga }}</td>
                    <td class="text-center">{{ $item->nik }}</td>
                    <td class="text-center">{{ $item->nkk ?? ($item->kartuKeluarga->nkk ?? '') }}</td>
                    <td>{{ $item->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="16" class="text-center" style="padding: 20px;">Belum ada data penduduk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
