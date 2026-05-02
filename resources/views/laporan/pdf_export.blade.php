<!DOCTYPE html>
<html>
<head>
    <title>Laporan Sistem Desa Cibatu</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; }
        .header h2 { margin: 5px 0; font-size: 12pt; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8pt; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SISTEM DESA CIBATU</h1>
        <h2>Laporan {{ ucfirst($type) }}</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if($type === 'penduduk')
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Tgl Lahir</th>
                    <th>Alamat</th>
                @elseif($type === 'kk')
                    <th>No. KK</th>
                    <th>Kepala Keluarga</th>
                    <th>Alamat</th>
                    <th>RT/RW</th>
                @elseif($type === 'mutasi')
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Tgl Mutasi</th>
                    <th>Keterangan</th>
                @elseif($type === 'berita')
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Penulis</th>
                    <th>Tgl Posting</th>
                @elseif($type === 'surat')
                    <th>No. Surat</th>
                    <th>Pemohon</th>
                    <th>Jenis</th>
                    <th>Keperluan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @if($type === 'penduduk')
                        <td>{{ $row->nik }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->jenis_kelamin == 'LAKI-LAKI' ? 'L' : 'P' }}</td>
                        <td>{{ $row->tanggal_lahir ? $row->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        <td>{{ Str::limit($row->alamat, 30) }}</td>
                    @elseif($type === 'kk')
                        <td>{{ $row->nkk }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->alamat }}</td>
                        <td>{{ $row->rt_label }}/{{ $row->rw_label }}</td>
                    @elseif($type === 'mutasi')
                        <td>{{ $row->penduduk->nama ?? 'Deleted' }}</td>
                        <td>{{ ucfirst($row->jenis_mutasi) }}</td>
                        <td>{{ $row->tanggal_mutasi ? $row->tanggal_mutasi->format('d/m/Y') : '-' }}</td>
                        <td>{{ $row->alasan }}</td>
                    @elseif($type === 'berita')
                        <td>{{ $row->judul }}</td>
                        <td>{{ $row->kategori }}</td>
                        <td>{{ $row->user->name ?? '-' }}</td>
                        <td>{{ $row->created_at->format('d/m/Y') }}</td>
                    @elseif($type === 'surat')
                        <td>{{ $row->nomor_surat }}</td>
                        <td>{{ $row->nama_pengaju }}</td>
                        <td>{{ $row->jenis_surat }}</td>
                        <td>{{ $row->keperluan }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ now()->format('d M Y H:i:s') }}
    </div>
</body>
</html>

