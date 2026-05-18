import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Calendar, Wallet, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;
const formatDate   = (d) => d ? new Date(d).toISOString().split('T')[0] : '';

const InputField = ({ label, error, children }) => (
    <div className="space-y-1.5">
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
        {children}
        {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
    </div>
);

export default function AddExpenditure({ auth, apbdesList = [], tahunList = [], tahun, jenis }) {
    const { data, setData, post, processing, errors } = useForm({
        apbdes_id:           '',
        nama_pengeluaran:    '',
        jumlah:              '',
        tanggal_pengeluaran: new Date().toISOString().split('T')[0],
        keterangan:          '',
    });

    const selectedApbdes = apbdesList.find(a => String(a.id) === String(data.apbdes_id));
    const sisaAnggaran   = selectedApbdes ? Number(selectedApbdes.sisa_anggaran) : null;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.store-pengeluaran'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Pengeluaran">
            <Head title="Tambah Pengeluaran APBDes" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Wallet className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Catat Pengeluaran</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Realisasi Anggaran Rekening APBDes</p>
                            </div>
                        </div>
                        <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                {apbdesList.length === 0 && (
                    <div className="bg-yellow-50 border border-yellow-100 rounded-2xl p-5 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-yellow-700 uppercase tracking-tighter italic">Tidak Ada Rekening Tersedia</p>
                            <p className="text-[10px] font-bold text-yellow-600 uppercase tracking-wider mt-0.5">
                                Tidak ada rekening {jenis} tahun {tahun} dengan sisa anggaran. Silakan tambah rekening atau pilih tahun/jenis lain.
                            </p>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                            <InputField label="Rekening APBDes" error={errors.apbdes_id}>
                                <select
                                    value={data.apbdes_id}
                                    onChange={e => setData('apbdes_id', e.target.value)}
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                >
                                    <option value="">-- Pilih Rekening --</option>
                                    {apbdesList.map(a => (
                                        <option key={a.id} value={a.id}>
                                            [{a.kode_rekening}] {a.nama_rekening} — Sisa: {formatRupiah(a.sisa_anggaran)}
                                        </option>
                                    ))}
                                </select>
                            </InputField>

                            {selectedApbdes && (
                                <div className="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-2">
                                    <p className="text-[9px] font-black text-blue-500 uppercase tracking-widest">Info Rekening Terpilih</p>
                                    <div className="flex gap-6">
                                        <div>
                                            <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Anggaran</p>
                                            <p className="text-sm font-black text-blue-900">{formatRupiah(selectedApbdes.anggaran)}</p>
                                        </div>
                                        <div>
                                            <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Realisasi</p>
                                            <p className="text-sm font-black text-blue-600">{formatRupiah(selectedApbdes.realisasi)}</p>
                                        </div>
                                        <div>
                                            <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Sisa</p>
                                            <p className="text-sm font-black text-green-700">{formatRupiah(selectedApbdes.sisa_anggaran)}</p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Nama Pengeluaran" error={errors.nama_pengeluaran}>
                                    <input type="text" value={data.nama_pengeluaran} onChange={e => setData('nama_pengeluaran', e.target.value)}
                                        placeholder="Nama / deskripsi pengeluaran" className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>
                                <InputField label="Tanggal Pengeluaran" error={errors.tanggal_pengeluaran}>
                                    <input type="date" value={data.tanggal_pengeluaran} onChange={e => setData('tanggal_pengeluaran', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>
                            </div>

                            <InputField label={`Jumlah (Rp)${sisaAnggaran !== null ? ` — Maks: ${formatRupiah(sisaAnggaran)}` : ''}`} error={errors.jumlah}>
                                <input type="number" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)}
                                    max={sisaAnggaran ?? undefined} min="0" placeholder="0"
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                            </InputField>

                            <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                <textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)}
                                    rows={3} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                            </InputField>
                        </div>

                        <div className="sticky top-6 self-start">
                            <button type="submit" disabled={processing || apbdesList.length === 0}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN PENGELUARAN'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
