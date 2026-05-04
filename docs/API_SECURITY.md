# 🔒 API Security Documentation (V2 - High Security)

Dokumen ini menjelaskan lapisan keamanan tingkat tinggi yang diterapkan pada API **Sistem Desa Cibatu** untuk melindungi data sensitif warga.

---

## 🛡️ 1. Mekanisme Keamanan Utama

Sistem menggunakan **Zero Trust Architecture** dengan beberapa lapis pertahanan:

### A. Digital Signature (HMAC-SHA256)
Setiap request ke endpoint **Private API** WAJIB menyertakan tanda tangan digital untuk memastikan integritas data.
- **Header**: `X-Signature`
- **Algoritma**: HMAC-SHA256
- **Secret**: Diambil dari `API_KEY` di file `.env`.
- **Payload**: `timestamp + method + path`

### B. Request Expiration (Anti-Replay)
Mencegah hacker mencuri request dan menggunakannya kembali di lain waktu.
- **Header**: `X-Timestamp` (Unix Timestamp)
- **Toleransi**: 300 detik (5 Menit). Request lebih dari 5 menit akan ditolak otomatis.

### C. Origin & User-Agent Whitelisting
- Hanya domain resmi yang terdaftar di `PrivateApiMiddleware` yang diizinkan.
- Request non-browser (curl/bot polosan) akan diblokir kecuali menyertakan header yang valid.

---

## 🔑 2. Cara Request (Untuk Developer/Frontend)

Untuk mengakses endpoint seperti `/api/v1/penduduk`, frontend harus mengirimkan header berikut:

```http
X-API-Key: [API_KEY_ANDA]
X-Timestamp: 1714845000
X-Signature: [HASIL_HMAC_SHA256]
User-Agent: Mozilla/5.0...
```

### Contoh Generator Signature (JavaScript):
```javascript
const timestamp = Math.floor(Date.now() / 1000);
const method = 'GET';
const path = 'v1/statistics';
const secret = 'your-api-key-secret';

const message = timestamp + method + path;
const signature = crypto.createHmac('sha256', secret).update(message).digest('hex');
```

---

## 🛡️ 3. Content Security Policy (CSP) & Nonce

Untuk mencegah serangan **XSS (Cross-Site Scripting)**, sistem menggunakan `CspNonceMiddleware`.
- **Nonce**: String unik yang di-generate setiap kali halaman direfresh.
- **Cara Pakai**: Semua inline script di Blade/React harus menyertakan atribut `nonce`.
  ```html
  <script nonce="{{ $csp_nonce }}"> ... </script>
  ```
- **Whitelist**: Google ReCAPTCHA, Google Fonts, dan CDN terpercaya sudah masuk daftar izin.

---

## 🚦 4. Rate Limiting (Throttling)

Setiap endpoint memiliki batas akses untuk mencegah serangan Brute Force:
- **General Data**: 100 request / menit.
- **Search NIK**: 10 request / menit.
- **Form Submit**: 5 request / menit.

---

## 📑 5. Daftar Endpoint Berdasarkan Tingkat Keamanan

| Endpoint | Keamanan | Middleware |
|----------|----------|------------|
| `/api/v1/berita` | Low | `throttle` |
| `/api/v1/statistics` | High | `private.api`, `signature` |
| `/api/v1/search-penduduk`| Critical | `private.api`, `captcha:v3` |
| `/api/v1/surat-pengajuan`| Critical | `private.api`, `captcha` |

---

## 🛡️ 6. CSRF Protection for Web-Desa

Meskipun menggunakan prefix `/v1`, sistem tetap menerapkan perlindungan CSRF untuk semua request yang merubah state (POST/PATCH/DELETE).

### Cara Kerja:
1. **Fetch Token**: Frontend (web-desa) harus memanggil endpoint `GET /api/v1/csrf-token` saat inisialisasi aplikasi.
2. **Storage**: Simpan token tersebut di memory (state) atau session.
3. **Usage**: Sertakan token tersebut di header `X-CSRF-TOKEN` untuk setiap request POST.

```javascript
// Contoh Fetch CSRF Token
const response = await axios.get('/api/v1/csrf-token');
const csrfToken = response.data.csrf_token;

// Contoh Kirim Request dengan CSRF
await axios.post('/api/v1/surat-pengajuan', data, {
    headers: { 'X-CSRF-TOKEN': csrfToken }
});
```

---
> [!IMPORTANT]
> **Peringatan**: Jangan pernah memberikan `API_KEY` kepada pihak ketiga. Gunakan `Proxy API` jika ingin mengakses data dari domain yang berbeda.
