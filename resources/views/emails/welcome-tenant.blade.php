<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Akses Admin Panel Desa {{ $tenant->name }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .header {
            background-color: #4f46e5;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 30px;
            color: #374151;
            line-height: 1.6;
        }
        .content p {
            margin: 0 0 15px;
        }
        .card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }
        .card table {
            width: 100%;
            border-collapse: collapse;
        }
        .card td {
            padding: 6px 0;
            vertical-align: top;
        }
        .card td.label {
            font-weight: 600;
            width: 140px;
            color: #4b5563;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            background-color: #4f46e5;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }
        .btn:hover {
            background-color: #4338ca;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang di Sistem Desa Digital</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $tenant->operator_name }}</strong>,</p>
            <p>Pendaftaran tenant baru untuk <strong>{{ $tenant->name }}</strong> telah berhasil diproses oleh Diskominfo. Database dan domain Anda kini sudah siap digunakan.</p>
            
            <p>Berikut adalah rincian informasi akun admin utama untuk mengakses Admin Panel desa Anda:</p>

            <div class="card">
                <table>
                    <tr>
                        <td class="label">Nama Desa</td>
                        <td>: {{ $tenant->name }}</td>
                    </tr>

                    <tr>
                        <td class="label">Link Login Admin</td>
                        <td>: <a href="{{ $loginUrl }}">{{ $loginUrl }}</a></td>
                    </tr>
                    <tr>
                        <td class="label">Email Login</td>
                        <td>: <strong>{{ $tenant->operator_email }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Password Sementara</td>
                        <td>: <code style="font-family: monospace; background: #e5e7eb; padding: 2px 6px; border-radius: 3px; font-size: 14px;">{{ $tenant->operator_password }}</code></td>
                    </tr>
                </table>
            </div>

            <p>Silakan klik tombol di bawah ini untuk mengakses halaman login Admin Panel desa Anda:</p>
            
            <div class="button-container">
                <a href="{{ $loginUrl }}" class="btn">Masuk Admin Panel</a>
            </div>

            <p style="font-size: 13px; color: #ef4444; font-weight: 600;">PENTING: Demi alasan keamanan, segera ganti password sementara Anda setelah berhasil login untuk pertama kali melalui menu Pengaturan Profil.</p>
            
            <p>Salam hangat,<br>Tim Diskominfo Kabupaten Purwakarta</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Sistem Layanan Desa Terpadu SaaS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
