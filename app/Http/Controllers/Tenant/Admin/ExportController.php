<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BantuanSosialExport;
use App\Exports\PenerimaBantuanSosialExport;
use App\Exports\PengaduanExport;
use App\Exports\UmkmExport;
use App\Exports\SuratPengajuanExport;
use App\Exports\PendudukExport;
use App\Exports\KartuKeluargaExport;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin_sistem');
    }

    /**
     * Export Bantuan Sosial
     */
    public function exportBantuanSosial(Request $request)
    {
        $filters = $request->only(['program', 'jenis', 'tahun']);
        $filename = 'bantuan_sosial_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new BantuanSosialExport($filters), $filename);
    }

    /**
     * Export Penerima Bantuan Sosial
     */
    public function exportPenerimaBantuanSosial(Request $request)
    {
        $filters = $request->only(['program', 'tahun', 'dusun']);
        $filename = 'penerima_bantuan_sosial_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PenerimaBantuanSosialExport($filters), $filename);
    }

    /**
     * Export Pengaduan
     */
    public function exportPengaduan(Request $request)
    {
        $filters = $request->only(['status', 'kategori', 'tahun', 'bulan']);
        $filename = 'pengaduan_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PengaduanExport($filters), $filename);
    }

    /**
     * Export UMKM
     */
    public function exportUmkm(Request $request)
    {
        $filters = $request->only(['jenis_usaha', 'status_usaha', 'is_unggulan', 'is_verified']);
        $filename = 'umkm_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new UmkmExport($filters), $filename);
    }

    /**
     * Export Surat Pengajuan
     */
    public function exportSuratPengajuan(Request $request)
    {
        $filters = $request->only(['jenis_surat', 'status', 'tahun', 'bulan']);
        $filename = 'surat_pengajuan_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new SuratPengajuanExport($filters), $filename);
    }

    /**
     * Export Penduduk
     */
    public function exportPenduduk(Request $request)
    {
        $filename = 'penduduk_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new PendudukExport($request), $filename);
    }

    /**
     * Export Kartu Keluarga
     */
    public function exportKartuKeluarga(Request $request)
    {
        $filters = $request->only(['dusun', 'rt', 'rw']);
        $filename = 'kartu_keluarga_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new KartuKeluargaExport($filters), $filename);
    }

    /**
     * Show Export Page
     */
    public function index()
    {
        return view('export-import.index');
    }
}
