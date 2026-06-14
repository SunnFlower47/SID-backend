<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .header { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
        .status-badge { 
            display: inline-block; padding: 5px 10px; border-radius: 4px; font-weight: bold; color: white; 
        }
        .status-diproses { background-color: #f59e0b; }
        .status-selesai { background-color: #10b981; }
        .status-ditolak { background-color: #ef4444; }
        .status-pending { background-color: #6b7280; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .detail-table th { text-align: left; padding: 8px; border-bottom: 1px solid #eee; color: #555; width: 40%; }
        .detail-table td { padding: 8px; border-bottom: 1px solid #eee; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pemberitahuan Status Surat</h2>
            <p>Pemerintah Desa Cibatu</p>
        </div>

        <p>Halo, <strong>{{ $surat->nama_pengaju }}</strong></p>
        <p>Pengajuan surat Anda dengan nomor pengajuan <strong>{{ $surat->nomor_pengajuan }}</strong> saat ini telah mengalami perubahan status.</p>

        @php
            $statusClass = 'status-pending';
            if ($surat->status === 'diproses') $statusClass = 'status-diproses';
            if ($surat->status === 'selesai') $statusClass = 'status-selesai';
            if ($surat->status === 'ditolak') $statusClass = 'status-ditolak';
            
            $statusList = [
                'pending' => 'Menunggu Persetujuan',
                'diproses' => 'Diproses',
                'ditolak' => 'Ditolak',
                'selesai' => 'Selesai',
            ];
            $statusLabel = $statusList[$surat->status] ?? $surat->status;
        @endphp

        <div style="text-align: center; margin: 20px 0;">
            Status Saat Ini:<br>
            <span class="status-badge {{ $statusClass }}">{{ strtoupper($statusLabel) }}</span>
        </div>

        <table class="detail-table">
            <tr>
                <th>Nomor Pengajuan</th>
                <td>{{ $surat->nomor_pengajuan }}</td>
            </tr>
            @if($surat->nomor_surat)
            <tr>
                <th>Nomor Surat Resmi</th>
                <td>{{ $surat->nomor_surat }}</td>
            </tr>
            @endif
            <tr>
                <th>Jenis Surat</th>
                <td>{{ $surat->suratType ? $surat->suratType->nama : $surat->jenis_surat }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengajuan</th>
                <td>{{ $surat->tanggal_surat->format('d/m/Y') }}</td>
            </tr>
            @if($surat->keterangan_admin)
            <tr>
                <th>Pesan/Keterangan Admin</th>
                <td style="color: #ef4444;">{{ $surat->keterangan_admin }}</td>
            </tr>
            @endif
        </table>

        @if($surat->status === 'selesai')
            <p><strong>Selamat!</strong> Surat Anda telah selesai diproses.</p>
            @if($surat->file_balasan_admin)
                <p>Kami telah melampirkan file dokumen surat Anda pada email ini (lihat lampiran).</p>
            @else
                <p>Silakan ambil surat fisik Anda di Kantor Kepala Desa Cibatu pada jam kerja.</p>
            @endif
        @elseif($surat->status === 'ditolak')
            <p>Mohon maaf, pengajuan surat Anda ditolak. Silakan baca pesan dari admin di atas atau ajukan ulang dengan data yang benar.</p>
        @elseif($surat->status === 'diproses')
            <p>Pengajuan Anda sedang kami proses dan tinjau. Kami akan memberitahukan Anda kembali jika surat sudah selesai.</p>
        @endif

        <p style="margin-top: 30px;">
            Hormat kami,<br>
            <strong>Pelayanan Desa Cibatu</strong>
        </p>

        <div class="footer">
            Email ini dibuat secara otomatis oleh sistem. Mohon jangan membalas ke alamat email ini.<br>
            Untuk pertanyaan lebih lanjut, silakan hubungi kontak desa melalui website resmi kami.
        </div>
    </div>
</body>
</html>
