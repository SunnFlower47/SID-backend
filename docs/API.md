# 📊 API DOCUMENTATION - SISTEM DESA CIBATU

## 🔗 Base URL
```
Production: https://admin-dscibatu.sunnflower.site/api/v1
Development: http://localhost:8000/api/v1
```

## 🔐 Authentication

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "admin@desa-cibatu.id",
  "password": "password"
}
```

**Response:**
```json
{
  "success": true,
  "token": "1|abc123def456...",
  "user": {
    "id": 1,
    "name": "Admin Desa",
    "email": "admin@desa-cibatu.id",
    "roles": ["admin-desa"]
  }
}
```

### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

---

## 📋 Mutasi API V3.0

### Create Mutasi
```http
POST /api/v1/mutasi/data
Authorization: Bearer {token}
Content-Type: application/json

{
  "jenis_mutasi": "pisah_kk",
  "kategori_mutasi": "luar_kota",
  "penduduk_id": 123,
  "nkk_tujuan": "1234567890123456",
  "alamat": "Jl. Contoh No. 123, Kota Tujuan",
  "kedudukan_keluarga_pisah": "Kepala Keluarga",
  "status_perkawinan_pisah": "Kawin",
  "tanggal_mutasi": "2025-10-18",
  "alasan": "Menikah"
}
```

### Get Mutasi Detail
```http
GET /api/v1/mutasi/data/{id}
Authorization: Bearer {token}
```

**Response untuk Pisah KK luar desa:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "jenis_mutasi": "pisah_kk",
    "kategori_mutasi": "luar_kota",
    "tanggal_mutasi": "2025-10-18",
    "asal_tujuan": "Pisah dari KK 1234567890123456 - Jl. Contoh No. 123",
    "alasan": "Menikah",
    "detail_tambahan": {
      "tracking": {
        "nkk_tujuan": "1234567890123456",
        "alamat_tujuan": "Jl. Contoh No. 123, Kota Tujuan",
        "kategori_pindah": "luar_kota",
        "tanggal_pindah": "2025-10-18"
      }
    },
    "penduduk": {
      "id": 123,
      "nama": "John Doe",
      "nik": "1234567890123456",
      "jenis_kelamin": "LAKI-LAKI"
    }
  }
}
```

### Cancel/Undo Mutasi
```http
DELETE /api/v1/mutasi/cancel/{id}
Authorization: Bearer {token}
```

### Undo Soft Delete Mutasi
```http
POST /api/v1/mutasi/undo/{id}
Authorization: Bearer {token}
```

---

## 📊 Statistics API

### Get Statistics
```http
GET /api/statistics
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_penduduk": 3569,
    "total_kk": 1263,
    "total_rt": 10,
    "total_mutasi": 192,
    "total_berita": 7,
    "total_pengajuan": 45,
    "laki_laki": 1791,
    "perempuan": 1778,
    "pendidikan": {
      "SD": 1200,
      "SMP": 800,
      "SMA": 600,
      "D3": 200,
      "S1": 150
    },
    "pekerjaan": {
      "Petani": 800,
      "Wiraswasta": 600,
      "PNS": 200,
      "Buruh": 400,
      "Lainnya": 300
    }
  }
}
```

---

## 👥 Penduduk API

### Get Penduduk List
```http
GET /api/penduduk?page=1&search=nama&rt=01&rw=01&jenis_kelamin=L
```

