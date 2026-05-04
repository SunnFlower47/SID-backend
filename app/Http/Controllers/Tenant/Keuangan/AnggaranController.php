<?php

namespace App\Http\Controllers\Tenant\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Apbdes;
use App\Models\ProyekDesa;
use App\Models\HistoriPengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class AnggaranController extends Controller
{
        public function __construct()
    {
        $this->middleware(['auth', 'can:keuangan']);
    }

    /**
     * Show form to create annual budget
     */
    public function createAnggaranTahunan()
    {
        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $currentYear = date('Y');

        return view('keuangan.apbdes.index', compact('tahunList', 'currentYear'));
    }

    /**
     * Store annual budget
     */
    public function storeAnggaranTahunan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tahun' => 'required|integer|min:2020|max:2030',
            'jenis' => 'required|in:pendapatan,belanja,pembiayaan',
            'sumber_dana' => 'required|in:dana_desa_ad,dana_desa_af,dana_desa_ak,dau,dak,dbh,did,pad',
            'kode_rekening' => 'required|string|max:20',
            'nama_rekening' => 'required|string|max:255',
            'anggaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['realisasi'] = 0; // Default realisasi 0
        $data['sisa_anggaran'] = $data['anggaran'];
        $data['status'] = 'disetujui';

        Apbdes::create($data);

        return redirect()->route('transparansi-desa.apbdes', ['tahun' => $data['tahun']])
            ->with('success', 'Anggaran tahunan berhasil ditambahkan.');
    }

    /**
     * Show form to add expenditure
     */
    public function createPengeluaran(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $jenis = $request->get('jenis', 'belanja');

        // Get APBDes entries that can receive expenditure
        $apbdesList = Apbdes::tahun($tahun)
            ->jenis($jenis)
            ->where('status', 'disetujui')
            ->whereRaw('realisasi < anggaran') // Only those with remaining budget
            ->orderBy('nama_rekening')
            ->get();

        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');

        return view('keuangan.realisasi.index', compact('apbdesList', 'tahunList', 'tahun', 'jenis'));
    }

    /**
     * Store expenditure
     */
    public function storePengeluaran(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apbdes_id' => 'required|exists:apbdes,id',
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_pengeluaran' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $apbdes = Apbdes::findOrFail($request->apbdes_id);

            // Check if expenditure exceeds remaining budget
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;
            if ($request->jumlah > $sisaAnggaran) {
                return redirect()->back()
                    ->withErrors(['jumlah' => 'Jumlah pengeluaran melebihi sisa anggaran yang tersedia (Rp ' . number_format($sisaAnggaran, 0, ',', '.') . ')'])
                    ->withInput();
            }

            // Create expenditure history record
            HistoriPengeluaran::create([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'apbdes_id' => $apbdes->id,
                'jumlah' => $request->jumlah,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'keterangan' => $request->keterangan,
                'user_id' => 1, // Default admin user
            ]);

            // Update realisasi
            $apbdes->realisasi += $request->jumlah;
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            DB::commit();

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun])
                ->with('success', 'Pengeluaran "' . $request->nama_pengeluaran . '" berhasil ditambahkan. Realisasi: Rp ' . number_format((float)$apbdes->realisasi, 0, ',', '.') . ', Sisa: Rp ' . number_format((float)$apbdes->sisa_anggaran, 0, ',', '.') . '.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show form to create project
     */
    public function createProyek()
    {
        $tahunList = Apbdes::select('tahun')->distinct()->orderBy('tahun', 'desc')->pluck('tahun');
        $currentYear = date('Y');

        // Get available APBDes entries for current year (belanja type with remaining budget)
        $apbdesList = Apbdes::where('tahun', $currentYear)
            ->where('jenis', 'belanja')
            ->where('status', 'disetujui')
            ->whereRaw('realisasi < anggaran') // Only those with remaining budget
            ->orderBy('nama_rekening')
            ->get();

        return view('keuangan.proyek.index', compact('tahunList', 'currentYear', 'apbdesList'));
    }

    /**
     * Store project and link to APBDes
     */
    public function storeProyek(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_proyek' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:1000',
            'jenis' => 'required|in:infrastruktur,sosial,ekonomi,lingkungan,lainnya',
            'anggaran' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'lokasi' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'kontraktor' => 'nullable|string|max:255',
            'tahun_anggaran' => 'required|integer|min:2020|max:2030',
            'apbdes_id' => 'required|exists:apbdes,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Get selected APBDes entry
            $apbdes = Apbdes::findOrFail($request->apbdes_id);

            // Check if project budget exceeds remaining APBDes budget
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;
            if ($request->anggaran > $sisaAnggaran) {
                return redirect()->back()
                    ->withErrors(['anggaran' => 'Anggaran proyek melebihi sisa anggaran APBDes yang tersedia (Rp ' . number_format((float)$sisaAnggaran, 0, ',', '.') . ')'])
                    ->withInput();
            }

            // Create project
            $proyekData = $request->only([
                'nama_proyek', 'deskripsi', 'jenis', 'anggaran',
                'tanggal_mulai', 'tanggal_selesai', 'lokasi',
                'penanggung_jawab', 'kontraktor'
            ]);
            $proyekData['realisasi'] = 0;
            $proyekData['status'] = 'perencanaan';
            $proyekData['progress'] = 0;
            $proyekData['apbdes_id'] = $apbdes->id; // Link to APBDes

            $proyek = ProyekDesa::create($proyekData);

            // Update APBDes realisasi (reduce available budget)
            $apbdes->realisasi += $request->anggaran;
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            DB::commit();

            $message = 'Proyek berhasil dibuat dan terhubung dengan rekening APBDes: ' . $apbdes->kode_rekening . ' - ' . $apbdes->nama_rekening . '.';
            $message .= ' Sisa anggaran rekening: Rp ' . number_format((float)$apbdes->sisa_anggaran, 0, ',', '.') . '.';

            return redirect()->route('transparansi-desa.proyek')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update project realization and sync with APBDes
     */
    public function updateRealisasiProyek(Request $request, ProyekDesa $proyek)
    {
        $validator = Validator::make($request->all(), [
            'realisasi' => 'required|numeric|min:0|max:' . $proyek->anggaran,
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldRealisasi = $proyek->realisasi;
            $newRealisasi = $request->realisasi;
            $selisih = $newRealisasi - $oldRealisasi;

            // Update project realization
            $proyek->realisasi = $newRealisasi;
            $proyek->progress = $proyek->anggaran > 0 ? round(($newRealisasi / $proyek->anggaran) * 100) : 0;

            // Update status based on progress
            if ($proyek->progress >= 100) {
                $proyek->status = 'selesai';
            } elseif ($proyek->progress > 0) {
                $proyek->status = 'berjalan';
            }

            $proyek->save();

            // Find and update related APBDes
            $apbdes = Apbdes::where('nama_rekening', 'LIKE', '%' . $proyek->nama_proyek . '%')
                ->where('jenis', 'belanja')
                ->first();

            if ($apbdes) {
                $apbdes->realisasi += $selisih;
                $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
                $apbdes->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Realisasi proyek berhasil diperbarui',
                'data' => [
                    'realisasi' => $proyek->realisasi,
                    'progress' => $proyek->progress,
                    'status' => $proyek->status,
                    'apbdes_updated' => $apbdes ? true : false
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show movement history for specific APBDes (all types: pendapatan, belanja, pembiayaan)
     */
    public function historiPengeluaran($id)
    {
        $apbdes = Apbdes::with('historiPengeluarans.user')->findOrFail($id);

        return view('keuangan.realisasi.history', compact('apbdes'));
    }

    /**
     * Show edit form for expenditure
     */
    public function editPengeluaran($id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);

        return view('keuangan.realisasi.edit', compact('pengeluaran'));
    }

    /**
     * Update expenditure
     */
    public function updatePengeluaran(Request $request, $id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);
        $apbdes = $pengeluaran->apbdes;

        $validator = Validator::make($request->all(), [
            'nama_pengeluaran' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
            'tanggal_pengeluaran' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Calculate the difference in amount
            $oldJumlah = $pengeluaran->jumlah;
            $newJumlah = $request->jumlah;
            $selisih = $newJumlah - $oldJumlah;

            // Check if new amount exceeds remaining budget
            $sisaAnggaran = $apbdes->anggaran - $apbdes->realisasi;
            if ($selisih > $sisaAnggaran) {
                return redirect()->back()
                    ->withErrors(['jumlah' => 'Jumlah pengeluaran melebihi sisa anggaran yang tersedia (Rp ' . number_format((float)$sisaAnggaran, 0, ',', '.') . ')'])
                    ->withInput();
            }

            // Update expenditure record
            $pengeluaran->update([
                'nama_pengeluaran' => $request->nama_pengeluaran,
                'jumlah' => $request->jumlah,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'keterangan' => $request->keterangan,
            ]);

            // Update APBDes realisasi
            $apbdes->realisasi = $apbdes->realisasi + $selisih;
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            DB::commit();

            return redirect()->route('anggaran.histori-pengeluaran', $apbdes->id)
                ->with('success', 'Pengeluaran "' . $request->nama_pengeluaran . '" berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete expenditure
     */
    public function deletePengeluaran($id)
    {
        $pengeluaran = HistoriPengeluaran::with('apbdes')->findOrFail($id);
        $apbdes = $pengeluaran->apbdes;

        try {
            DB::beginTransaction();

            // Update APBDes realisasi (add back the amount)
            $apbdes->realisasi = $apbdes->realisasi - $pengeluaran->jumlah;
            $apbdes->sisa_anggaran = $apbdes->anggaran - $apbdes->realisasi;
            $apbdes->save();

            // Delete expenditure record
            $pengeluaran->delete();

            DB::commit();

            return redirect()->route('anggaran.histori-pengeluaran', $apbdes->id)
                ->with('success', 'Pengeluaran "' . $pengeluaran->nama_pengeluaran . '" berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for APBDes
     */
    public function editApbdes($id)
    {
        $this->authorize('anggaran.edit');

        $apbdes = Apbdes::findOrFail($id);

        return view('keuangan.apbdes.edit', compact('apbdes'));
    }

    /**
     * Update APBDes
     */
    public function updateApbdes(Request $request, $id)
    {
        $this->authorize('anggaran.edit');

        $apbdes = Apbdes::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'kode_rekening' => 'required|string|max:50',
            'nama_rekening' => 'required|string|max:255',
            'jenis' => 'required|in:pendapatan,belanja,pembiayaan',
            'sumber_dana' => 'required|string|max:100',
            'anggaran' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Check if anggaran is being reduced below current realisasi
            if ($request->anggaran < $apbdes->realisasi) {
                return redirect()->back()
                    ->withErrors(['anggaran' => 'Anggaran tidak boleh kurang dari realisasi yang sudah ada (Rp ' . number_format((float)$apbdes->realisasi, 0, ',', '.') . ')'])
                    ->withInput();
            }

            // Update APBDes
            $apbdes->update([
                'kode_rekening' => $request->kode_rekening,
                'nama_rekening' => $request->nama_rekening,
                'jenis' => $request->jenis,
                'sumber_dana' => $request->sumber_dana,
                'anggaran' => $request->anggaran,
                'keterangan' => $request->keterangan,
                'sisa_anggaran' => $request->anggaran - $apbdes->realisasi,
            ]);

            DB::commit();

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun])
                ->with('success', 'Data APBDes "' . $request->nama_rekening . '" berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Delete APBDes
     */
    public function deleteApbdes($id)
    {
        $this->authorize('anggaran.delete');

        $apbdes = Apbdes::findOrFail($id);

        try {
            DB::beginTransaction();

            // Check if there are any expenditures for this APBDes
            if ($apbdes->historiPengeluarans()->count() > 0) {
                return redirect()->back()
                    ->withErrors(['error' => 'Tidak dapat menghapus rekening APBDes yang sudah memiliki histori pengeluaran.']);
            }

            // Check if there are any projects linked to this APBDes
            if ($apbdes->proyekDesas()->count() > 0) {
                return redirect()->back()
                    ->withErrors(['error' => 'Tidak dapat menghapus rekening APBDes yang sudah terhubung dengan proyek desa.']);
            }

            // Delete APBDes
            $apbdes->delete();

            DB::commit();

            return redirect()->route('transparansi-desa.apbdes', ['tahun' => $apbdes->tahun])
                ->with('success', 'Data APBDes "' . $apbdes->nama_rekening . '" berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
