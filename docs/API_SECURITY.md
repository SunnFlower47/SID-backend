# API Security Documentation

## 🔒 Keamanan API Desa Cibatu

### Endpoint yang Dilindungi:
- `/api/v1/penduduk` - Data penduduk lengkap
- `/api/v1/statistics` - Statistik penduduk
- `/api/v1/statistics/penduduk` - Statistik detail penduduk
- `/api/v1/statistics/kk` - Statistik kartu keluarga
- `/api/v1/statistics/mutasi` - Statistik mutasi
- `/api/v1/struktur-desa` - Struktur organisasi (mengandung NIK)
- `/api/v1/perangkat-desa` - Data perangkat desa (mengandung NIK)
- `/api/v1/rt-rw` - Data RT/RW (mengandung NIK)
- `/api/v1/bumdes` - Data BUMDes (mengandung NIK)
- `/api/v1/search-penduduk` - Pencarian penduduk
- `/api/v1/check-nik/{nik}` - Validasi NIK
- `/api/v1/testimoni` - Data testimoni (mengandung email, telepon, RT/RW)
- `/api/v1/testimoni/stats` - Statistik testimoni
- `/api/v1/testimoni/categories` - Kategori testimoni
- `/api/v1/surat-pengajuan/nik/{nik}` - Data surat pengajuan
- `/api/v1/surat-pengajuan/search` - Pencarian surat

### Cara Menggunakan API:

#### 1. Header Authentication
```bash
curl -H "X-API-Key: desa-cibatu-2024-secure-key" \
     https://admin-dscibatu.sunnflower.site/api/v1/penduduk
```

#### 2. Query Parameter
```bash
curl "https://admin-dscibatu.sunnflower.site/api/v1/penduduk?api_key=desa-cibatu-2024-secure-key"
```

### Frontend Configuration:
```javascript
// Di file .env
REACT_APP_API_URL=https://admin-dscibatu.sunnflower.site/api/v1
REACT_APP_API_KEY=desa-cibatu-2024-secure-key
```

### Keamanan yang Diterapkan:
1. ✅ **API Key Authentication** - Semua endpoint sensitif memerlukan API key
2. ✅ **Rate Limiting** - Mencegah abuse dengan throttle
3. ✅ **CORS Protection** - Hanya domain yang diizinkan
4. ✅ **Input Validation** - Validasi semua input
5. ✅ **Error Handling** - Tidak mengekspos informasi sensitif

### Rekomendasi:
1. **Ganti API Key** secara berkala
2. **Monitor API Usage** untuk mendeteksi abuse
3. **Log semua akses** untuk audit
4. **Gunakan HTTPS** di production
5. **Backup data** secara berkala

### Endpoint yang Aman (Tidak Perlu API Key):
- `/api/v1/berita` - Berita publik
- `/api/v1/contact` - Informasi kontak
- `/api/v1/desa-info` - Informasi desa
- `/api/v1/fasilitas-desa` - Fasilitas desa
- `/api/v1/agenda-desa` - Agenda desa
- `/api/v1/agenda-categories` - Kategori agenda