**Query Parameters:**
- `page` - Page number (default: 1)
- `search` - Search by nama, nik, nkk, alamat
- `rt` - Filter by RT
- `rw` - Filter by RW
- `dusun` - Filter by Dusun
- `jenis_kelamin` - Filter by gender (L/P)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nama": "John Doe",
        "nik": "3201234567890123",
        "nkk": "3201234567890124",
        "jenis_kelamin": "L",
        "tanggal_lahir": "1990-01-01",
        "alamat": "Jl. Contoh No. 1",
        "rt": "01",
        "rw": "01",
        "dusun": "Dusun 1",
        "kedudukan_keluarga": "Kepala Keluarga",
        "status_perkawinan": "Kawin",
        "agama": "Islam",
        "pendidikan": "SMA",
        "pekerjaan": "Wiraswasta"
      }
    ],
    "total": 3569,
    "per_page": 50
  }
}
```

### Create Penduduk
```http
POST /api/penduduk
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "John Doe",
  "nik": "3201234567890123",
  "nkk": "3201234567890124",
  "jenis_kelamin": "L",
  "tanggal_lahir": "1990-01-01",
  "tempat_lahir": "Purwakarta",
  "alamat": "Jl. Contoh No. 1",
  "rt": "01",
  "rw": "01",
  "dusun": "Dusun 1",
  "kedudukan_keluarga": "Kepala Keluarga",
  "status_perkawinan": "Kawin",
  "agama": "Islam",
  "pendidikan": "SMA",
  "pekerjaan": "Wiraswasta"
}
```

### Update Penduduk
```http
PUT /api/penduduk/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "John Doe Updated",
  "alamat": "Jl. Contoh No. 2"
}
```

### Delete Penduduk
```http
DELETE /api/penduduk/{id}
Authorization: Bearer {token}
```

---

## 🏘️ Kartu Keluarga API

### Get Kartu Keluarga List
```http
GET /api/kartu-keluarga?page=1&search=nkk&status=aktif
```

**Query Parameters:**
- `page` - Page number
- `search` - Search by NKK or nama kepala keluarga
- `status` - Filter by status (all, aktif, bermasalah, kosong)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "nkk": "3201234567890124",
        "nama_kepala_keluarga": "John Doe",
        "jumlah_anggota": 4,
        "anggota_aktif": 3,
        "anggota_meninggal": 1,
        "anggota_pindah": 0,
        "anggota_mutasi": 1,
        "tanggal_dibuat": "2024-01-01T00:00:00.000000Z",
        "tanggal_update": "2024-01-15T10:30:00.000000Z"
      }
    ],
    "total": 1263,
    "per_page": 20
  }
}
```

### Get Kartu Keluarga Detail
```http
GET /api/kartu-keluarga/{nkk}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "nkk": "3201234567890124",
    "nama_kepala_keluarga": "John Doe",
    "anggota": [
      {
        "id": 1,
        "nama": "John Doe",
        "nik": "3201234567890123",
        "kedudukan_keluarga": "Kepala Keluarga",
        "jenis_kelamin": "L",
        "tanggal_lahir": "1990-01-01"
      }
    ]
  }
}
```

### Update Kepala Keluarga
```http
POST /api/kartu-keluarga/{nkk}/update-kepala-keluarga
Authorization: Bearer {token}
Content-Type: application/json

{
  "nik_baru": "3201234567890125"
}
```

---

## 📄 Surat API

### Get Surat Types
```http
GET /api/v1/surat-types
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "domisili",
      "name": "Surat Domisili",
      "description": "Surat keterangan domisili warga",
      "persyaratan": "- KTP\n- KK",
      "has_template": true,
      "template_code": "SKD",
      "icon": "fas fa-home",
      "color": "blue",
      "category": "Template",
      "form_json": [
        {
          "name": "alamat_domisili",
          "type": "textarea",
          "label": "Alamat Domisili Sekarang",
          "required": true,
          "placeholder": "Masukkan alamat lengkap domisili saat ini"
        }
      ]
    }
  ]
}
```

### Check NIK / Search Penduduk (Verifikasi NIK + Tanggal Lahir)
```http
POST /api/v1/search-penduduk
Content-Type: application/json

{
  "nik": "3201234567890123",
  "tanggal_lahir": "1990-01-01"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Verifikasi berhasil",
  "data": {
    "id": 1,
    "nama": "Joh****************",
    "alamat": "Jl. C************, RT 0/0"
  }
}
```

