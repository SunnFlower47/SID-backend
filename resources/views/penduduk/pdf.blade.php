<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penduduk - {{ $penduduk->nama }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 10px 15px;
            font-weight: bold;
            color: #374151;
            border-left: 4px solid #3b82f6;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            min-width: 120px;
        }
        .info-value {
            color: #6b7280;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-aktif {
            background-color: #dcfce7;
            color: #166534;
        }
        .status-tidak-aktif {
            background-color: #fef2f2;
            color: #dc2626;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Data Penduduk</h1>
            <p>Desa Cibatu - {{ date('d F Y') }}</p>
        </div>

        <div class="section">
            <div class="section-title">Informasi Pribadi</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">NIK:</span>
                    <span class="info-value">{{ $penduduk->nik ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama:</span>
                    <span class="info-value">{{ $penduduk->nama ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Jenis Kelamin:</span>
                    <span class="info-value">{{ $penduduk->jenis_kelamin ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tempat Lahir:</span>
                    <span class="info-value">{{ $penduduk->tempat_lahir ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tanggal Lahir:</span>
                    <span class="info-value">{{ $penduduk->tanggal_lahir ? \Carbon\Carbon::parse($penduduk->tanggal_lahir)->format('d F Y') : '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Agama:</span>
                    <span class="info-value">{{ $penduduk->agama ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status Perkawinan:</span>
                    <span class="info-value">{{ $penduduk->status_perkawinan ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Pekerjaan:</span>
                    <span class="info-value">{{ $penduduk->pekerjaan ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Informasi Keluarga</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">NKK:</span>
                    <span class="info-value">{{ $penduduk->kartuKeluarga->nkk ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kedudukan Keluarga:</span>
                    <span class="info-value">{{ $penduduk->kedudukan_keluarga ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Ayah:</span>
                    <span class="info-value">{{ $penduduk->nama_ayah ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Nama Ibu:</span>
                    <span class="info-value">{{ $penduduk->nama_ibu ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Alamat</div>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Alamat:</span>
                    <span class="info-value">{{ $penduduk->alamat ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">RT:</span>
                    <span class="info-value">{{ $penduduk->rt ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">RW:</span>
                    <span class="info-value">{{ $penduduk->rw ?? '-' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dusun:</span>
                    <span class="info-value">{{ $penduduk->dusun ?? '-' }}</span>
                </div>
            </div>
        </div>

        @if($penduduk->keterangan)
        <div class="section">
            <div class="section-title">Keterangan</div>
            <p style="color: #6b7280; background-color: #fef3c7; padding: 15px; border-radius: 6px; border-left: 4px solid #f59e0b;">
                {{ $penduduk->keterangan }}
            </p>
        </div>
        @endif

        <div class="footer">
            <p>Dokumen ini dibuat secara otomatis oleh Sistem Informasi Desa Cibatu</p>
            <p>Tanggal cetak: {{ date('d F Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>

