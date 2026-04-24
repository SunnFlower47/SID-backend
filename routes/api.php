<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WebDesaController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\BantuanSosialController;
use App\Http\Controllers\Api\PengaduanController;
use App\Http\Controllers\Api\SuratPengajuanApiController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PendudukApiController;
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
Route::prefix('v1')->group(function () {

    // ========================================
    // FRONTEND DATA ENDPOINTS (Private API)
    // ========================================

    // Statistics & Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/statistics', [WebDesaController::class, 'getStatistics'])->middleware(['throttle:500,1', 'private.api', 'api.key']);
    Route::get('/statistics/penduduk', [WebDesaController::class, 'getPendudukStats'])->middleware(['throttle:500,1', 'private.api', 'api.key']);
    Route::get('/statistics/kk', [WebDesaController::class, 'getKKStats'])->middleware(['throttle:500,1', 'private.api', 'api.key']);
    Route::get('/statistics/mutasi', [WebDesaController::class, 'getMutasiStats'])->middleware(['throttle:500,1', 'private.api', 'api.key']);

    // Penduduk Data - Frontend routes (dengan API key untuk keamanan)
    // Route::get('/penduduk', [PendudukApiController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    // Route::get('/penduduk/age-statistics', [PendudukApiController::class, 'ageStatistics'])->middleware(['throttle:400,1', 'private.api', 'api.key']);
    // Route::get('/penduduk/filter-options', [PendudukApiController::class, 'filterOptions'])->middleware(['throttle:400,1', 'private.api', 'api.key']);

    // Testimoni Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/testimoni', [TestimoniController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/testimoni/stats', [TestimoniController::class, 'stats'])->middleware(['throttle:400,1', 'private.api', 'api.key']);
    Route::get('/testimoni/categories', [TestimoniController::class, 'categories'])->middleware(['throttle:400,1', 'private.api', 'api.key']);

    // Struktur Desa Data - Frontend routes (dengan API key untuk keamanan)
    Route::get('/struktur-desa', [App\Http\Controllers\Api\StrukturDesaController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/perangkat-desa', [App\Http\Controllers\Api\StrukturDesaController::class, 'perangkatDesa'])->middleware(['throttle:400,1', 'private.api', 'api.key']);
    Route::get('/rt-rw', [App\Http\Controllers\Api\StrukturDesaController::class, 'rtRw'])->middleware(['throttle:400,1', 'private.api', 'api.key']);
    Route::get('/bumdes', [App\Http\Controllers\Api\StrukturDesaController::class, 'bumdes'])->middleware(['throttle:400,1', 'private.api', 'api.key']);
    Route::get('/struktur-desa/category/{category}', [App\Http\Controllers\Api\StrukturDesaController::class, 'byCategory'])->middleware(['throttle:400,1', 'private.api', 'api.key']);

    // Berita & Content - Frontend routes (dengan API key untuk keamanan)
    Route::get('/berita', [BeritaController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita/{slug}', [BeritaController::class, 'show'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-featured', [BeritaController::class, 'featured'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-categories', [BeritaController::class, 'categories'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-latest', [BeritaController::class, 'latest'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-search', [BeritaController::class, 'search'])->middleware(['throttle:200,1', 'private.api', 'api.key']);
    Route::get('/berita-by-category/{category}', [BeritaController::class, 'getByCategory'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-eksternal', [App\Http\Controllers\Api\BeritaEksternalController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/berita-combined', [App\Http\Controllers\Api\BeritaEksternalController::class, 'internalExternalCombined'])->middleware(['throttle:300,1', 'private.api', 'api.key']);

    // Desa Info & Services - Frontend routes (dengan API key untuk keamanan)
    Route::get('/desa-info', [App\Http\Controllers\Api\WebDesaController::class, 'desaInfo'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/contact-info', [App\Http\Controllers\Api\WebDesaController::class, 'contactInfo'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/contact/info', [ContactController::class, 'info'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/kontak-desa', [App\Http\Controllers\Api\KontakDesaController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);

    // Proyek & Development - Frontend routes (dengan API key untuk keamanan)
    Route::get('/proyek-desa', [App\Http\Controllers\Api\ProyekDesaController::class, 'index'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/proyek-desa/{id}', [App\Http\Controllers\Api\ProyekDesaController::class, 'show'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/proyek-desa/tahun/{year}', [App\Http\Controllers\Api\ProyekDesaController::class, 'byYear'])->middleware(['throttle:300,1', 'private.api', 'api.key']);

    // Announcements - Frontend routes (dengan API key untuk keamanan)
    Route::get('/announcements', [WebDesaController::class, 'getAnnouncements'])->middleware(['throttle:300,1', 'private.api', 'api.key']);
    Route::get('/announcements/{id}', [WebDesaController::class, 'getAnnouncement'])->middleware(['throttle:300,1', 'private.api', 'api.key']);

    // Bantuan Sosial
    Route::get('/bantuan-sosial', [BantuanSosialController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);

    // UMKM
    Route::get('/umkm', [App\Http\Controllers\Api\UmkmController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/umkm/{id}', [App\Http\Controllers\Api\UmkmController::class, 'show'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/umkm-unggulan', [App\Http\Controllers\Api\UmkmController::class, 'unggulan'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/umkm-jenis/{jenis}', [App\Http\Controllers\Api\UmkmController::class, 'byJenisUsaha'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/umkm-statistics', [App\Http\Controllers\Api\UmkmController::class, 'statistics'])->middleware(['throttle:300,1', 'private.api']);

    // Agenda Desa
    Route::get('/agenda-desa', [App\Http\Controllers\Api\AgendaDesaController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/agenda-categories', [App\Http\Controllers\Api\AgendaDesaController::class, 'categories'])->middleware(['throttle:300,1', 'private.api']);

    // Transparansi
    Route::get('/transparansi', [App\Http\Controllers\Api\TransparansiController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/apbdes', [App\Http\Controllers\Api\TransparansiController::class, 'apbdes'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/proyek-pembangunan', [App\Http\Controllers\Api\TransparansiController::class, 'proyekPembangunan'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/bantuan-sosial-transparansi', [App\Http\Controllers\Api\TransparansiController::class, 'bantuanSosialTransparansi'])->middleware(['throttle:300,1', 'private.api']);

    // Fasilitas Desa
    Route::get('/fasilitas-desa', [App\Http\Controllers\Api\FasilitasDesaController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::get('/fasilitas-desa/jenis/{jenis}', [App\Http\Controllers\Api\FasilitasDesaController::class, 'byJenis'])->middleware(['throttle:300,1', 'private.api']);

    // Surat Types
    Route::get('/surat-types', [WebDesaController::class, 'getSuratTypes'])->middleware(['throttle:300,1', 'private.api']);

    // Admin Notifications (for header)
    Route::get('/contact-messages/notifications', [App\Http\Controllers\Admin\ContactMessageController::class, 'notifications'])->middleware(['throttle:200,1', 'private.api']);

    // ========================================
    // FORM SUBMISSIONS (Private API)
    // ========================================

    // Contact Form
    Route::post('/contact/submit', [ContactController::class, 'submit'])->middleware(['throttle:30,1', 'private.api']);

    // Testimoni Form
    Route::post('/testimoni', [TestimoniController::class, 'store'])->middleware(['throttle:50,1', 'private.api']);

    // Pengaduan Form
    Route::get('/pengaduan', [PengaduanController::class, 'index'])->middleware(['throttle:300,1', 'private.api']);
    Route::post('/pengaduan/submit', [PengaduanController::class, 'submit'])->middleware(['throttle:30,1', 'private.api']);

    // Surat Pengajuan Forms
    Route::post('/surat-pengajuan', [SuratPengajuanApiController::class, 'store'])->middleware(['throttle:30,1', 'private.api']);
    Route::get('/check-nik/{nik}', [SuratPengajuanApiController::class, 'checkNik'])->middleware(['throttle:400,1', 'private.api']);
    Route::get('/surat-pengajuan/{id}/status', [SuratPengajuanApiController::class, 'checkStatus'])->middleware(['throttle:500,1', 'private.api']);
    Route::match(['GET', 'POST'], '/surat-pengajuan/nik/{nik}', [SuratPengajuanApiController::class, 'getByNik'])->middleware(['throttle:400,1', 'private.api']);
    Route::get('/surat-pengajuan/search', [SuratPengajuanApiController::class, 'getByNomorSurat'])->middleware(['throttle:400,1', 'private.api']);

    // Bantuan Sosial Check
    Route::post('/bantuan-sosial/check', [BantuanSosialController::class, 'checkByNik'])->middleware(['throttle:50,1', 'private.api']);

    // CAPTCHA & Rate Limiting
    Route::get('/captcha', [App\Http\Controllers\Api\SecureSearchController::class, 'generateCaptcha'])->middleware(['throttle:300,5', 'private.api']);
    Route::get('/rate-limit-status', [App\Http\Controllers\Api\SecureSearchController::class, 'getRateLimitStatus'])->middleware(['throttle:200,1', 'private.api']);

});

// ========================================
// API PROXY (Frontend → Proxy → Private API)
// ========================================
Route::prefix('proxy/v1')->middleware(['throttle:60,1'])->group(function () {
    // CORS preflight
    Route::options('{path?}', [ApiProxyController::class, 'options'])->where('path', '.*');

    // Proxy untuk semua endpoint
    Route::any('{path?}', [ApiProxyController::class, 'proxy'])->where('path', '.*');
});

// Admin API routes (protected with Sanctum)
Route::middleware(['auth:sanctum', 'throttle:100,1'])->prefix('admin')->group(function () {
    // Add admin API routes here if needed
});

// Admin notifications API (for header notifications) - Moved to private API
// Route::middleware(['auth:sanctum', 'throttle:200,1'])->group(function () {
//     Route::get('/contact-messages/notifications', [App\Http\Controllers\Admin\ContactMessageController::class, 'notifications'])->name('api.contact-messages.notifications');
// });