### Submit Surat Pengajuan
```http
POST /api/v1/surat-pengajuan
Content-Type: multipart/form-data

penduduk_id: 1
nik: "3201234567890123"
tanggal_lahir: "1990-01-01"
surat_type: "domisili"
keperluan: "Pembuatan Rekening Bank"
tujuan: "Bank BNI"
tanggal_surat: "2024-01-15"
email_pengaju: "user@example.com"
data_tambahan: {"alamat_domisili": "Jl. Cibatu Baru No. 10"}
file_lampiran: (binary file PDF)
```

**Response:**
```json
{
  "success": true,
  "message": "Pengajuan surat berhasil dikirim",
  "nomor_resi": "REQ-240115-ABCD",
  "data": {
    "id": 1,
    "nomor_surat": null,
    "nomor_resi": "REQ-240115-ABCD",
    "jenis_surat_nama": "Surat Domisili",
    "status": "pending",
    "keperluan": "Pembuatan Rekening Bank",
    "keterangan_admin": null,
    "tanggal_pengajuan": "2024-01-15T10:30:00.000000Z",
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z"
  }
}
```

### Get Surat Status
```http
GET /api/v1/surat-status?nomor=REQ-240115-ABCD&nik=3201234567890123
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nomor_surat": "140/001/SKD/2024",
      "nomor_resi": "REQ-240115-ABCD",
      "jenis_surat_nama": "Surat Domisili",
      "status": "selesai",
      "keperluan": "Pembuatan Rekening Bank",
      "keterangan_admin": "Silakan ambil di kantor desa",
      "tanggal_pengajuan": "2024-01-15T10:30:00.000000Z",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-16T09:00:00.000000Z"
    }
  ]
}
```

### Get Surat History
```http
POST /api/v1/surat-history
Content-Type: application/json

{
  "nik": "3201234567890123",
  "tanggal_lahir": "1990-01-01"
}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nomor_surat": "140/001/SKD/2024",
      "nomor_resi": "REQ-240115-ABCD",
      "jenis_surat_nama": "Surat Domisili",
      "status": "selesai",
      "keperluan": "Pembuatan Rekening Bank",
      "keterangan_admin": "Silakan ambil di kantor desa",
      "tanggal_pengajuan": "2024-01-15T10:30:00.000000Z",
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-16T09:00:00.000000Z"
    }
  ]
}
```

---

## 🏛️ Desa Info API

### Get Desa Information
```http
GET /api/desa-info
```

**Response:**
```json
{
  "success": true,
  "data": {
    "desa": {
      "nama_desa": "DESA CIBATU",
      "kecamatan": "CIBATU",
      "kabupaten": "Purwakarta",
      "provinsi": "Jawa Barat",
      "kode_pos": "41161",
      "alamat_lengkap": "Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161",
      "telepon": "(0264) 123456",
      "email": "desacibatu.2001@gmail.com",
      "website": "https://desa-cibatu.id",
      "latitude": "-6.5001403",
      "longitude": "107.5342964"
    },
    "kepala_desa": {
      "nama": "Dr. H. John Doe, S.H., M.H.",
      "nip": "196501011990031001",
      "periode": "2020-2026"
    },
    "sekretaris": {
      "nama": "Jane Doe, S.E.",
      "nip": "197001011990032002"
    },
    "logos": {
      "desa": "/images/logo-desa.png",
      "kabupaten": "/images/logo-kabupaten.png"
    }
  }
}
```

---

## 📰 Berita API

### Get Berita List
```http
GET /api/berita?page=1&type=internal&search=judul
```

