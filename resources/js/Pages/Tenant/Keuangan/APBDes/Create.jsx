import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BarChart3, ArrowLeft, Save, Info } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

const SUMBER_DANA_OPTIONS = [
    { value: 'dana_desa_ad', label: 'Dana Desa - Alokasi Dasar (AD)' },
    { value: 'dana_desa_af', label: 'Dana Desa - Alokasi Formula (AF)' },
    { value: 'dana_desa_ak', label: 'Dana Desa - Alokasi Kinerja (AK)' },
    { value: 'dau',          label: 'Dana Alokasi Umum (DAU)' },
    { value: 'dak',          label: 'Dana Alokasi Khusus (DAK)' },
    { value: 'dbh',          label: 'Dana Bagi Hasil (DBH)' },
    { value: 'did',          label: 'Dana Insentif Daerah (DID)' },
    { value: 'pad',          label: 'Pendapatan Asli Desa (PAD)' },
];

export default function Create({ auth, tahunList = [], currentYear }) {
    const { data, setData, post, processing, errors } = useForm({
        tahun:          currentYear ?? new Date().getFullYear(),
        jenis:          'pendapatan',
        sumber_dana:    '',
        kode_rekening:  '',
        nama_rekening:  '',
        anggaran:       '',
        keterangan:     '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.store-tahunan'), {
            onSuccess: () => {
                Swal.fire({ icon: 'success', title: 'BERHASIL!', text: 'Rekening APBDes berhasil ditambahkan.', timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-3xl' } });
            },
        });
    };

    const InputField = ({ label, error, children }) => (
        <div className="space-y-1.5">
            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
            {children}
            {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
        </div>
    );

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
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Input Anggaran Rekening Baru</p>
                            </div>
                        </div>
                        <Link href={route('transparansi-desa.apbdes')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Main Form */}
                        <div className="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Tahun Anggaran" error={errors.tahun}>
                                    <select
                                        value={data.tahun}
                                        onChange={e => setData('tahun', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        {(tahunList.length ? tahunList : [currentYear]).map(t => (
                                            <option key={t} value={t}>{t}</option>
                                        ))}
                                        {!tahunList.includes(currentYear) && <option value={currentYear}>{currentYear} (Baru)</option>}
                                    </select>
                                </InputField>

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
                            </div>

                            <InputField label="Sumber Dana" error={errors.sumber_dana}>
                                <select
                                    value={data.sumber_dana}
                                    onChange={e => setData('sumber_dana', e.target.value)}
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                >
                                    <option value="">-- Pilih Sumber Dana --</option>
                                    {SUMBER_DANA_OPTIONS.map(opt => (
                                        <option key={opt.value} value={opt.value}>{opt.label}</option>
                                    ))}
                                </select>
                            </InputField>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <InputField label="Kode Rekening" error={errors.kode_rekening}>
                                    <input
                                        type="text"
                                        value={data.kode_rekening}
                                        onChange={e => setData('kode_rekening', e.target.value)}
                                        placeholder="Contoh: 1.1.1"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 font-mono"
                                    />
                                </InputField>

                                <InputField label="Jumlah Anggaran (Rp)" error={errors.anggaran}>
                                    <input
                                        type="number"
                                        value={data.anggaran}
                                        onChange={e => setData('anggaran', e.target.value)}
                                        placeholder="0"
                                        min="0"
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500"
                                    />
                                </InputField>
                            </div>

                            <InputField label="Nama Rekening" error={errors.nama_rekening}>
                                <input
                                    type="text"
                                    value={data.nama_rekening}
                                    onChange={e => setData('nama_rekening', e.target.value)}
                                    placeholder="Contoh: Pendapatan Dana Desa"
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500"
                                />
                            </InputField>

                            <InputField label="Keterangan (Opsional)" error={errors.keterangan}>
                                <textarea
                                    value={data.keterangan}
                                    onChange={e => setData('keterangan', e.target.value)}
                                    rows={3}
                                    placeholder="Catatan tambahan tentang rekening ini..."
                                    className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none"
                                />
                            </InputField>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            <div className="bg-green-50 rounded-2xl border border-green-100 p-6">
                                <div className="flex items-center gap-3 mb-4">
                                    <div className="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                        <Info className="w-4 h-4 text-green-600" />
                                    </div>
                                    <h3 className="text-xs font-black text-green-800 uppercase italic tracking-tighter">Panduan Pengisian</h3>
                                </div>
                                <ul className="space-y-2 text-[10px] font-bold text-green-700 uppercase tracking-widest">
                                    <li>• Kode rekening mengikuti format APBDes (1.x.x untuk pendapatan, 2.x.x untuk belanja)</li>
                                    <li>• Sumber dana menentukan asal pembiayaan anggaran</li>
                                    <li>• Rekening baru otomatis berstatus <b>Disetujui</b></li>
                                    <li>• Realisasi dimulai dari Rp 0</li>
                                </ul>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50"
                            >
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
