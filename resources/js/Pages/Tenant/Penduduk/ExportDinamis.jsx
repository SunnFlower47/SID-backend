import React, { useState } from 'react';
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard } from '@/Components/Shared';
import MultiResidentAutocomplete from '@/Components/Shared/MultiResidentAutocomplete';
import { FileSpreadsheet, CheckSquare, Square, Download, Loader2, Users, MapPin, SlidersHorizontal, Filter, X } from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import loadingAnimation from '@/assets/lottie/loading-circle-animation.json';
import successAnimation from '@/assets/lottie/success-animation.json';

const LottieComponent = Lottie?.default || Lottie;

const AVAILABLE_COLUMNS = [
    { id: 'nik',                label: 'NIK' },
    { id: 'nama',               label: 'Nama Lengkap' },
    { id: 'jenis_kelamin',      label: 'Jenis Kelamin' },
    { id: 'tempat_lahir',       label: 'Tempat Lahir' },
    { id: 'tanggal_lahir',      label: 'Tanggal Lahir' },
    { id: 'agama',              label: 'Agama' },
    { id: 'pendidikan',         label: 'Pendidikan' },
    { id: 'pekerjaan',          label: 'Pekerjaan' },
    { id: 'status_perkawinan',  label: 'Status Perkawinan' },
    { id: 'kedudukan_keluarga', label: 'Kedudukan Dlm Keluarga' },
    { id: 'alamat',             label: 'Alamat' },
    { id: 'rt',                 label: 'RT' },
    { id: 'rw',                 label: 'RW' },
    { id: 'dusun',              label: 'Dusun' },
    { id: 'nkk',                label: 'No. KK' },
    { id: 'nama_ayah',          label: 'Nama Ayah' },
    { id: 'nama_ibu',           label: 'Nama Ibu' },
    { id: 'golongan_darah',     label: 'Golongan Darah' },
    { id: 'no_akta_lahir',      label: 'No. Akta Lahir' },
    { id: 'telepon',            label: 'Telepon' },
    { id: 'warganegara',        label: 'Kewarganegaraan' },
    { id: 'membaca_huruf',      label: 'Dapat Membaca Huruf' },
    { id: 'status_asuransi',    label: 'Status Asuransi' },
    { id: 'jenis_cacat',        label: 'Jenis Cacat' },
    { id: 'sakit_menahun',      label: 'Sakit Menahun' },
    { id: 'keterangan',         label: 'Keterangan' },
];

const selectStyle = "w-full px-4 py-3 bg-white border border-gray-200 rounded-2xl text-sm font-medium focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all outline-none disabled:bg-gray-50 disabled:text-gray-400 text-gray-700";

