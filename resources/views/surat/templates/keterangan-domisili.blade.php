<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Domisili - {{ $nama }}</title>
    <style>
        @page {
            size: 330mm 215mm; /* F4 Landscape */
            margin: 20mm;
        }

        body {
            font-family: "Times New Roman", serif;
            font-size: 10pt;
            background: #fff;
        }

        .container {
            width: 440px;
            min-height: 670px;
            margin-left: 30px;
            border: 2px solid #000;
            padding: 10px 20px;
        }

        /* KOP */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 2px 4px;
        }

        .kop-table .logo-cell {
            width: 55px;
            text-align: center;
        }

        .kop-table .logo-cell img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .kop-table .teks-kop {
            text-align: center;
            line-height: 1.3;
        }

        .kop-table .teks-kop .pemerintah { font-size: 8pt; }
        .kop-table .teks-kop .kecamatan  { font-size: 9pt; }
        .kop-table .teks-kop .desa       { font-size: 14pt; font-weight: bold; letter-spacing: 1px; }
        .kop-table .teks-kop .alamat      { font-size: 7.5pt; }

        .garis-kop {
            border-bottom: 3px double #000;
            margin: 6px 0;
        }

        /* JUDUL */
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 8px;
            font-size: 11pt;
            text-transform: uppercase;
        }

        .subtitle {
            text-align: center;
            margin-bottom: 8px;
            font-size: 10pt;
        }

        /* KONTEN */
        .content {
            font-size: 10pt;
            text-align: justify;
        }

        .content p {
            margin: 2px 0;
            text-indent: 20px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        .data-table td {
            padding: 1px 2px;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.3;
        }

        .label  { width: 130px; white-space: nowrap; }
        .colon  { width: 10px; text-align: center; }

        .berlaku {
            font-weight: bold;
            margin-top: 4px;
            font-size: 10pt;
        }

        /* TTD */
        .footer { margin-top: 10px; width: 100%; font-size: 10pt; }

        .ttd-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .ttd-kiri  { text-align: center; width: 38%; }
        .ttd-kanan { text-align: center; width: 38%; }

        .foto-box {
            width: 70px;
            height: 90px;
            border: 1px solid #000;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 4px;
            font-size: 8pt;
            color: #555;
        }

        .foto-box img { width: 100%; height: 100%; object-fit: cover; }

        .ttd-ruang { height: 55px; }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            font-size: 10pt;
        }

        @media print {
            body { margin: 0; background: white; -webkit-print-color-adjust: exact; }
            .container { width: 440px; margin-left: 30px; border: 2px solid #000; }
            @page { margin: 10mm; }
        }
    </style>
</head>
<body>

@php
    function intToRomanDomisili($number) {
        $map = ['M'=>1000,'CM'=>900,'D'=>500,'CD'=>400,'C'=>100,'XC'=>90,'L'=>50,'XL'=>40,'X'=>10,'IX'=>9,'V'=>5,'IV'=>4,'I'=>1];
        $result = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) { $number -= $int; $result .= $roman; break; }
            }
        }
        return $result;
    }

    $tanggal_surat_safe = isset($tanggal_surat) ? $tanggal_surat : \Carbon\Carbon::now();
    $bulanSurat = \Carbon\Carbon::parse($tanggal_surat_safe)->format('n');
    $tahunSurat = \Carbon\Carbon::parse($tanggal_surat_safe)->format('Y');

    $nama_kabupaten = $desa['nama_kabupaten'] ?? $desa->nama_kabupaten ?? 'Purwakarta';
    $nama_kecamatan = $desa['nama_kecamatan'] ?? $desa->nama_kecamatan ?? 'Cibatu';
    $nama_desa      = $desa['nama_desa']      ?? $desa->nama_desa      ?? 'Cibatu';
    $alamat_desa    = $desa['alamat']         ?? $desa->alamat         ?? '';

    $kades_val   = $kepala_desa ?? null;
    $nama_kepala = '';
    if (is_object($kades_val))    $nama_kepala = $kades_val->nama ?? '';
    elseif (is_array($kades_val)) $nama_kepala = $kades_val['nama'] ?? '';
    else                          $nama_kepala = (string) $kades_val;
    if (empty($nama_kepala))      $nama_kepala = '....................';

    // Gunakan $penduduk (data utama) atau fallback ke data_tambahan jika perlu
    $p = $penduduk ?? null;
    $dt = $data_tambahan ?? [];
    
    // Data Pribadi (Prioritas Manual Form -> Database)
    $nama          = $dt['nama'] ?? ($p->nama ?? '....................');
    $nik           = $dt['nik'] ?? ($p->nik ?? '....................');
    $tempat_lahir  = $dt['tempat_lahir'] ?? ($p->tempat_lahir ?? '........');
    $tanggal_lahir = $dt['tanggal_lahir'] ?? ($p->tanggal_lahir ?? null);
    $jenis_kelamin = $dt['jenis_kelamin'] ?? ($p->jenis_kelamin ?? '');
    $agama         = $dt['agama'] ?? ($p->agama ?? '........');
    $status        = $dt['status_perkawinan'] ?? ($p->status_perkawinan ?? '........');
    $pekerjaan     = $dt['pekerjaan'] ?? ($p->pekerjaan ?? '........');

    // Label Wilayah (RT/RW/Dusun di Desa Sekarang)
    $rt_val = $dt['rt'] ?? ($p->rt_label ?? '');
    $rw_val = $dt['rw'] ?? ($p->rw_label ?? '');
    $dusun  = $dt['dusun'] ?? ($p->dusun_label ?? '');

    // Alamat Domisili Sekarang (Di Desa Ini)
    $alamat_domisili = $dt['alamat_tinggal'] ?? ($p->alamat ?? '');
    if ($rt_val) $alamat_domisili .= ' RT. ' . str_pad($rt_val, 3, '0', STR_PAD_LEFT);
    if ($rw_val) $alamat_domisili .= '/RW. ' . str_pad($rw_val, 3, '0', STR_PAD_LEFT);

    // Alamat Asal
    $alamat_asal = $dt['alamat_asal'] ?? ($p->alamat_asal ?? '-');
