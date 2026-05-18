import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Edit2, Upload, X, Download, Eye, Trash2 } from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function EditExpenditure({ auth, pengeluaran, jenisBuktiOptions = {} }) {
    const apbdes      = pengeluaran.apbdes;
    const [previewFile, setPreviewFile] = useState(null);

    const { data, setData, post, processing, errors } = useForm({
        _method:              'PUT',
        nama_pengeluaran:     pengeluaran.nama_pengeluaran     ?? '',
        jumlah:               pengeluaran.jumlah               ?? '',
        tanggal_pengeluaran:  pengeluaran.tanggal_pengeluaran
            ? new Date(pengeluaran.tanggal_pengeluaran).toISOString().split('T')[0]
            : '',
        keterangan:           pengeluaran.keterangan           ?? '',
        no_bukti:             pengeluaran.no_bukti             ?? '',
        jenis_bukti:          pengeluaran.jenis_bukti          ?? 'kwitansi',
        file_bukti:           null,
        hapus_file:           false,
    });

    const sisaAnggaran = apbdes
        ? Number(apbdes.anggaran) - Number(apbdes.realisasi) + Number(pengeluaran.jumlah)
        : null;

    // POST with _method=PUT for file upload (Laravel method spoofing)
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.update-pengeluaran', pengeluaran.id), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Pengeluaran">
            <Head title="Edit Pengeluaran APBDes" />

            {/* Preview Modal */}
            {previewFile && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" onClick={() => setPreviewFile(null)}>
                    <div className="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onClick={e => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-5 border-b border-gray-100">
                            <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{previewFile.nama}</p>
                            <div className="flex gap-2">
                                <a href={previewFile.url} download target="_blank" rel="noopener noreferrer"
                                    className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-700 transition-all">
                                    <Download className="w-3 h-3" /> Download
                                </a>
                                <button onClick={() => setPreviewFile(null)} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all"><X className="w-4 h-4" /></button>
                            </div>
                        </div>
                        <div className="h-[75vh] overflow-auto">
                            {previewFile.url.match(/\.(jpg|jpeg|png)$/i)
                                ? <img src={previewFile.url} alt="Preview" className="w-full h-full object-contain bg-gray-50 p-4" />
                                : <iframe src={previewFile.url} className="w-full h-full" title="Preview Bukti" />
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
                                <Edit2 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none line-clamp-1">{pengeluaran.nama_pengeluaran}</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">
                                    Edit Pengeluaran · {pengeluaran.no_bukti ?? 'Tanpa No. Bukti'} · {apbdes?.kode_rekening}
                                </p>
                            </div>
                        </div>
                        <Link href={route('anggaran.histori-pengeluaran', apbdes?.id)}
                            className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {/* Main Form */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Seksi 1: Informasi Pengeluaran */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3">Informasi Pengeluaran</h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Nama Pengeluaran *" error={errors.nama_pengeluaran}>
                                        <input type="text" value={data.nama_pengeluaran} onChange={e => setData('nama_pengeluaran', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                    <InputField label="Tanggal *" error={errors.tanggal_pengeluaran}>
                                        <input type="date" value={data.tanggal_pengeluaran} onChange={e => setData('tanggal_pengeluaran', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>

                                <InputField label={`Jumlah (Rp)${sisaAnggaran !== null ? ` — Maks: ${formatRupiah(sisaAnggaran)}` : ''}`} error={errors.jumlah}>
                                    <input type="number" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)}
                                        max={sisaAnggaran ?? undefined} min="0"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>

                                <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                    <textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)}
                                        rows={2} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                                </InputField>
                            </div>

                            {/* Seksi 2: Dokumen Bukti */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3 flex items-center gap-2">
                                    <Upload className="w-4 h-4 text-green-600" /> Dokumen Bukti Pembayaran
                                </h3>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="No. Bukti" error={errors.no_bukti} hint="Kosongkan untuk tidak mengubah">
                                        <input type="text" value={data.no_bukti} onChange={e => setData('no_bukti', e.target.value)}
                                            placeholder={pengeluaran.no_bukti ?? 'BKT-YYYY-XXXX'}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 font-mono" />
                                    </InputField>
                                    <InputField label="Jenis Bukti" error={errors.jenis_bukti}>
                                        <select value={data.jenis_bukti} onChange={e => setData('jenis_bukti', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none">
                                            {Object.entries(jenisBuktiOptions).map(([v, l]) => <option key={v} value={v}>{l}</option>)}
                                        </select>
                                    </InputField>
                                </div>

                                {/* File saat ini */}
                                {pengeluaran.file_bukti_url && !data.hapus_file && (
                                    <div className="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                                                <Eye className="w-4 h-4 text-blue-600" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-black text-blue-900">File Saat Ini</p>
                                                <p className="text-[9px] font-bold text-blue-500 uppercase tracking-widest truncate max-w-[200px]">{pengeluaran.nama_file_bukti}</p>
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <button type="button"
                                                onClick={() => setPreviewFile({ url: pengeluaran.file_bukti_url, nama: pengeluaran.nama_file_bukti })}
                                                className="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-200 transition-all">
                                                LIHAT
                                            </button>
                                            <button type="button"
                                                onClick={() => setData('hapus_file', true)}
                                                className="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-red-100 transition-all flex items-center gap-1">
                                                <Trash2 className="w-3 h-3" /> HAPUS
                                            </button>
                                        </div>
                                    </div>
                                )}
                                {data.hapus_file && (
                                    <div className="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-xl">
                                        <p className="text-[9px] font-black text-red-600 uppercase tracking-widest">File akan dihapus saat disimpan</p>
                                        <button type="button" onClick={() => setData('hapus_file', false)}
                                            className="text-[9px] font-black text-red-500 underline uppercase tracking-widest">BATALKAN</button>
                                    </div>
                                )}

                                <InputField label={pengeluaran.file_bukti_url ? 'Ganti File (PDF/JPG, maks 5MB)' : 'Upload Bukti (PDF/JPG, maks 5MB)'} error={errors.file_bukti}>
                                    <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                                        onChange={e => { setData('file_bukti', e.target.files[0]); setData('hapus_file', false); }}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-green-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" />
                                </InputField>
                            </div>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-4 sticky top-6 self-start">
                            {apbdes && (
                                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rekening Terkait</p>
                                    <p className="text-xs font-black text-gray-900">[{apbdes.kode_rekening}] {apbdes.nama_rekening}</p>
                                    <div className="flex gap-4 mt-2">
                                        <div>
                                            <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Anggaran</p>
                                            <p className="text-sm font-black text-gray-900">{formatRupiah(apbdes.anggaran)}</p>
                                        </div>
                                        <div>
                                            <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Realisasi</p>
                                            <p className="text-sm font-black text-blue-600">{formatRupiah(apbdes.realisasi)}</p>
                                        </div>
                                    </div>
                                </div>
                            )}
                            <button type="submit" disabled={processing}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN PERUBAHAN'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
