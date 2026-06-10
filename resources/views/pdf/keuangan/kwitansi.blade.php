<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; line-height: 1.5; color: #000; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .w-full { width: 100%; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-8 { margin-top: 2rem; }
        table { width: 100%; border-collapse: collapse; }
        .table-noborder td { padding: 8px 4px; vertical-align: top; }
        .header-line { border-bottom: 3px solid #000; margin-top: 10px; margin-bottom: 15px; }
        .text-right { text-align: right; }
        .border-box { border: 1px solid #000; padding: 20px; border-radius: 4px; }
        .amount-box { 
            display: inline-block; 
            border: 2px solid #000; 
            padding: 10px 20px; 
            font-size: 14pt; 
            font-weight: bold;
            background-color: #f8f8f8;
            margin-top: 10px;
        }
        .bg-gray { background-color: #f3f4f6; }
    </style>
</head>
<body>

    <div class="border-box">
        <table class="w-full">
            <tr>
                <td width="50%">
                    <div class="font-bold uppercase">PEMERINTAH DESA {{ $desaInfo['nama_desa'] ?? '...' }}</div>
                    <div class="text-xs">Kecamatan {{ $desaInfo['kecamatan'] ?? '...' }}, Kabupaten {{ $desaInfo['kabupaten'] ?? '...' }}</div>
                </td>
                <td width="50%" class="text-right">
                    Tahun Anggaran: <b>{{ $pengeluaran->apbdes->tahun }}</b><br/>
                    No. Bukti: <b>{{ $pengeluaran->no_bukti ?? '......../KWT/'.date('Y') }}</b>
                </td>
            </tr>
        </table>
        
        <div class="header-line"></div>

        <div class="text-center font-bold mb-4 mt-4">
            <h2 style="margin: 0; text-decoration: underline; letter-spacing: 2px;">K U I T A N S I</h2>
        </div>

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

        <table class="table-noborder w-full mb-4">
            <tr>
                <td width="25%">Telah Terima Dari</td>
                <td width="5%">:</td>
                <td width="70%" class="font-bold">Bendahara Desa {{ $desaInfo['nama_desa'] ?? '...' }}</td>
            </tr>
            <tr>
                <td>Uang Sebesar</td>
                <td>:</td>
                <td>
                    <div style="background-color: #f3f4f6; padding: 5px 10px; border: 1px solid #ccc; display: inline-block; width: 100%;">
                        <i>{{ trim(terbilang($pengeluaran->jumlah)) }} Rupiah</i>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Untuk Pembayaran</td>
                <td>:</td>
                <td>
                    <b>{{ $pengeluaran->nama_pengeluaran }}</b><br/>
                    Kode Rekening: {{ $pengeluaran->apbdes->kode_rekening }} - {{ $pengeluaran->apbdes->nama_rekening }}<br/>
                    Bidang: {{ $pengeluaran->apbdes->bidang }}
                    @if($pengeluaran->keterangan)
                        <br/>Keterangan: {{ $pengeluaran->keterangan }}
                    @endif
                </td>
            </tr>
        </table>

        <div class="amount-box">
            Rp {{ number_format($pengeluaran->jumlah, 0, ',', '.') }}
        </div>

        <table class="table-noborder w-full mt-4 text-center">
            <tr>
                <td width="33%">
                    <br/>
                    Lunas Dibayar,<br/>
                    Bendahara / Kaur Keuangan<br/><br/><br/><br/><br/>
                    <b>(......................................)</b>
                </td>
                <td width="33%">
                    <br/>
                    Mengetahui,<br/>
                    Kepala Desa<br/><br/><br/><br/><br/>
                    <b>{{ $kepalaInfo['nama'] ?? '(......................................)' }}</b>
                </td>
                <td width="34%">
                    Desa {{ $desaInfo['nama_desa'] ?? '...' }}, {{ \Carbon\Carbon::parse($pengeluaran->tanggal_pengeluaran)->translatedFormat('d F Y') }}<br/>
                    Penerima,<br/><br/><br/><br/><br/>
                    <b>{{ $pengeluaran->nama_penerima ?: '(......................................)' }}</b>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
