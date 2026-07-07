<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesaInfoApiController;
use App\Http\Controllers\Api\StatisticApiController;
use App\Http\Controllers\Api\LayananApiController;
use App\Http\Controllers\Api\PengumumanApiController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\BantuanSosialController;
use App\Http\Controllers\Api\PengaduanController;
use App\Http\Controllers\Api\SuratPengajuanApiController;
use App\Http\Controllers\Api\ContactController;

use App\Http\Controllers\Api\TestimoniController;
use App\Http\Controllers\Api\ApiProxyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ========================================
// PRIVATE API ONLY (Hanya untuk Internal/Admin)
// ========================================
// ========================================
// PUBLIC API (No API Key Required)
// ========================================
Route::get('/v1/verifikasi/surat/{token}', [\App\Http\Controllers\Api\VerifikasiSuratApiController::class, 'verify'])->middleware(['throttle:30,1', 'tenant.api']);

Route::prefix('v1/public-statistics')->middleware(['throttle:10,1', 'tenant.api'])->group(function () {
    // Statistik umum untuk halaman welcome (tidak butuh API key)
    Route::get('/', [StatisticApiController::class, 'getPublicStatistics']);
    Route::get('/penduduk', [StatisticApiController::class, 'getPublicPendudukStats']);
    // Info desa publik: nama, sosmed, kontak (tidak ada data sensitif)
    Route::get('/info-desa', [DesaInfoApiController::class, 'getPublicDesaInfo']);
});