@endphp

<div class="container">

    {{-- KOP SURAT --}}
    <table class="kop-table">
        <tr>
            @if(!empty($desa->logo ?? $desa['logo'] ?? null))
            <td class="logo-cell">
                <img src="{{ public_path('storage/' . ($desa->logo ?? $desa['logo'])) }}" alt="Logo">
            </td>
            @endif
            <td class="teks-kop">
                <div class="pemerintah">PEMERINTAH KABUPATEN {{ strtoupper($nama_kabupaten) }}</div>
                <div class="kecamatan">KECAMATAN {{ strtoupper($nama_kecamatan) }}</div>
                <div class="desa">DESA {{ strtoupper($nama_desa) }}</div>
                <div class="alamat">{{ $alamat_desa }}</div>
            </td>
        </tr>
    </table>
    <div class="garis-kop"></div>

    {{-- JUDUL --}}
    <div class="title">SURAT KETERANGAN DOMISILI</div>
    <div class="subtitle">
        No. {{ $kode_surat }} / {{ $nomor_urut }} / {{ $kode_desa }} / {{ $bulan_romawi }} / {{ $tahun_surat }}
    </div>

    {{-- KONTEN --}}
    <div class="content">
        <p>Yang &nbsp;tanda tangan di bawah ini Kepala Desa {{ $nama_desa }} Kecamatan {{ $nama_kecamatan }}
        Kabupaten {{ $nama_kabupaten }}, menerangkan bahwa :</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama</td>
                <td class="colon">:</td>
                <td><b>{{ strtoupper($nama) }}</b></td>
            </tr>
            <tr>
                <td class="label">Tempat/Tgl.Lahir</td>
                <td class="colon">:</td>
                <td>{{ $tempat_lahir }}, {{ $tanggal_lahir ? \Carbon\Carbon::parse($tanggal_lahir)->format('d-m-Y') : '........' }}</td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td>{{ $jenis_kelamin == 'L' || $jenis_kelamin == 'LAKI-LAKI' ? 'Laki - Laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <td class="label">Bangsa/Agama</td>
                <td class="colon">:</td>
                <td>Indonesia/{{ $agama }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="colon">:</td>
                <td>{{ $status }}</td>
            </tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td class="colon">:</td>
                <td>{{ $pekerjaan }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Asal</td>
                <td class="colon">:</td>
                <td>{{ $alamat_asal }}</td>
            </tr>
            <tr>
                <td class="label">Alamat Domisili Sekarang</td>
                <td class="colon">:</td>
                <td>
                    {{ $alamat_domisili }}<br>
                    @if($dusun)Kp. {{ $dusun }}<br>@endif
                    Desa {{ $nama_desa }} Kec. {{ $nama_kecamatan }}<br>
                    Kab. {{ $nama_kabupaten }}
                </td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td>{{ $nik }}</td>
            </tr>
        </table>

        <p>Nama tersebut diatas adalah benar &nbsp;telah berdomisili dan tercatat dalam buku
        Induk kependudukan sementara desa {{ $nama_desa }}, Berdasarkan catatan dan penelitian
        serta data yang ada pada kami bahwa nama tersebut benar berdomisili pada alamat tersebut diatas.</p>

        <p style="margin-top: 4px;">
            &nbsp;&nbsp;&nbsp;&nbsp;Demikian Surat Keterangan ini kami buat dengan sebenarnya agar yang
            berkepentingan mengetahui dan untuk dipergunakan sebagaimana mestinya.
        </p>

        <p class="berlaku">
            Berlaku s/d : {{ isset($data_tambahan['tanggal_berlaku']) ? \Carbon\Carbon::parse($data_tambahan['tanggal_berlaku'])->isoFormat('D MMMM Y') : \Carbon\Carbon::parse($tanggal_surat_safe)->addDays(30)->isoFormat('D MMMM Y') }}
        </p>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="footer">
        <div class="ttd-wrapper">

            <div class="ttd-kiri">
                <p>Yang bersangkutan</p>
                <div class="foto-box">
                    @php
                        $foto_path = $data_tambahan['foto'] ?? ($p->foto ?? null);
                    @endphp
                    @if(!empty($foto_path))
                        <img src="{{ public_path('storage/' . $foto_path) }}" alt="Foto">
                    @else
                        Foto 3x4
                    @endif
                </div>
                <div class="ttd-nama">{{ strtoupper($nama) }}</div>
            </div>

            <div class="ttd-kanan">
                <p>
                    {{ $nama_desa }}, {{ \Carbon\Carbon::parse($tanggal_surat_safe)->isoFormat('D MMMM Y') }}<br>
                    @if($is_sekdes ?? false)
                        a/n Kepala Desa {{ $nama_desa }}
                    @else
                        Kepala Desa {{ $nama_desa }}
                    @endif
                </p>
                <div class="ttd-ruang"></div>
                <div class="ttd-nama">{{ strtoupper($nama_kepala) }}</div>
            </div>

        </div>
    </div>

</div>
</body>
</html>
