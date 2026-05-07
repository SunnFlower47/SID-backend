<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tanggapan Pengaduan - Pemerintah Desa Cibatu</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #064e3b; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #064e3b; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; }
        .content { margin-bottom: 30px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 50px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px; }
        .badge-status { background-color: #064e3b; color: #ffffff; }
        .original-message { background: #f3f4f6; padding: 15px 20px; border-left: 4px solid #064e3b; border-radius: 0 12px 12px 0; font-style: italic; color: #6b7280; font-size: 14px; margin-bottom: 25px; }
        .reply-content { font-size: 16px; color: #1f2937; padding: 20px; background-color: #f0fdfa; border: 1px solid #ccfbf1; border-radius: 12px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #e5e7eb; font-size: 12px; color: #9ca3af; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SID CIBATU</h1>
            <p style="margin: 5px 0 0; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 2px;">Tanggapan Atas Pengaduan</p>
        </div>
        
        <div class="content">
            <p>Halo, <strong>{{ $pengaduan->nama_pelapor }}</strong>,</p>
            <p>Terima kasih telah menyampaikan aspirasi Anda. Berikut adalah tanggapan resmi kami atas laporan Anda mengenai <strong>"{{ $pengaduan->judul }}"</strong>:</p>
            
            <div class="reply-content">
                <p style="margin-top: 0; font-size: 12px; font-weight: 900; color: #064e3b; text-transform: uppercase;">Tanggapan Admin:</p>
                {!! nl2br(e($adminReply)) !!}
            </div>

            <div style="margin: 20px 0;">
                <p style="margin: 0; font-size: 12px; color: #6b7280; font-weight: bold; text-transform: uppercase;">Status Terbaru:</p>
                <span class="badge badge-status">{{ strtoupper($pengaduan->status) }}</span>
            </div>
            
            <h4 style="margin-top: 30px; margin-bottom: 10px; color: #4b5563; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Rincian Laporan Anda:</h4>
            <div class="original-message">
                "{!! nl2br(e($pengaduan->deskripsi)) !!}"
            </div>
        </div>

        <div class="footer">
            <p style="margin-bottom: 5px;">Email ini dikirim secara otomatis oleh Sistem Informasi Desa Cibatu.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} Pemerintah Desa Cibatu. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
