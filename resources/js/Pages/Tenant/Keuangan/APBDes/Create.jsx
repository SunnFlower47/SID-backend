import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BIDANG_LIST, SUB_BIDANG, SUMBER_DANA_LIST, BIDANG_COLOR, BIDANG_MAP } from '@/Constants/keuangan';
import { BarChart3, ArrowLeft, Save, Info, ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

// Panduan kode rekening per Bidang/Jenis
const KODE_PANDUAN = {
    pendapatan: { prefix: '4', contoh: '4.1.1.001', deskripsi: 'Format: 4.x.x.xxx (Pendapatan)' },
    belanja:    { prefix: '5', contoh: '5.1.1.001', deskripsi: 'Format: 5.x.x.xxx (Belanja)' },
    pembiayaan: { prefix: '6', contoh: '6.1.1.001', deskripsi: 'Format: 6.x.x.xxx (Pembiayaan)' },
};

const InputField = ({ label, error, hint, children }) => (
    <div className="space-y-1.5">
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
        {children}
        {hint && !error && <p className="text-[9px] font-bold text-gray-400 ml-1">{hint}</p>}
        {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
    </div>
);

export default function Create({ auth, tahunList = [], currentYear }) {
    const { data, setData, post, processing, errors } = useForm({
        tahun:          currentYear ?? new Date().getFullYear(),
        bidang:         '',
        sub_bidang:     '',
        kegiatan:       '',
        jenis:          'belanja',
        sumber_dana:    '',
        kode_rekening:  '',
        nama_rekening:  '',
        anggaran:       '',
        keterangan:     '',
    });

    const subBidangOptions = data.bidang ? (SUB_BIDANG[data.bidang] ?? []) : [];
    const bidangColor      = BIDANG_COLOR[data.bidang] ?? {};
    const kodePanduan      = KODE_PANDUAN[data.jenis] ?? {};

    const handleBidangChange = (val) => {
        setData(prev => ({ ...prev, bidang: Number(val), sub_bidang: '', kegiatan: '' }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.store-tahunan'), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Rekening APBDes berhasil ditambahkan.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' },
                });
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Rekening APBDes">
            <Head title="Tambah Rekening APBDes" />

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
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Tambah Rekening APBDes</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Sesuai Permendagri No. 20 Tahun 2018</p>
                            </div>
                        </div>
                        <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* ── Main Form ─────────────────────── */}
                        <div className="lg:col-span-2 space-y-6">

                            {/* Seksi 1: Klasifikasi APBDes */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3 flex items-center gap-2">
                                    <span className="w-5 h-5 bg-green-600 text-white rounded-lg flex items-center justify-center text-[9px] font-black shrink-0">1</span>
                                    Klasifikasi APBDes (Permendagri 20/2018)
                                </h3>

                                {/* Bidang */}
                                <InputField label="Bidang *" error={errors.bidang} hint="5 Bidang sesuai Permendagri No. 20 Tahun 2018">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        {BIDANG_LIST.map(b => {
                                            const cfg = BIDANG_COLOR[b.value];
                                            const isSelected = Number(data.bidang) === b.value;
                                            return (
                                                <button
                                                    key={b.value}
                                                    type="button"
                                                    onClick={() => handleBidangChange(b.value)}
                                                    className={cn(
                                                        'text-left p-3 rounded-xl border-2 transition-all text-[9px] font-black uppercase tracking-wider leading-tight',
                                                        isSelected
                                                            ? `${cfg.border} ${cfg.bg} ${cfg.text} shadow-sm`
                                                            : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200'
                                                    )}
                                                >
                                                    <span className={cn('block text-[8px] mb-0.5 font-black', isSelected ? cfg.text : 'text-gray-400')}>Bidang {b.value}</span>
                                                    {BIDANG_MAP[b.value]}
                                                </button>
                                            );
                                        })}
                                    </div>
                                </InputField>

                                {/* Sub-Bidang — muncul setelah Bidang dipilih */}
                                {data.bidang && (
                                    <div className="animate-in slide-in-from-top-2 duration-200">
                                        <InputField label="Sub-Bidang" error={errors.sub_bidang} hint="Opsional — pilih sub-bidang sesuai kegiatan">
                                            <select
                                                value={data.sub_bidang}
                                                onChange={e => setData('sub_bidang', e.target.value)}
                                                className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                            >
                                                <option value="">-- Pilih Sub-Bidang (Opsional) --</option>
                                                {subBidangOptions.map(sb => (
                                                    <option key={sb.value} value={sb.value}>{sb.label}</option>
                                                ))}
                                            </select>
                                        </InputField>
                                    </div>
                                )}

                                {/* Nama Kegiatan */}
                                <InputField label="Nama Kegiatan" error={errors.kegiatan} hint="Opsional — spesifikasi kegiatan dalam sub-bidang">
                                    <input
                                        type="text"
                                        value={data.kegiatan}
                                        onChange={e => setData('kegiatan', e.target.value)}
                                        placeholder="Contoh: Penyediaan Penghasilan Tetap Perangkat Desa"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500"
                                    />
                                </InputField>
                            </div>

                            {/* Seksi 2: Detail Rekening */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3 flex items-center gap-2">
                                    <span className="w-5 h-5 bg-green-600 text-white rounded-lg flex items-center justify-center text-[9px] font-black shrink-0">2</span>
                                    Detail Rekening & Anggaran
                                </h3>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Tahun Anggaran *" error={errors.tahun}>
                                        <select value={data.tahun} onChange={e => setData('tahun', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none">
                                            {(tahunList.length ? tahunList : [currentYear]).map(t => (
                                                <option key={t} value={t}>{t}</option>
                                            ))}
                                            {!tahunList.includes(currentYear) && (
                                                <option value={currentYear}>{currentYear} (Baru)</option>
                                            )}
                                        </select>
                                    </InputField>

                                    <InputField label="Jenis Rekening *" error={errors.jenis}>
                                        <select value={data.jenis} onChange={e => setData('jenis', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none">
                                            <option value="pendapatan">Pendapatan</option>
                                            <option value="belanja">Belanja</option>
                                            <option value="pembiayaan">Pembiayaan</option>
                                        </select>
                                    </InputField>
                                </div>

                                {/* Sumber Dana — grouped optgroup */}
                                <InputField label="Sumber Dana *" error={errors.sumber_dana}>
                                    <select value={data.sumber_dana} onChange={e => setData('sumber_dana', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                                        <option value="">-- Pilih Sumber Dana --</option>
                                        {SUMBER_DANA_LIST.map(group => (
                                            <optgroup key={group.group} label={group.group}>
                                                {group.options.map(opt => (
                                                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                                                ))}
                                            </optgroup>
                                        ))}
                                    </select>
                                </InputField>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Kode Rekening *" error={errors.kode_rekening} hint={kodePanduan.deskripsi}>
                                        <input type="text" value={data.kode_rekening}
                                            onChange={e => setData('kode_rekening', e.target.value)}
                                            placeholder={kodePanduan.contoh ?? '5.1.1.001'}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 font-mono" />
                                    </InputField>

                                    <InputField label="Jumlah Anggaran (Rp) *" error={errors.anggaran}>
                                        <input type="number" value={data.anggaran}
                                            onChange={e => setData('anggaran', e.target.value)}
                                            placeholder="0" min="0"
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>

                                <InputField label="Nama / Uraian Rekening *" error={errors.nama_rekening}>
                                    <input type="text" value={data.nama_rekening}
                                        onChange={e => setData('nama_rekening', e.target.value)}
                                        placeholder="Contoh: Belanja Pegawai"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>

                                <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                    <textarea value={data.keterangan} onChange={e => setData('keterangan', e.target.value)}
                                        rows={2} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                                </InputField>
                            </div>
                        </div>

                        {/* ── Sidebar ────────────────────────── */}
                        <div className="space-y-4 sticky top-6 self-start">
                            {/* Preview Klasifikasi */}
                            {data.bidang && (
                                <div className={cn('rounded-2xl border p-5 space-y-3 animate-in fade-in duration-300', bidangColor.border ?? 'border-gray-100', bidangColor.bg ?? 'bg-gray-50')}>
                                    <p className={cn('text-[9px] font-black uppercase tracking-widest', bidangColor.text ?? 'text-gray-500')}>Klasifikasi Terpilih</p>
                                    <div className="space-y-1.5">
                                        <p className="text-xs font-black text-gray-900">Bidang {data.bidang}</p>
                                        <p className="text-[10px] font-bold text-gray-600">{BIDANG_LIST.find(b => b.value === Number(data.bidang))?.label}</p>
                                        {data.sub_bidang && <p className="text-[9px] font-bold text-gray-500">{data.sub_bidang} — {subBidangOptions.find(s => s.value === data.sub_bidang)?.label?.split('—')[1]?.trim()}</p>}
                                        {data.kegiatan && <p className="text-[9px] font-bold text-gray-500 italic">{data.kegiatan}</p>}
                                    </div>
                                </div>
                            )}

                            {/* Panduan */}
                            <div className="bg-green-50 rounded-2xl border border-green-100 p-5">
                                <div className="flex items-center gap-2 mb-3">
                                    <Info className="w-4 h-4 text-green-600 shrink-0" />
                                    <h3 className="text-xs font-black text-green-800 uppercase italic tracking-tighter">Panduan</h3>
                                </div>
                                <ul className="space-y-1.5 text-[9px] font-bold text-green-700 uppercase tracking-wider leading-relaxed">
                                    <li>• Bidang wajib diisi sesuai struktur APBDes</li>
                                    <li>• Kode rekening Pendapatan: 4.x.x.xxx</li>
                                    <li>• Kode rekening Belanja: 5.x.x.xxx</li>
                                    <li>• Kode rekening Pembiayaan: 6.x.x.xxx</li>
                                    <li>• ADD berasal dari APBD Kabupaten, bukan dari Pusat</li>
                                    <li>• Rekening baru otomatis berstatus Disetujui</li>
                                </ul>
                            </div>

                            <button type="submit" disabled={processing}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN REKENING'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
