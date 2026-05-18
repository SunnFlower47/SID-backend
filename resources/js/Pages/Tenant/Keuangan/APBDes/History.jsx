import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import { History, ArrowLeft, Plus, Edit2, Trash2, Save, X, Calendar, FileText, Upload, Eye, Download, CheckCircle, Clock, XCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;
const formatDate   = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

const JENIS_CONFIG = {
    pendapatan: { color: 'text-emerald-700', bg: 'bg-emerald-50' },
    belanja:    { color: 'text-blue-700',    bg: 'bg-blue-50'    },
    pembiayaan: { color: 'text-purple-700',  bg: 'bg-purple-50'  },
};

const SPJ_CONFIG = {
    belum: { icon: Clock,         color: 'text-yellow-600', bg: 'bg-yellow-50', label: 'Belum SPJ' },
    sudah: { icon: CheckCircle,   color: 'text-green-600',  bg: 'bg-green-50',  label: 'Sudah SPJ' },
};

export default function HistoryPage({ auth, apbdes, jenisBuktiOptions = {} }) {
    const [showAddForm, setShowAddForm] = useState(false);
    const [previewFile, setPreviewFile] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        apbdes_id:            apbdes.id,
        nama_pengeluaran:     '',
        jumlah:               '',
        tanggal_pengeluaran:  new Date().toISOString().split('T')[0],
        keterangan:           '',
        no_bukti:             '',
        jenis_bukti:          'kwitansi',
        file_bukti:           null,
    });

    const sisaAnggaran = Number(apbdes.anggaran) - Number(apbdes.realisasi);

    const handleAdd = (e) => {
        e.preventDefault();
        post(route('anggaran.store-pengeluaran'), {
            forceFormData: true, // wajib untuk multipart/form-data (file upload)
            onSuccess: () => { reset(); setShowAddForm(false); },
        });
    };

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'HAPUS PENGELUARAN?',
            html: `Hapus <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">File bukti akan ikut dihapus</small>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!', cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px]', cancelButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px] text-gray-500' },
        }).then(r => { if (r.isConfirmed) router.delete(route('anggaran.delete-pengeluaran', id), { preserveScroll: true }); });
    };

    const jenisCfg = JENIS_CONFIG[apbdes.jenis] ?? JENIS_CONFIG.belanja;

    const InputField = ({ label, error, children }) => (
        <div className="space-y-1.5">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
            {children}
            {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
        </div>
    );

    return (
        <AuthenticatedLayout user={auth.user} title="Histori Pengeluaran">
            <Head title="Histori Pengeluaran APBDes" />

            {/* Preview File Modal */}
            {previewFile && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" onClick={() => setPreviewFile(null)}>
                    <div className="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onClick={e => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-5 border-b border-gray-100">
                            <div>
                                <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{previewFile.nama}</p>
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{previewFile.noBukti}</p>
                            </div>
                            <div className="flex gap-2">
                                <a href={previewFile.url} download target="_blank" rel="noopener noreferrer"
                                    className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-700 transition-all">
                                    <Download className="w-3 h-3" /> Download
                                </a>
                                <button onClick={() => setPreviewFile(null)} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div className="h-[75vh] overflow-auto">
                            {previewFile.url.endsWith('.pdf')
                                ? <iframe src={previewFile.url} className="w-full h-full" title="Preview Bukti" />
                                : <img src={previewFile.url} alt="Preview Bukti" className="w-full h-full object-contain bg-gray-50 p-4" />
                            }
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <History className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none line-clamp-1">{apbdes.nama_rekening}</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Histori Pengeluaran · {apbdes.kode_rekening} · {apbdes.tahun}</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <button onClick={() => setShowAddForm(!showAddForm)}
                                className="flex items-center px-4 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg transition-all hover:scale-105 uppercase tracking-widest">
                                <Plus className="w-3.5 h-3.5 mr-2" /> TAMBAH PENGELUARAN
                            </button>
                            <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Status Card + Add Form */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                        <div className="flex items-center justify-between">
                            <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Status Rekening</h3>
                            <span className={cn('px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest', jenisCfg.bg, jenisCfg.color)}>{apbdes.jenis}</span>
                        </div>
                        <div className="space-y-2">
                            {[
                                { label: 'Anggaran', value: formatRupiah(apbdes.anggaran), color: 'text-gray-900' },
                                { label: 'Realisasi', value: formatRupiah(apbdes.realisasi), color: 'text-blue-600' },
                                { label: 'Sisa Anggaran', value: formatRupiah(sisaAnggaran), color: sisaAnggaran >= 0 ? 'text-green-600' : 'text-red-600' },
                            ].map(r => (
                                <div key={r.label} className="flex justify-between py-2 border-b border-gray-50 last:border-0">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{r.label}</span>
                                    <span className={cn('text-xs font-black', r.color)}>{r.value}</span>
                                </div>
                            ))}
                        </div>
                        <AnggaranProgressBar anggaran={Number(apbdes.anggaran)} realisasi={Number(apbdes.realisasi)} height="h-2" showLabels={false} />
                        <p className="text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">
                            {apbdes.anggaran > 0 ? Math.min(100, Math.round((apbdes.realisasi / apbdes.anggaran) * 100)) : 0}% Terserap
                        </p>
                    </div>

                    {/* Add Form */}
                    {showAddForm && (
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-green-100 shadow-sm p-6 space-y-4 animate-in slide-in-from-top-3 duration-300">
                            <div className="flex items-center justify-between mb-1">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Tambah Pengeluaran</h3>
                                <button onClick={() => { setShowAddForm(false); reset(); }} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                            <form onSubmit={handleAdd} className="space-y-4" encType="multipart/form-data">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <InputField label="Nama Pengeluaran *" error={errors.nama_pengeluaran}>
                                        <input type="text" value={data.nama_pengeluaran} onChange={e => setData('nama_pengeluaran', e.target.value)}
                                            placeholder="Contoh: Pembelian material..." className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                    <InputField label="Tanggal Pengeluaran *" error={errors.tanggal_pengeluaran}>
                                        <input type="date" value={data.tanggal_pengeluaran} onChange={e => setData('tanggal_pengeluaran', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>
                                <InputField label={`Jumlah * — Sisa: ${formatRupiah(sisaAnggaran)}`} error={errors.jumlah}>
                                    <input type="number" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)}
                                        max={sisaAnggaran} min="0" placeholder="0"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>

                                {/* ── Bukti Pembayaran ── */}
                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <InputField label="No. Bukti" error={errors.no_bukti}>
                                        <input type="text" value={data.no_bukti} onChange={e => setData('no_bukti', e.target.value)}
                                            placeholder="Auto: BKT-YYYY-XXXX"
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 font-mono" />
                                    </InputField>
                                    <InputField label="Jenis Bukti" error={errors.jenis_bukti}>
                                        <select value={data.jenis_bukti} onChange={e => setData('jenis_bukti', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none">
                                            {Object.entries(jenisBuktiOptions).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                                        </select>
                                    </InputField>
                                    <InputField label="Upload Bukti (PDF/JPG, maks 5MB)" error={errors.file_bukti}>
                                        <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                                            onChange={e => setData('file_bukti', e.target.files[0])}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-green-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" />
                                    </InputField>
                                </div>

                                <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                    <textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)}
                                        rows={2} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                                </InputField>
                                <div className="flex gap-3">
                                    <button type="button" onClick={() => { setShowAddForm(false); reset(); }}
                                        className="flex-1 py-3 rounded-xl bg-gray-50 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-100 border border-gray-200 transition-all">BATAL</button>
                                    <button type="submit" disabled={processing}
                                        className="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 disabled:opacity-50">
                                        <Save className="w-3.5 h-3.5" /> {processing ? 'MENYIMPAN...' : 'SIMPAN'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    )}
                </div>

                {/* History Table */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div className="p-5 sm:p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 className="text-base font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-3">
                            <History className="w-5 h-5 text-green-600" /> Histori Pengeluaran
                        </h3>
                        <span className="px-4 py-1.5 bg-blue-50 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            {apbdes.histori_pengeluarans?.length ?? 0} Transaksi
                        </span>
                    </div>
                    {apbdes.histori_pengeluarans?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-gray-50/80 border-b border-gray-100">
                                        {['No. Bukti', 'Tanggal', 'Nama Pengeluaran', 'Jumlah', 'Jenis Bukti', 'Dokumen', 'SPJ', 'Aksi'].map(h => (
                                            <th key={h} className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {apbdes.histori_pengeluarans.map((item) => {
                                        const spjCfg = SPJ_CONFIG[item.spj_status] ?? SPJ_CONFIG.belum;
                                        const SpjIcon = spjCfg.icon;
                                        return (
                                            <tr key={item.id} className="hover:bg-gray-50/50 transition-all group">
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className="text-[9px] font-black text-gray-500 font-mono uppercase">{item.no_bukti ?? '—'}</span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-2">
                                                        <Calendar className="w-3.5 h-3.5 text-gray-300" />
                                                        <span className="text-xs font-bold text-gray-600">{formatDate(item.tanggal_pengeluaran)}</span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 max-w-[180px]">
                                                    <p className="text-xs font-black text-gray-900 truncate">{item.nama_pengeluaran}</p>
                                                    {item.keterangan && <p className="text-[9px] text-gray-400 font-bold mt-0.5 truncate">{item.keterangan}</p>}
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className="text-sm font-black text-blue-600">{formatRupiah(item.jumlah)}</span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className="px-2 py-0.5 bg-gray-50 border border-gray-100 rounded-lg text-[9px] font-black text-gray-600 uppercase tracking-widest">
                                                        {item.jenis_bukti_label ?? item.jenis_bukti ?? '—'}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    {item.file_bukti_url ? (
                                                        <button
                                                            onClick={() => setPreviewFile({ url: item.file_bukti_url, nama: item.nama_file_bukti, noBukti: item.no_bukti })}
                                                            className="flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 border border-blue-100 rounded-lg text-[9px] font-black text-blue-600 uppercase tracking-widest hover:bg-blue-100 transition-all"
                                                        >
                                                            <Eye className="w-3 h-3" /> LIHAT
                                                        </button>
                                                    ) : (
                                                        <span className="text-[9px] text-gray-300 font-bold uppercase tracking-widest">Tidak ada</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className={cn('flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest', spjCfg.bg, spjCfg.color)}>
                                                        <SpjIcon className="w-2.5 h-2.5" /> {spjCfg.label}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-1">
                                                        <Link href={route('anggaran.edit-pengeluaran', item.id)}
                                                            className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                                            <Edit2 className="w-3.5 h-3.5" />
                                                        </Link>
                                                        <button onClick={() => handleDelete(item.id, item.nama_pengeluaran)}
                                                            className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                                            <Trash2 className="w-3.5 h-3.5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-gray-50/80 border-t border-gray-100">
                                        <td colSpan={3} className="px-4 py-3">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pengeluaran</span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="text-sm font-black text-blue-700">
                                                {formatRupiah(apbdes.histori_pengeluarans.reduce((s, i) => s + Number(i.jumlah), 0))}
                                            </span>
                                        </td>
                                        <td colSpan={4} />
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    ) : (
                        <div className="p-16 text-center">
                            <FileText className="w-12 h-12 text-gray-200 mx-auto mb-4" />
                            <h3 className="text-base font-black text-gray-400 uppercase italic tracking-tighter">Belum Ada Pengeluaran</h3>
                            <p className="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-2 mb-6">Klik "Tambah Pengeluaran" untuk mencatat realisasi</p>
                            <button onClick={() => setShowAddForm(true)} className="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white rounded-2xl text-xs font-black shadow-xl shadow-green-200 hover:bg-green-700 transition-all uppercase tracking-widest">
                                <Plus className="w-4 h-4" /> TAMBAH PENGELUARAN PERTAMA
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
