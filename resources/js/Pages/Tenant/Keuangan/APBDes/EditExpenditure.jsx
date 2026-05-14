import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Edit2 } from 'lucide-react';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function EditExpenditure({ auth, pengeluaran }) {
    const apbdes = pengeluaran.apbdes;

    const { data, setData, put, processing, errors } = useForm({
        nama_pengeluaran:    pengeluaran.nama_pengeluaran ?? '',
        jumlah:              pengeluaran.jumlah ?? '',
        tanggal_pengeluaran: pengeluaran.tanggal_pengeluaran
            ? new Date(pengeluaran.tanggal_pengeluaran).toISOString().split('T')[0]
            : '',
        keterangan:          pengeluaran.keterangan ?? '',
    });

    const sisaAnggaran = apbdes
        ? Number(apbdes.anggaran) - Number(apbdes.realisasi) + Number(pengeluaran.jumlah)
        : null;

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('anggaran.update-pengeluaran', pengeluaran.id));
    };

    const InputField = ({ label, error, children }) => (
        <div className="space-y-1.5">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
            {children}
            {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
        </div>
    );

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Pengeluaran">
            <Head title="Edit Pengeluaran APBDes" />

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
                                    Edit Pengeluaran · {apbdes?.kode_rekening} – {apbdes?.nama_rekening}
                                </p>
                            </div>
                        </div>
                        <Link href={route('anggaran.histori-pengeluaran', apbdes?.id)} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Nama Pengeluaran" error={errors.nama_pengeluaran}>
                                    <input type="text" value={data.nama_pengeluaran} onChange={e => setData('nama_pengeluaran', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>
                                <InputField label="Tanggal" error={errors.tanggal_pengeluaran}>
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
                                    rows={3} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                            </InputField>
                        </div>

                        <div className="space-y-4">
                            {apbdes && (
                                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-2">
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