**Query Parameters:**
- `page` - Page number
- `type` - Type of news (internal, external, combined)
- `search` - Search by title
- `external_source` - External source (antara, detik, kompas, cnn)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "judul": "Pembangunan Jembatan Desa",
        "slug": "pembangunan-jembatan-desa",
        "konten": "Desa Cibatu akan membangun jembatan...",
        "gambar": "/images/berita/jembatan.jpg",
        "kategori": "Pembangunan",
        "status": "published",
        "tanggal_publish": "2024-01-15T10:00:00.000000Z",
        "penulis": "Admin Desa"
      }
    ],
    "total": 7,
    "per_page": 10
  }
}
```

### Get Combined Berita (Internal + External)
```http
GET /api/berita-combined?page=1
```

---

## 📅 Agenda API

### Get Agenda List
```http
GET /api/agenda-desa?page=1&kategori=pembangunan&tanggal=2024-01-15
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "judul": "Rapat Koordinasi Pembangunan",
      "deskripsi": "Rapat koordinasi pembangunan jembatan desa",
      "tanggal": "2024-01-20",
      "waktu": "09:00",
      "lokasi": "Balai Desa",
      "kategori": "Pembangunan",
      "status": "akan_dilaksanakan"
    }
  ]
}
```

---

## 🏗️ Transparansi API

### Get APBDes
```http
GET /api/apbdes?tahun=2024
```

### Get Proyek Pembangunan
```http
GET /api/proyek-pembangunan?status=berjalan
```

### Get Bantuan Sosial
```http
GET /api/bantuan-sosial-transparansi
```

---

## 📞 Contact API

### Submit Contact Form
```http
POST /api/v1/contact/submit
Content-Type: application/json

{
  "nama": "John Doe",
  "email": "john@example.com",
  "telepon": "081234567890",
  "subjek": "Pertanyaan Layanan",
  "pesan": "Saya ingin bertanya tentang prosedur pengajuan surat keterangan domisili. Bagaimana caranya?"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Pesan berhasil dikirim! Terima kasih atas masukan Anda.",
  "data": {
    "id": 1,
    "nama": "John Doe",
    "email": "john@example.com",
    "subjek": "Pertanyaan Layanan",
    "timestamp": "07/10/2025 02:43:08"
  }
}
```

### Get Contact Information
```http
GET /api/v1/contact/info
```

**Response:**
```json
{
  "success": true,
  "data": {
    "desa": {
      "nama_desa": "DESA CIBATU",
      "kecamatan": "CIBATU",
      "kabupaten": "Purwakarta",
      "provinsi": "Jawa Barat",
      "alamat_lengkap": "Jl. Cibatu Km. 15, Desa Cibatu, Kec. Cibatu, Kab. Purwakarta, Cibatu, Purwakarta, Jawa Barat 41161",
      "telepon": "(0264) 123456",
      "email": "desacibatu.2001@gmail.com",
      "website": "https://desa-cibatu.id"
    },
    "jam_operasional": {
      "senin_jumat": "08:00 - 16:00",
      "sabtu": "08:00 - 12:00",
      "minggu": "Libur"
    },
    "kontak_penting": {
      "kepala_desa": "(0264) 123456",
      "sekretaris": "(0264) 123457",
      "puskesmas": "(0264) 123458"
    }
  }
}
```

### Contact Message Management (Admin Only)
```http
GET /contact-messages
Authorization: Bearer {token}
```

**Query Parameters:**
- `page` - Page number
- `search` - Search by nama, email, subjek
- `status` - Filter by status (unread, read, replied, archived)

**Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nama": "John Doe",
        "email": "john@example.com",
        "telepon": "081234567890",
        "subjek": "Pertanyaan Layanan",
        "pesan": "Saya ingin bertanya tentang...",
        "status": "unread",
        "ip_address": "192.168.1.1",
        "user_agent": "Mozilla/5.0...",
        "created_at": "2025-10-07T02:43:08.000000Z",
        "read_at": null,
        "replied_at": null,
        "admin_reply": null
      }
    ],
    "total": 3,
    "per_page": 15
  },
  "statistics": {
    "total": 3,
    "unread": 3,
    "read": 0,
    "replied": 0,
    "archived": 0
  }
}
```

### Mark Message as Read
```http
POST /contact-messages/{id}/mark-read
Authorization: Bearer {token}
```

