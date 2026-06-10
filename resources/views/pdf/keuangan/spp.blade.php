<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Permintaan Pembayaran (SPP)</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .w-full { width: 100%; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-8 { margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        .table-bordered th, .table-bordered td { border: 1px solid #000; padding: 6px; }
        .table-noborder td { padding: 4px; vertical-align: top; }
        .header-line { border-bottom: 3px solid #000; margin-top: 10px; margin-bottom: 15px; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

    <div class="text-center font-bold uppercase mb-2">
        <h3 style="margin: 0;">PEMERINTAH KABUPATEN {{ $desaInfo['kabupaten'] ?? '...' }}</h3>
        <h3 style="margin: 0;">KECAMATAN {{ $desaInfo['kecamatan'] ?? '...' }}</h3>
        <h2 style="margin: 0;">DESA {{ $desaInfo['nama_desa'] ?? '...' }}</h2>
    </div>
    <div class="header-line"></div>

    <div class="text-center font-bold mb-4">
        <h3 style="margin: 0; text-decoration: underline;">SURAT PERMINTAAN PEMBAYARAN (SPP)</h3>
        <p style="margin: 0; font-weight: normal;">No: {{ $pengeluaran->no_bukti ?? '......../SPP/'.date('Y') }}</p>
    </div>

    <table class="table-noborder mb-4">
        <tr>
            <td width="20%">Tahun Anggaran</td>
            <td width="5%">:</td>
            <td width="75%">{{ $pengeluaran->apbdes->tahun }}</td>
        </tr>
        <tr>
            <td>Bidang</td>
            <td>:</td>
            <td>{{ $pengeluaran->apbdes->bidang }}</td>
        </tr>
        <tr>
            <td>Kegiatan</td>
            <td>:</td>
            <td>{{ $pengeluaran->apbdes->kegiatan }}</td>
        </tr>
        <tr>
            <td>Waktu Pelaksanaan</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p>Berdasarkan Peraturan Desa tentang APBDesa, dengan ini kami mengajukan Surat Permintaan Pembayaran sebagai berikut:</p>

    <table class="table-bordered w-full mb-4">
        <thead>
            <tr>
                <th width="15%">Kode Rekening</th>
                <th width="35%">Uraian</th>
                <th width="15%">Pagu Anggaran</th>
                <th width="15%">Pencairan S.d Lalu</th>
                <th width="20%">Permintaan Sekarang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">{{ $pengeluaran->apbdes->kode_rekening }}</td>
                <td>
                    <b>{{ $pengeluaran->apbdes->nama_rekening }}</b><br/> 
                    <i>{{ $pengeluaran->nama_pengeluaran }}</i>
                    @if($pengeluaran->nama_penerima)
                    <br/><br/>Penerima: {{ $pengeluaran->nama_penerima }}
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($pengeluaran->apbdes->anggaran, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($pencairanLalu, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">Total Permintaan</th>
                <th class="text-right font-bold">Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}</th>
            </tr>
            <tr>
                <th colspan="4" class="text-right">Sisa Dana Rekening</th>
                <th class="text-right">Rp {{ number_format($pengeluaran->apbdes->anggaran - $pencairanLalu - $pengeluaran->jumlah, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <?php
    if (!function_exists('terbilang')) {
        function terbilang($angka) {
            $angka = abs($angka);
            $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            $terbilang = "";
            if ($angka < 12) {
                $terbilang = " " . $baca[$angka];
            } else if ($angka < 20) {
                $terbilang = terbilang($angka - 10) . " Belas";
            } else if ($angka < 100) {
                $terbilang = terbilang($angka / 10) . " Puluh" . terbilang($angka % 10);
            } else if ($angka < 200) {
                $terbilang = " Seratus" . terbilang($angka - 100);
            } else if ($angka < 1000) {
                $terbilang = terbilang($angka / 100) . " Ratus" . terbilang($angka % 100);
            } else if ($angka < 2000) {
                $terbilang = " Seribu" . terbilang($angka - 1000);
            } else if ($angka < 1000000) {
                $terbilang = terbilang($angka / 1000) . " Ribu" . terbilang($angka % 1000);
            } else if ($angka < 1000000000) {
                $terbilang = terbilang($angka / 1000000) . " Juta" . terbilang($angka % 1000000);
            } else if ($angka < 1000000000000) {
                $terbilang = terbilang($angka / 1000000000) . " Milyar" . terbilang($angka % 1000000000);
            }
            return $terbilang;
        }
    }
    ?>

    <p>Terbilang: <i><strong>{{ trim(terbilang($pengeluaran->jumlah)) }} Rupiah</strong></i></p>

    <table class="table-noborder w-full mt-8 text-center" style="margin-top: 40px;">
        <tr>
            <td width="33%">
                Telah Diverifikasi,<br/>
                Sekretaris Desa<br/><br/><br/><br/><br/>
                <b>(......................................)</b>
            </td>
            <td width="33%">
                Setuju Dibayar,<br/>
                Kepala Desa<br/><br/><br/><br/><br/>
                <b>{{ $kepalaInfo['nama'] ?? '(......................................)' }}</b>
            </td>
            <td width="34%">
                Desa {{ $desaInfo['nama_desa'] ?? '...' }}, {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->translatedFormat('d F Y') }}<br/>
                Pelaksana Kegiatan<br/><br/><br/><br/><br/>
                <b>(......................................)</b>
            </td>
        </tr>
    </table>

</body>
</html>
