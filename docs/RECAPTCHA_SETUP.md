# Konfigurasi reCAPTCHA untuk Login

## Status Saat Ini
✅ **Development Mode**: reCAPTCHA disabled di local/testing  
✅ **Production Ready**: Siap untuk deployment dengan API key  
✅ **Environment Aware**: Otomatis enable/disable berdasarkan environment  

## Setup untuk Production (Saat Deployment)

### 1. Daftar di Google reCAPTCHA
1. Kunjungi: https://www.google.com/recaptcha/admin
2. Login dengan akun Google
3. Klik "Create" untuk membuat site baru
4. Pilih reCAPTCHA v2 "I'm not a robot" Checkbox
5. Masukkan domain production (contoh: sistem-desa-cibatu.com)
6. Dapatkan Site Key dan Secret Key

### 2. Konfigurasi Environment Production
Tambahkan ke file `.env` di server production:

```env
# reCAPTCHA Configuration (Production)
RECAPTCHA_SITE_KEY=your_recaptcha_site_key_here
RECAPTCHA_SECRET_KEY=your_recaptcha_secret_key_here
```

### 3. Environment Detection
- **Local/Testing**: reCAPTCHA otomatis disabled
- **Production**: reCAPTCHA otomatis enabled
- **No Action Needed**: Sistem otomatis detect environment

### 3. File yang Sudah Dimodifikasi
- `resources/views/auth/login.blade.php` - Form login dengan reCAPTCHA
- `config/services.php` - Konfigurasi reCAPTCHA
- `app/Http/Middleware/RecaptchaMiddleware.php` - Middleware validasi
- `bootstrap/app.php` - Registrasi middleware
- `routes/web.php` - Aplikasi middleware ke route login

### 4. Testing
1. Pastikan environment variables sudah di-set
2. Clear cache: `php artisan config:clear`
3. Test login dengan reCAPTCHA

### 5. Catatan Penting
- reCAPTCHA hanya aktif di production/testing
- Di development, middleware akan skip validasi jika environment = 'testing'
- Pastikan domain sudah terdaftar di Google reCAPTCHA
- Site Key untuk frontend, Secret Key untuk backend

## Troubleshooting
- Jika reCAPTCHA tidak muncul: cek Site Key di .env
- Jika validasi gagal: cek Secret Key di .env
- Jika error 403: pastikan domain terdaftar di Google reCAPTCHA
