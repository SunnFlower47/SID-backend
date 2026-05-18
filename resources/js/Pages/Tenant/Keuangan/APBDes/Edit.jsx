import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import { BIDANG_LIST, BIDANG_COLOR, BIDANG_MAP, SUB_BIDANG, SUMBER_DANA_LIST } from '@/Constants/keuangan';
import { BarChart3, ArrowLeft, Save, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

const InputField = ({ label, error, children }) => (
    <div className="space-y-1.5">
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
        {children}
        {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
    </div>
);

export default function Edit({ auth, apbdes }) {
    const { data, setData, put, processing, errors } = useForm({
        bidang:        apbdes.bidang        ?? '',
        sub_bidang:    apbdes.sub_bidang    ?? '',
        kegiatan:      apbdes.kegiatan      ?? '',
        kode_rekening: apbdes.kode_rekening ?? '',
        nama_rekening: apbdes.nama_rekening ?? '',
        jenis:         apbdes.jenis         ?? 'pendapatan',
        sumber_dana:   apbdes.sumber_dana   ?? '',
        anggaran:      apbdes.anggaran      ?? '',
        keterangan:    apbdes.keterangan    ?? '',
    });

    const subBidangOptions = data.bidang ? (SUB_BIDANG[data.bidang] ?? []) : [];
    const bidangColor      = BIDANG_COLOR[data.bidang] ?? {};

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('anggaran.update-apbdes', apbdes.id));
    };

    const isBelowRealisasi = Number(data.anggaran) < Number(apbdes.realisasi);

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Rekening APBDes">
            <Head title="Edit Rekening APBDes" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <BarChart3 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none line-clamp-1">{apbdes.nama_rekening}</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Edit Rekening APBDes · {apbdes.kode_rekening}</p>
                            </div>
                        </div>
                        <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                {/* Budget Warning */}
                {isBelowRealisasi && (
                    <div className="bg-red-50 border border-red-100 rounded-2xl p-4 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-red-700 uppercase tracking-tighter italic">Peringatan: Anggaran Lebih Kecil dari Realisasi</p>
                            <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5">
                                Anggaran tidak boleh kurang dari realisasi yang sudah ada ({formatRupiah(apbdes.realisasi)})
                            </p>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Main Form */}
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                            {/* Bidang Selector */}
                            <div className="space-y-3">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Bidang (Permendagri 20/2018)</label>
                                <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    {BIDANG_LIST.map(b => {
                                        const cfg = BIDANG_COLOR[b.value];
                                        const isSelected = Number(data.bidang) === b.value;
                                        return (
                                            <button key={b.value} type="button"
                                                onClick={() => setData(prev => ({ ...prev, bidang: b.value, sub_bidang: '' }))}
                                                className={cn(
                                                    'text-left p-3 rounded-xl border-2 transition-all text-[9px] font-black uppercase tracking-wider leading-tight',
                                                    isSelected ? `${cfg.border} ${cfg.bg} ${cfg.text} shadow-sm` : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200'
                                                )}>
                                                <span className={cn('block text-[8px] mb-0.5 font-black', isSelected ? cfg.text : 'text-gray-400')}>Bidang {b.value}</span>
                                                {BIDANG_MAP[b.value]}
                                            </button>
                                        );
                                    })}
                                </div>
                                {errors.bidang && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{errors.bidang}</p>}
                            </div>

                            {/* Sub-Bidang */}
                            {data.bidang && (
                                <div className="space-y-1.5 animate-in slide-in-from-top-1 duration-200">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sub-Bidang</label>
                                    <select value={data.sub_bidang} onChange={e => setData('sub_bidang', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none">
                                        <option value="">-- Pilih Sub-Bidang (Opsional) --</option>
                                        {subBidangOptions.map(sb => <option key={sb.value} value={sb.value}>{sb.label}</option>)}
                                    </select>
                                </div>
                            )}

                            {/* Kegiatan */}
                            <div className="space-y-1.5">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Kegiatan</label>
                                <input type="text" value={data.kegiatan} onChange={e => setData('kegiatan', e.target.value)}
                                    placeholder="Opsional — nama spesifik kegiatan"
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Jenis Rekening" error={errors.jenis}>
                                    <select
                                        value={data.jenis}
                                        onChange={e => setData('jenis', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        <option value="pendapatan">Pendapatan</option>
                                        <option value="belanja">Belanja</option>
                                        <option value="pembiayaan">Pembiayaan</option>
                                    </select>
                                </InputField>

                                <div className="space-y-1.5">
                                    <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sumber Dana</label>
                                    <select value={data.sumber_dana} onChange={e => setData('sumber_dana', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                                        <option value="">-- Pilih Sumber Dana --</option>
                                        {SUMBER_DANA_LIST.map(group => (
                                            <optgroup key={group.group} label={group.group}>
                                                {group.options.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                                            </optgroup>
                                        ))}
                                    </select>
                                    {errors.sumber_dana && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{errors.sumber_dana}</p>}
                                </div>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Kode Rekening" error={errors.kode_rekening}>
                                    <input type="text" value={data.kode_rekening} onChange={e => setData('kode_rekening', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 font-mono" />
                                </InputField>

                                <InputField label="Jumlah Anggaran (Rp)" error={errors.anggaran}>
                                    <input type="number" value={data.anggaran} onChange={e => setData('anggaran', e.target.value)}
                                        min={apbdes.realisasi} className={cn("w-full border rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500", isBelowRealisasi ? 'border-red-300 bg-red-50' : 'border-gray-200')} />
                                </InputField>
                            </div>

                            <InputField label="Nama Rekening" error={errors.nama_rekening}>
                                <input type="text" value={data.nama_rekening} onChange={e => setData('nama_rekening', e.target.value)}
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                            </InputField>

                            <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                <textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)}
                                    rows={3} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                            </InputField>
                        </div>

                        {/* Sidebar: Current Status */}
                        <div className="space-y-4 sticky top-6 self-start">
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Status Saat Ini</h3>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between py-2 border-b border-gray-50">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun</span>
                                        <span className="text-xs font-black text-gray-900">{apbdes.tahun}</span>
                                    </div>
                                    <div className="flex justify-between py-2 border-b border-gray-50">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Realisasi</span>
                                        <span className="text-xs font-black text-blue-600">{formatRupiah(apbdes.realisasi)}</span>
                                    </div>
                                    <div className="flex justify-between py-2">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sisa</span>
                                        <span className="text-xs font-black text-green-600">{formatRupiah(apbdes.sisa_anggaran)}</span>
                                    </div>
                                </div>
                                <AnggaranProgressBar anggaran={Number(apbdes.anggaran)} realisasi={Number(apbdes.realisasi)} height="h-2" />
                            </div>

                            <button
                                type="submit"
                                disabled={processing || isBelowRealisasi}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
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
