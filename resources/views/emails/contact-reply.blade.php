<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balasan dari Pemerintah Desa</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f9f9f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { text-align: center; border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #10b981; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: 1px; text-transform: uppercase; }
        .content { margin-bottom: 30px; }
        .original-message { background: #f3f4f6; padding: 15px 20px; border-left: 4px solid #10b981; border-radius: 0 12px 12px 0; font-style: italic; color: #6b7280; font-size: 14px; margin-bottom: 25px; }
        .reply-content { font-size: 16px; color: #1f2937; padding: 20px; background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 12px; margin: 20px 0; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px dashed #e5e7eb; font-size: 12px; color: #9ca3af; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pemerintah Desa</h1>
            <p style="margin: 5px 0 0; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: bold; letter-spacing: 2px;">Tanggapan Resmi</p>
        </div>
        
        <div class="content">
            <p>Halo, <strong>{{ $contactMessage->nama }}</strong>,</p>
            <p>Terima kasih telah menghubungi kami. Berikut adalah tanggapan atas pesan Anda mengenai <strong>"{{ $contactMessage->subjek }}"</strong>:</p>
            
            <div class="reply-content">
                {!! nl2br(e($adminReply)) !!}
            </div>
            
            <h4 style="margin-top: 30px; margin-bottom: 10px; color: #4b5563; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">Mengutip Pesan Asli Anda:</h4>
            <div class="original-message">
                "{!! nl2br(e($contactMessage->pesan)) !!}"
            </div>
        </div>

        <div class="footer">
            <p style="margin-bottom: 5px;">Email ini dikirim secara otomatis oleh Sistem Informasi Desa.</p>
            <p style="margin: 0;">&copy; {{ date('Y') }} Pemerintah Desa. Hak Cipta Dilindungi.</p>
        </div>
    </div>
</body>
</html>