### Mark Message as Replied
```http
POST /contact-messages/{id}/mark-replied
Authorization: Bearer {token}
Content-Type: application/json

{
  "admin_reply": "Terima kasih atas pertanyaan Anda. Untuk pengajuan surat domisili..."
}
```

### Archive Message
```http
POST /contact-messages/{id}/archive
Authorization: Bearer {token}
```

### Bulk Actions
```http
POST /contact-messages/bulk-action
Authorization: Bearer {token}
Content-Type: application/json

{
  "action": "mark_read",
  "ids": [1, 2, 3]
}
```

**Available Actions:**
- `mark_read` - Mark as read
- `mark_replied` - Mark as replied
- `archive` - Archive messages
- `delete` - Delete messages

---

## 🔍 Search API

### Global Search
```http
GET /api/search?q=john&type=penduduk,surat,berita
```

**Query Parameters:**
- `q` - Search query
- `type` - Search types (penduduk, surat, berita, agenda)

**Response:**
```json
{
  "success": true,
  "data": {
    "penduduk": [
      {
        "id": 1,
        "nama": "John Doe",
        "nik": "3201234567890123",
        "alamat": "Jl. Contoh No. 1"
      }
    ],
    "surat": [
      {
        "id": 1,
        "nomor_surat": "140/SP-KEL/2024",
        "jenis_surat": "kelahiran",
        "status": "approved"
      }
    ],
    "berita": [
      {
        "id": 1,
        "judul": "Pembangunan Jembatan Desa",
        "slug": "pembangunan-jembatan-desa"
      }
    ]
  }
}
```

---

## ⚠️ Error Handling

### Error Response Format
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Validation error message"]
  },
  "error_code": "VALIDATION_ERROR"
}
```

### Common Error Codes
- `VALIDATION_ERROR` - Input validation failed
- `UNAUTHORIZED` - Authentication required
- `FORBIDDEN` - Insufficient permissions
- `NOT_FOUND` - Resource not found
- `RATE_LIMITED` - Too many requests
- `SERVER_ERROR` - Internal server error

### HTTP Status Codes
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

---

## 🔒 Rate Limiting

### Default Limits
- **API**: 60 requests per minute per IP
- **Login**: 5 attempts per minute per IP
- **Contact Form**: 10 submissions per minute per IP
- **Contact Submit**: 10 requests per minute per IP

### Headers
```http
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

---

## 📝 Examples

### JavaScript/Axios Example
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: 'https://desa-cibatu.id/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Add auth token
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Get statistics
const getStatistics = async () => {
  try {
    const response = await api.get('/statistics');
    return response.data;
  } catch (error) {
    console.error('Error:', error.response.data);
    throw error;
  }
};

// Submit surat pengajuan
const submitSuratPengajuan = async (data) => {
  try {
    const response = await api.post('/surat-pengajuan', data);
    return response.data;
  } catch (error) {
    console.error('Error:', error.response.data);
    throw error;
  }
};
```

### PHP/cURL Example
```php
<?php
$baseUrl = 'https://desa-cibatu.id/api';
$token = 'your-auth-token';

// Get statistics
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/statistics');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Total Penduduk: " . $data['data']['total_penduduk'];
} else {
    echo "Error: " . $response;
}
?>
```

---

## 🔄 Webhooks

### Surat Status Update
```http
POST /webhook/surat-status-update
Content-Type: application/json
X-Webhook-Signature: sha256=...

{
  "event": "surat.status.updated",
  "data": {
    "surat_id": 1,
    "nomor_surat": "140/SP-KEL/2024",
    "status": "approved",
    "updated_at": "2024-01-16T09:00:00.000000Z"
  }
}
```

---

## 📊 API Analytics

### Usage Statistics
- Total API calls per day
- Most used endpoints
- Response time metrics
- Error rate tracking

### Monitoring
- Real-time API health
- Performance metrics
- Error tracking
- Usage analytics

---

*For more information, visit [API Documentation](https://desa-cibatu.id/docs/api) or contact the development team.*
