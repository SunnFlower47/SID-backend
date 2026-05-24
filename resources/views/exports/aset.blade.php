<table>
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 14px;">LAPORAN ASET DESA SEMESTER {{ $semester == 1 ? 'I (Satu)' : 'II (Dua)' }}</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 14px;">RINCIAN PERKELOMPOK BARANG</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 14px;">TAHUN ANGGARAN {{ $tahun }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th rowspan="3" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">KODE</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">NAMA BARANG</th>
            <th rowspan="3" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">SATUAN</th>
            <th colspan="2" style="text-align: center; font-weight: bold; border: 1px solid black;">SALDO PER 1 JANUARI {{ $tahun }}</th>
            <th colspan="4" style="text-align: center; font-weight: bold; border: 1px solid black;">MUTASI</th>
            <th colspan="2" style="text-align: center; font-weight: bold; border: 1px solid black;">SALDO PER 31 Desember {{ $tahun }}</th>
        </tr>
        <tr>
            <th rowspan="2" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">KWANTITAS</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">NILAI (Rp)</th>
            <th colspan="2" style="text-align: center; font-weight: bold; border: 1px solid black;">BERTAMBAH</th>
            <th colspan="2" style="text-align: center; font-weight: bold; border: 1px solid black;">BERKURANG</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">KWANTITAS</th>
            <th rowspan="2" style="vertical-align: middle; text-align: center; font-weight: bold; border: 1px solid black;">NILAI (Rp)</th>
        </tr>
        <tr>
            <th style="text-align: center; font-weight: bold; border: 1px solid black;">KWANTITAS</th>
            <th style="text-align: center; font-weight: bold; border: 1px solid black;">NILAI (Rp)</th>
            <th style="text-align: center; font-weight: bold; border: 1px solid black;">KWANTITAS</th>
            <th style="text-align: center; font-weight: bold; border: 1px solid black;">NILAI (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($groupedAset as $kategoriNama => $inventarisList)
            <tr>
                <th style="border: 1px solid black;"></th>
                <th colspan="10" style="font-weight: bold; border: 1px solid black; text-align: left;">{{ mb_strtoupper($kategoriNama) }}</th>
            </tr>
            @php 
                $sumSaldoAwalNilai = 0;
                $sumSaldoAkhirNilai = 0;
            @endphp
            
            @foreach($inventarisList as $aset)
                @php
                    // Hitung Saldo Awal (Mutasi sebelum tahun yang dipilih)
                    $awalKwantitas = 0;
                    $awalNilai = 0;
                    
                    // Hitung Mutasi di tahun berjalan
                    $tambahKwantitas = 0;
                    $tambahNilai = 0;
                    
                    $kurangKwantitas = 0;
                    $kurangNilai = 0;

                    foreach($aset->mutasis as $mutasi) {
                        if($mutasi->tahun < $tahun) {
                            if($mutasi->jenis == 'tambah') {
                                $awalKwantitas += $mutasi->kwantitas;
                                $awalNilai += $mutasi->nilai;
                            } else {
                                $awalKwantitas -= $mutasi->kwantitas;
                                $awalNilai -= $mutasi->nilai;
                            }
                        } elseif($mutasi->tahun == $tahun) {
                            if($mutasi->jenis == 'tambah') {
                                $tambahKwantitas += $mutasi->kwantitas;
                                $tambahNilai += $mutasi->nilai;
                            } else {
                                $kurangKwantitas += $mutasi->kwantitas;
                                $kurangNilai += $mutasi->nilai;
                            }
                        }
                    }

                    // Saldo Akhir
                    $akhirKwantitas = $awalKwantitas + $tambahKwantitas - $kurangKwantitas;
                    $akhirNilai = $awalNilai + $tambahNilai - $kurangNilai;

                    // Akumulasi sum untuk grup
                    $sumSaldoAwalNilai += $awalNilai;
                    $sumSaldoAkhirNilai += $akhirNilai;
                @endphp
                <tr>
                    <td style="border: 1px solid black;">{{ $aset->barang?->kode_barang ?? '-' }}</td>
                    <td style="border: 1px solid black;">{{ $aset->nama_display }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $aset->satuan ?? '-' }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $awalKwantitas > 0 ? $awalKwantitas : '-' }}</td>
                    <td style="border: 1px solid black;" data-format="#,##0.00_-">Rp {{ number_format($awalNilai, 2, ',', '.') }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $tambahKwantitas > 0 ? $tambahKwantitas : '-' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $tambahNilai > 0 ? 'Rp ' . number_format($tambahNilai, 2, ',', '.') : '-' }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $kurangKwantitas > 0 ? $kurangKwantitas : '-' }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $kurangNilai > 0 ? 'Rp ' . number_format($kurangNilai, 2, ',', '.') : '-' }}</td>
                    <td style="text-align: center; border: 1px solid black;">{{ $akhirKwantitas > 0 ? $akhirKwantitas : '-' }}</td>
                    <td style="border: 1px solid black;" data-format="#,##0.00_-">Rp {{ number_format($akhirNilai, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align: center; font-weight: bold; border: 1px solid black;">TOTAL</td>
                <td style="font-weight: bold; border: 1px solid black;">Rp {{ number_format($sumSaldoAwalNilai, 2, ',', '.') }}</td>
                <td colspan="5" style="border: 1px solid black;"></td>
                <td style="font-weight: bold; border: 1px solid black;">Rp {{ number_format($sumSaldoAkhirNilai, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