Route::prefix('v1')->middleware('tenant.api')->group(function () {

    // ========================================
    // FRONTEND DATA ENDPOINTS (Private API)
    // ========================================

    // Statistics & Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/statistics', [StatisticApiController::class, 'getStatistics'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/statistics/penduduk', [StatisticApiController::class, 'getPendudukStats'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/statistics/kk', [StatisticApiController::class, 'getKKStats'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/statistics/mutasi', [StatisticApiController::class, 'getMutasiStats'])->middleware(['throttle:100,1', 'private.api']);


    // Testimoni Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/testimoni', [TestimoniController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/testimoni/stats', [TestimoniController::class, 'stats'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/testimoni/categories', [TestimoniController::class, 'categories'])->middleware(['throttle:100,1', 'private.api']);

    // Struktur Desa Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/struktur-desa', [\App\Http\Controllers\Api\StrukturDesaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/perangkat-desa', [\App\Http\Controllers\Api\StrukturDesaController::class, 'perangkatDesa'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/rt-rw', [\App\Http\Controllers\Api\StrukturDesaController::class, 'rtRw'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/master-wilayah', [\App\Http\Controllers\Api\StrukturDesaController::class, 'masterWilayah'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/bumdes', [\App\Http\Controllers\Api\StrukturDesaController::class, 'bumdes'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/struktur-desa/category/{category}', [\App\Http\Controllers\Api\StrukturDesaController::class, 'byCategory'])->middleware(['throttle:100,1', 'private.api']);

    // Berita & Content - Frontend routes (dengan API key untuk keamanan)
    Route::get('/berita', [BeritaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/berita/{slug}', [BeritaController::class, 'show'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/berita-featured', [BeritaController::class, 'featured'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/berita-categories', [BeritaController::class, 'categories'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/berita-latest', [BeritaController::class, 'latest'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/berita-search', [BeritaController::class, 'search'])->middleware(['throttle:50,1', 'private.api']);
    Route::get('/berita-by-category/{category}', [BeritaController::class, 'getByCategory'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/berita-eksternal', [\App\Http\Controllers\Api\BeritaEksternalController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/berita-combined', [\App\Http\Controllers\Api\BeritaEksternalController::class, 'internalExternalCombined'])->middleware(['throttle:300,1', 'private.api']);

    // Desa Info & Services - Frontend routes (dengan API key untuk keamanan)
    Route::get('/desa-info', [DesaInfoApiController::class, 'getDesaInfo'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/contact-info', [DesaInfoApiController::class, 'getContactInfo'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/contact/info', [ContactController::class, 'info'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/kontak-desa', [\App\Http\Controllers\Api\KontakDesaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    // GeoJSON batas wilayah — dibaca server-side, file tidak langsung diekspos ke publik
    Route::get('/geojson', [DesaInfoApiController::class, 'getGeoJson'])->middleware(['throttle:60,1', 'private.api']);

    // Proyek & Development - Frontend routes (dengan API key untuk keamanan)
    Route::get('/proyek-desa', [\App\Http\Controllers\Api\ProyekDesaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/proyek-desa/{id}', [\App\Http\Controllers\Api\ProyekDesaController::class, 'show'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/proyek-desa/tahun/{year}', [\App\Http\Controllers\Api\ProyekDesaController::class, 'byYear'])->middleware(['throttle:100,1', 'private.api']);

    // Announcements - Frontend routes (dengan API key untuk keamanan)
    Route::get('/announcements', [PengumumanApiController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/announcements/{id}', [PengumumanApiController::class, 'show'])->middleware(['throttle:100,1', 'private.api']);

    // Bantuan Sosial
    Route::get('/bantuan-sosial', [BantuanSosialController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);

    // UMKM
    Route::get('/umkm', [\App\Http\Controllers\Api\UmkmController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/umkm/{id}', [\App\Http\Controllers\Api\UmkmController::class, 'show'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/umkm-unggulan', [\App\Http\Controllers\Api\UmkmController::class, 'unggulan'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/umkm-jenis/{jenis}', [\App\Http\Controllers\Api\UmkmController::class, 'byJenisUsaha'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/umkm-statistics', [\App\Http\Controllers\Api\UmkmController::class, 'statistics'])->middleware(['throttle:100,1', 'private.api']);

    // Agenda Desa
    Route::get('/agenda-desa', [\App\Http\Controllers\Api\AgendaDesaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/agenda-categories', [\App\Http\Controllers\Api\AgendaDesaController::class, 'categories'])->middleware(['throttle:100,1', 'private.api']);

    // Transparansi
    Route::get('/transparansi', [\App\Http\Controllers\Api\TransparansiController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/apbdes', [\App\Http\Controllers\Api\TransparansiController::class, 'apbdes'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/proyek-pembangunan', [\App\Http\Controllers\Api\TransparansiController::class, 'proyekPembangunan'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/bantuan-sosial-transparansi', [\App\Http\Controllers\Api\TransparansiController::class, 'bantuanSosialTransparansi'])->middleware(['throttle:100,1', 'private.api']);

    // Fasilitas Desa
    Route::get('/fasilitas-desa', [\App\Http\Controllers\Api\FasilitasDesaController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);
    Route::get('/fasilitas-desa/jenis/{jenis}', [\App\Http\Controllers\Api\FasilitasDesaController::class, 'byJenis'])->middleware(['throttle:100,1', 'private.api']);

    Route::get('/surat-types', [SuratPengajuanApiController::class, 'index'])->middleware(['throttle:100,1', 'private.api']);

    // Search Penduduk (Verifikasi NIK - reCAPTCHA dinonaktifkan sementara untuk lomba #JuaraVibeCoding)
    Route::post('/search-penduduk', [SuratPengajuanApiController::class, 'checkNik'])->middleware(['throttle:100,1', 'private.api', 'captcha:v3']);

    // Admin Notifications (for header)
    Route::get('/contact-messages/notifications', [\App\Http\Controllers\Tenant\Pelayanan\ContactMessageController::class, 'notifications'])->middleware(['throttle:100,1', 'private.api']);

    // ========================================
    // FORM SUBMISSIONS (Private API)
    // ========================================

    // Contact Form
    Route::post('/contact/submit', [ContactController::class, 'submit'])->middleware(['throttle:100,1', 'private.api', 'captcha:v2']);

    // Testimoni Form
    Route::post('/testimoni', [TestimoniController::class, 'store'])->middleware(['throttle:100,1', 'private.api', 'captcha:v2']);

    // Pengaduan Form
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::post('/pengaduan/submit', [PengaduanController::class, 'submit'])->middleware(['throttle:100,1', 'private.api', 'captcha:v2']);

    // Surat Pengajuan Forms
    Route::post('/surat-pengajuan', [SuratPengajuanApiController::class, 'store'])->middleware(['throttle:100,1', 'private.api', 'captcha:v2']);
    Route::get('/surat-status', [SuratPengajuanApiController::class, 'checkStatus'])->middleware(['throttle:300,1', 'private.api']);
    Route::post('/surat-history', [SuratPengajuanApiController::class, 'getHistory'])->middleware(['throttle:100,1', 'private.api']);
    Route::post('/chat', [\App\Http\Controllers\Api\AiController::class, 'chat'])->middleware(['throttle:300,1', 'private.api']);

    // Bantuan Sosial Check
    Route::post('/bantuan-sosial/check', [BantuanSosialController::class, 'checkByNik'])->middleware(['throttle:100,1', 'private.api', 'captcha:v2']);

    // CSRF Token (Hanya untuk Web Desa Resmi)
    Route::get('/csrf-token', function() {
        return response()->json([
            'csrf_token' => csrf_token(),
            'expires_at' => now()->addMinutes(config('session.lifetime'))->toIso8601String()
        ]);
    })->middleware(['throttle:300,1', 'private.api']);

    // CAPTCHA & Rate Limiting
    Route::get('/captcha', [\App\Http\Controllers\Api\SecureSearchController::class, 'generateCaptcha'])->middleware(['throttle:300,5', 'private.api']);
    Route::get('/rate-limit-status', [\App\Http\Controllers\Api\SecureSearchController::class, 'getRateLimitStatus'])->middleware(['throttle:200,1', 'private.api']);

});

// ========================================
// API PROXY (Frontend → Proxy → Private API)
// ========================================
Route::prefix('proxy/v1')->middleware(['throttle:300,1'])->group(function () {
    // CORS preflight
    Route::options('{path?}', [ApiProxyController::class, 'options'])->where('path', '.*');

    // Proxy untuk semua endpoint
    Route::any('{path?}', [ApiProxyController::class, 'proxy'])->where('path', '.*');
});

// ========================================
// ADMIN PANEL API (Next.js Ready)
// ========================================
// Route::prefix('v1/admin')->name('admin-api.')->group(function () {
//     // Login & Password Reset (Public)
//     Route::post('/login', [\App\Http\Controllers\ApiAdminPanel\Auth\AuthenticatedSessionController::class, 'store']);
//     Route::post('/forgot-password', [\App\Http\Controllers\ApiAdminPanel\Auth\PasswordResetLinkController::class, 'store']);
//     Route::post('/reset-password', [\App\Http\Controllers\ApiAdminPanel\Auth\NewPasswordController::class, 'store']);

//     // Protected (Token required)
//     Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
//         Route::post('/logout', [\App\Http\Controllers\ApiAdminPanel\Auth\AuthenticatedSessionController::class, 'destroy']);

//         // Target pertama: Dashboard Statistics
//         Route::get('/statistics', [\App\Http\Controllers\ApiAdminPanel\StatisticsController::class, 'index']);

//         // Target kedua: Data Penduduk
//         Route::get('/penduduk/export', [\App\Http\Controllers\ApiAdminPanel\PendudukController::class, 'exportExcel']);
//         Route::apiResource('penduduk', \App\Http\Controllers\ApiAdminPanel\PendudukController::class);
//         Route::get('/penduduk-search', [\App\Http\Controllers\ApiAdminPanel\PendudukController::class, 'search']);
//         Route::get('/penduduk-check-nik', [\App\Http\Controllers\ApiAdminPanel\PendudukController::class, 'checkNIKExists']);
//         Route::get('/penduduk-check-nkk', [\App\Http\Controllers\ApiAdminPanel\PendudukController::class, 'checkNKKExists']);

//         // Target ketiga: Mutasi
//         Route::apiResource('mutasi', \App\Http\Controllers\ApiAdminPanel\MutasiController::class);
//         Route::get('/mutasi/{mutasi}/print-kematian', [\App\Http\Controllers\ApiAdminPanel\MutasiController::class, 'printKematian']);
//         Route::get('/mutasi-search-kk', [\App\Http\Controllers\ApiAdminPanel\MutasiController::class, 'searchKK']);
//         Route::get('/mutasi-search-penduduk', [\App\Http\Controllers\ApiAdminPanel\MutasiController::class, 'searchPenduduk']);
//         Route::get('/mutasi-anggota-keluarga', [\App\Http\Controllers\ApiAdminPanel\MutasiController::class, 'getAnggotaKeluarga']);

//         // Target keempat: Kartu Keluarga
//         Route::get('/kartu-keluarga/bermasalah', [\App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class, 'indexBermasalah']);
//         Route::apiResource('kartu-keluarga', \App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class);
//         Route::post('/kartu-keluarga/sync', [\App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class, 'sync']);
//         Route::post('/kartu-keluarga/{nkk}/resolve-sementara', [\App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class, 'resolveKkSementara']);
//         Route::post('/kartu-keluarga/{nkk}/batalkan-sementara', [\App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class, 'batalkanSementara']);
//         Route::post('/kartu-keluarga/{nkk}/resolve-permanen', [\App\Http\Controllers\ApiAdminPanel\KartuKeluargaController::class, 'resolveKkPermanen']);

//         // Target kelima: Wilayah
//         Route::get('/wilayah', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'index']);
//         Route::get('/wilayah/tree', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'getTree']);
//         Route::post('/wilayah/dusun', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'storeDusun']);
//         Route::post('/wilayah/rw', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'storeRw']);
//         Route::post('/wilayah/rt', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'storeRt']);
//         Route::delete('/wilayah/rt/{rt}', [\App\Http\Controllers\ApiAdminPanel\WilayahController::class, 'destroyRt']);

//         // Target keenam: User & Role Management
//         Route::apiResource('users', \App\Http\Controllers\ApiAdminPanel\UserController::class);
//         Route::get('/roles', [\App\Http\Controllers\ApiAdminPanel\RoleController::class, 'index']);
//         Route::get('/permissions', [\App\Http\Controllers\ApiAdminPanel\RoleController::class, 'permissions']);
//         Route::post('/roles', [\App\Http\Controllers\ApiAdminPanel\RoleController::class, 'store']);
//         Route::put('/roles/{role}', [\App\Http\Controllers\ApiAdminPanel\RoleController::class, 'update']);

//         // Target ketujuh: Surat-menyurat
//         Route::get('/surat/types', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'index']); // Dianggap index sementara
//         Route::get('/surat/history', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'index']); // History pake index dengan filter
//         Route::get('/surat/statistics', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'statistics']);
//         Route::post('/surat/{type}', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'store']);
//         Route::get('/surat/{surat}/download', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'download']);

//         Route::apiResource('surat-pengajuan', \App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class)->only(['index', 'store', 'show']);
//         Route::patch('/surat-pengajuan/{surat_pengajuan}/status', [\App\Http\Controllers\ApiAdminPanel\SuratPengajuanController::class, 'updateStatus']);

//         // Target kedelapan: Anggaran
//         Route::get('/anggaran', [\App\Http\Controllers\ApiAdminPanel\AnggaranController::class, 'index']);
//         Route::post('/anggaran', [\App\Http\Controllers\ApiAdminPanel\AnggaranController::class, 'store']);
//         Route::post('/anggaran/pengeluaran', [\App\Http\Controllers\ApiAdminPanel\AnggaranController::class, 'storePengeluaran']);
//         Route::get('/anggaran/proyek', [\App\Http\Controllers\ApiAdminPanel\AnggaranController::class, 'listProyek']);

//         // Target kesembilan: Bantuan Sosial
//         Route::get('/bantuan-sosial', [\App\Http\Controllers\ApiAdminPanel\BantuanSosialController::class, 'index']);
//         Route::post('/bantuan-sosial', [\App\Http\Controllers\ApiAdminPanel\BantuanSosialController::class, 'store']);
//         Route::get('/bantuan-sosial/cek-nik', [\App\Http\Controllers\ApiAdminPanel\BantuanSosialController::class, 'checkByNik']);
//         Route::post('/bantuan-sosial/{bantuan_sosial}/penerima', [\App\Http\Controllers\ApiAdminPanel\BantuanSosialController::class, 'addPenerima']);

//         // Target kesepuluh: Pengaturan Desa
//         Route::get('/settings', [\App\Http\Controllers\ApiAdminPanel\DesaSettingsController::class, 'index']);
//         Route::post('/settings', [\App\Http\Controllers\ApiAdminPanel\DesaSettingsController::class, 'update']);
//         Route::post('/settings/{key}', [\App\Http\Controllers\ApiAdminPanel\DesaSettingsController::class, 'updateSetting']);

//         // Target kesebelas: CMS & Konten
//         Route::apiResource('berita', \App\Http\Controllers\ApiAdminPanel\BeritaController::class);
//         Route::apiResource('umkm', \App\Http\Controllers\ApiAdminPanel\UmkmController::class);
//         Route::apiResource('fasilitas-desa', \App\Http\Controllers\ApiAdminPanel\FasilitasDesaController::class);
//         Route::apiResource('struktur-desa', \App\Http\Controllers\ApiAdminPanel\StrukturDesaController::class);
//         Route::apiResource('kontak-desa', \App\Http\Controllers\ApiAdminPanel\KontakDesaController::class);

//         Route::get('/testimoni', [\App\Http\Controllers\ApiAdminPanel\TestimoniController::class, 'index']);
//         Route::post('/testimoni', [\App\Http\Controllers\ApiAdminPanel\TestimoniController::class, 'store']);
//         Route::patch('/testimoni/{testimoni}/status', [\App\Http\Controllers\ApiAdminPanel\TestimoniController::class, 'updateStatus']);
//         Route::delete('/testimoni/{testimoni}', [\App\Http\Controllers\ApiAdminPanel\TestimoniController::class, 'destroy']);

//         // Target kedua belas: Interaksi & Komunikasi
//         Route::apiResource('pengaduan', \App\Http\Controllers\ApiAdminPanel\PengaduanController::class);

//         Route::prefix('contact-messages')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'index']);
//             Route::get('/notifications', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'notifications']);
//             Route::post('/bulk-action', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'bulkAction']);
//             Route::get('/{contact_message}', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'show']);
//             Route::post('/{contact_message}/reply', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'markAsReplied']);
//             Route::post('/{contact_message}/archive', [\App\Http\Controllers\ApiAdminPanel\ContactMessageController::class, 'archive']);
//         });

//         Route::prefix('notifications')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\NotificationController::class, 'index']);
//             Route::post('/mark-read', [\App\Http\Controllers\ApiAdminPanel\NotificationController::class, 'markAsRead']);
//             Route::post('/mark-all-read', [\App\Http\Controllers\ApiAdminPanel\NotificationController::class, 'markAllRead']);
//         });

//         // Target ketiga belas: Sistem & Utilitas
//         Route::prefix('audit-log')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\AuditLogController::class, 'index']);
//             Route::get('/{activity}', [\App\Http\Controllers\ApiAdminPanel\AuditLogController::class, 'show']);
//             Route::post('/clear', [\App\Http\Controllers\ApiAdminPanel\AuditLogController::class, 'clear']);
//         });

//         Route::prefix('backup')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\BackupController::class, 'index']);
//             Route::post('/', [\App\Http\Controllers\ApiAdminPanel\BackupController::class, 'create']);
//             Route::get('/download/{filename}', [\App\Http\Controllers\ApiAdminPanel\BackupController::class, 'download']);
//             Route::delete('/{filename}', [\App\Http\Controllers\ApiAdminPanel\BackupController::class, 'destroy']);
//         });

//         Route::prefix('laporan')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\LaporanController::class, 'index']);
//             Route::get('/penduduk', [\App\Http\Controllers\ApiAdminPanel\LaporanController::class, 'penduduk']);
//             Route::get('/mutasi', [\App\Http\Controllers\ApiAdminPanel\LaporanController::class, 'mutasi']);
//             Route::post('/generate', [\App\Http\Controllers\ApiAdminPanel\LaporanController::class, 'generate']);
//         });

//         Route::prefix('export-import')->group(function() {
//             Route::get('/export/penduduk', [\App\Http\Controllers\ApiAdminPanel\ExportImportController::class, 'exportPenduduk']);
//             Route::get('/export/kk', [\App\Http\Controllers\ApiAdminPanel\ExportImportController::class, 'exportKartuKeluarga']);
//             Route::post('/preview/penduduk', [\App\Http\Controllers\ApiAdminPanel\ExportImportController::class, 'previewPenduduk']);
//             Route::post('/import/penduduk', [\App\Http\Controllers\ApiAdminPanel\ExportImportController::class, 'importPenduduk']);
//         });

//         // Target terakhir: Statistik & Profil
//         Route::get('/comparison', [\App\Http\Controllers\ApiAdminPanel\ComparisonController::class, 'index']);

//         Route::prefix('profile')->group(function() {
//             Route::get('/', [\App\Http\Controllers\ApiAdminPanel\ProfileController::class, 'show']);
//             Route::put('/', [\App\Http\Controllers\ApiAdminPanel\ProfileController::class, 'update']);
//             Route::put('/password', [\App\Http\Controllers\ApiAdminPanel\ProfileController::class, 'updatePassword']);
//         });
//     });
// });