export default function ExportDinamis({ auth, rtList = [], rwList = [], dusunList = [] }) {
    // Column selection
    const [selectedCols, setSelectedCols] = useState(['nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'rt', 'rw', 'dusun']);

    // Filter wilayah
    const [selectedDusun, setSelectedDusun] = useState('');
    const [selectedRw, setSelectedRw]       = useState('');
    const [selectedRt, setSelectedRt]       = useState('');

    // Filter lainnya
    const [jenisKelamin, setJenisKelamin]     = useState('');
    const [statusPerkawinan, setStatusPerkawinan] = useState('');
    const [kategoriUsia, setKategoriUsia]     = useState('');

    // Pencarian per-individu (opsional)
    const [selectedPenduduks, setSelectedPenduduks] = useState([]);

    const [isExporting, setIsExporting] = useState(false);
    const [showSuccess, setShowSuccess] = useState(false);

    // Filter RW berdasarkan dusun yang dipilih
    const filteredRw = selectedDusun
        ? rwList.filter(rw => rw.dusun_id == selectedDusun)
        : rwList;
    const filteredRt = selectedRw
        ? rtList.filter(rt => rt.rw_id == selectedRw)
        : rtList;

    const toggleColumn = (id) => setSelectedCols(prev =>
        prev.includes(id) ? prev.filter(col => col !== id) : [...prev, id]
    );

    const toggleAll = () => {
        if (selectedCols.length === AVAILABLE_COLUMNS.length) {
            setSelectedCols([]);
        } else {
            setSelectedCols(AVAILABLE_COLUMNS.map(c => c.id));
        }
    };

    const handleRemovePenduduk = (id) => {
        setSelectedPenduduks(prev => prev.filter(p => p.id !== id));
    };

    const handleExport = async () => {
        if (selectedCols.length === 0) {
            Swal.fire('Perhatian', 'Pilih setidaknya satu kolom untuk diekspor.', 'warning');
            return;
        }

        setIsExporting(true);
        try {
            const params = {};
            params.columns = selectedCols.join(',');

            // Jika ada penduduk yang dipilih satu-per-satu, kirim id mereka
            if (selectedPenduduks.length > 0) {
                params.penduduk_ids = selectedPenduduks.map(p => p.id).join(',');
            } else {
                // Kalau tidak, gunakan filter wilayah
                if (selectedDusun) params.dusun_id = selectedDusun;
                if (selectedRw)    params.rw_id    = selectedRw;
                if (selectedRt)    params.rt_id    = selectedRt;
                if (jenisKelamin)      params.jenis_kelamin     = jenisKelamin;
                if (statusPerkawinan)  params.status_perkawinan = statusPerkawinan;
                if (kategoriUsia)      params.kategori_usia     = kategoriUsia;
            }

            const response = await axios.get(route('penduduk.export.excel'), {
                params,
                responseType: 'blob'
            });

            const url  = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href  = url;
            link.setAttribute('download', `Data_Penduduk_Custom_${new Date().toLocaleDateString('id-ID')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();

            setShowSuccess(true);
            setTimeout(() => setShowSuccess(false), 3000);
        } catch (error) {
            console.error('Export error:', error);
            Swal.fire('Gagal!', 'Terjadi kesalahan saat mengekspor data.', 'error');
        } finally {
            setIsExporting(false);
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Export Kustom Penduduk" />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Loading Overlay */}
                {isExporting && (
                    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm animate-in fade-in duration-300">
                        <div className="bg-white rounded-3xl p-8 shadow-2xl flex flex-col items-center gap-4 max-w-xs w-full mx-4 animate-in zoom-in-95 duration-300">
                            <div className="w-24 h-24">
                                <LottieComponent animationData={loadingAnimation} loop={true} />
                            </div>
                            <div className="text-center">
                                <h3 className="text-lg font-black text-gray-900">Mengekspor Data</h3>
                                <p className="text-sm text-gray-500 mt-1">Mohon tunggu, file Excel sedang disiapkan...</p>
                            </div>
                        </div>
                    </div>
                )}

                {/* Success Overlay */}
                {showSuccess && (
                    <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/20 backdrop-blur-sm animate-in fade-in duration-300">
                        <div className="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center animate-in zoom-in duration-300">
                            <div className="w-48 h-48">
                                <LottieComponent animationData={successAnimation} loop={false} />
                            </div>
                            <h3 className="text-2xl font-black text-gray-900 mt-4 uppercase italic tracking-tighter">Export Berhasil!</h3>
                            <p className="text-sm text-gray-500 mt-2">File Excel Anda sedang diunduh</p>
                        </div>
                    </div>
                )}

                <PageHeader
                    title="Export Data Kustom"
                    subtitle="Export data penduduk dengan kolom dan filter yang Anda tentukan sendiri."
                    icon={FileSpreadsheet}
                />

                {/* SECTION 1: Filter Penduduk Individual */}
                <FormCard icon={Users} title="Pilih Penduduk Tertentu (Opsional)" className="overflow-visible">
                        <p className="text-sm text-gray-500 mb-4">
                            Cari dan pilih penduduk spesifik yang ingin diekspor. Jika dikosongkan, sistem akan menggunakan filter wilayah di bawah.
                        </p>
                        <MultiResidentAutocomplete
                            label="Cari Nama / NIK"
                            placeholder="Ketik min. 3 huruf untuk mencari..."
                            onSelect={(list) => setSelectedPenduduks(list)}
                        />

                        {selectedPenduduks.length > 0 && (
                            <div className="mt-4 flex flex-wrap gap-2">
                                {selectedPenduduks.map(p => (
                                    <div key={p.id} className="flex items-center gap-2 bg-blue-50 text-blue-800 text-sm font-semibold px-3 py-1.5 rounded-xl border border-blue-200">
                                        <span>{p.nama}</span>
                                        <button onClick={() => handleRemovePenduduk(p.id)} className="hover:text-red-600 transition-colors">
                                            <X className="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}
                </FormCard>

                {/* SECTION 2: Filter Wilayah & Demografi */}
                <FormCard
                        icon={Filter}
                        title="Filter Wilayah & Demografi"
                        actions={
                            selectedPenduduks.length > 0 && (
                                <span className="text-xs font-bold text-amber-600 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                                    Dinonaktifkan (mode pilih individu)
                                </span>
                            )
                        }
                    >
                        <div className={cn("space-y-5", selectedPenduduks.length > 0 && "opacity-40 pointer-events-none select-none")}>
                            {/* Wilayah */}
                            <div>
                                <div className="flex items-center gap-2 mb-3">
                                    <MapPin className="w-4 h-4 text-gray-400" />
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Wilayah (Dusun / RW / RT)</label>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <select value={selectedDusun} onChange={e => { setSelectedDusun(e.target.value); setSelectedRw(''); setSelectedRt(''); }} className={selectStyle}>
                                        <option value="">Semua Dusun</option>
                                        {dusunList.map(d => <option key={d.id} value={d.id}>{d.nama}</option>)}
                                    </select>
                                    <select value={selectedRw} disabled={!selectedDusun} onChange={e => { setSelectedRw(e.target.value); setSelectedRt(''); }} className={selectStyle}>
                                        <option value="">Semua RW</option>
                                        {filteredRw.map(rw => <option key={rw.id} value={rw.id}>{rw.kode}</option>)}
                                    </select>
                                    <select value={selectedRt} disabled={!selectedRw} onChange={e => setSelectedRt(e.target.value)} className={selectStyle}>
                                        <option value="">Semua RT</option>
                                        {filteredRt.map(rt => <option key={rt.id} value={rt.id}>{rt.kode}</option>)}
                                    </select>
                                </div>
                            </div>

                            {/* Demografi */}
                            <div>
                                <div className="flex items-center gap-2 mb-3">
                                    <SlidersHorizontal className="w-4 h-4 text-gray-400" />
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Demografi</label>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <select value={jenisKelamin} onChange={e => setJenisKelamin(e.target.value)} className={selectStyle}>
                                        <option value="">Semua Jenis Kelamin</option>
                                        <option value="LAKI-LAKI">Laki-Laki</option>
                                        <option value="PEREMPUAN">Perempuan</option>
                                    </select>
                                    <select value={statusPerkawinan} onChange={e => setStatusPerkawinan(e.target.value)} className={selectStyle}>
                                        <option value="">Semua Status Perkawinan</option>
                                        <option value="Belum Kawin">Belum Kawin</option>
                                        <option value="Kawin">Kawin</option>
                                        <option value="Cerai Hidup">Cerai Hidup</option>
                                        <option value="Cerai Mati">Cerai Mati</option>
                                    </select>
                                    <select value={kategoriUsia} onChange={e => setKategoriUsia(e.target.value)} className={selectStyle}>
                                        <option value="">Semua Usia</option>
                                        <option value="balita">Balita (0–5 thn)</option>
                                        <option value="anak">Anak (5–12 thn)</option>
                                        <option value="remaja">Remaja (12–18 thn)</option>
                                        <option value="dewasa_muda">Dewasa Muda (18–30 thn)</option>
                                        <option value="dewasa">Dewasa (30–60 thn)</option>
                                        <option value="lansia">Lansia (60+ thn)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                </FormCard>

                {/* SECTION 3: Pilih Kolom */}
                <FormCard
                        icon={FileSpreadsheet}
                        title="Pilih Kolom yang Diekspor"
                        actions={
                            <div className="flex items-center gap-3">
                                <span className="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                                    {selectedCols.length} kolom dipilih
                                </span>
                                <button onClick={toggleAll} className="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                    {selectedCols.length === AVAILABLE_COLUMNS.length
                                        ? <><CheckSquare className="w-4 h-4 text-blue-600" /> Hapus Semua</>
                                        : <><Square className="w-4 h-4 text-gray-400" /> Pilih Semua</>
                                    }
                                </button>
                            </div>
                        }
                    >
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                            {AVAILABLE_COLUMNS.map(col => {
                                const isSelected = selectedCols.includes(col.id);
                                return (
                                    <div
                                        key={col.id}
                                        onClick={() => toggleColumn(col.id)}
                                        className={cn(
                                            "flex items-center gap-2.5 p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-150 select-none",
                                            isSelected
                                                ? "border-blue-500 bg-blue-50/40 shadow-sm"
                                                : "border-gray-100 hover:border-blue-200 hover:bg-gray-50"
                                        )}
                                    >
                                        <div className={cn(
                                            "flex items-center justify-center w-5 h-5 rounded-md transition-colors shrink-0",
                                            isSelected ? "bg-blue-600 text-white" : "border-2 border-gray-300"
                                        )}>
                                            {isSelected && <CheckSquare className="w-3.5 h-3.5" />}
                                        </div>
                                        <span className={cn("text-xs font-semibold leading-tight", isSelected ? "text-blue-900" : "text-gray-700")}>
                                            {col.label}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>

                        {/* Export Button */}
                        <div className="mt-6 flex items-center justify-between pt-6 border-t border-gray-100">
                            <p className="text-sm text-gray-500">
                                {selectedPenduduks.length > 0
                                    ? <><span className="font-bold text-blue-600">{selectedPenduduks.length} penduduk</span> dipilih secara manual</>
                                    : 'Export semua penduduk sesuai filter di atas'
                                }
                            </p>
                            <button
                                onClick={handleExport}
                                disabled={selectedCols.length === 0 || isExporting}
                                className="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed hover:-translate-y-0.5"
                            >
                                {isExporting
                                    ? <><Loader2 className="w-5 h-5 animate-spin" /> Memproses...</>
                                    : <><Download className="w-5 h-5" /> Download Excel</>
                                }
                            </button>
                        </div>
                </FormCard>

            </div>
        </AuthenticatedLayout>
    );
}
