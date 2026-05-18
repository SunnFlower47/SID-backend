import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import { Building2, ArrowLeft, Save, Info, AlertTriangle, Calendar } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

const JENIS_OPTIONS = [
    { value: 'infrastruktur', label: 'Infrastruktur' },
    { value: 'sosial',        label: 'Sosial'        },
    { value: 'ekonomi',       label: 'Ekonomi'       },
    { value: 'lingkungan',    label: 'Lingkungan'    },
    { value: 'lainnya',       label: 'Lainnya'       },
];

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

const InputField = ({ label, error, children, className = '' }) => (
    <div className={cn('space-y-1.5', className)}>
        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">{label}</label>
        {children}
        {error && <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider italic ml-1">{error}</p>}
    </div>
);

export default function Create({ auth, tahunList = [], currentYear, apbdesList = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        nama_proyek:       '',
        deskripsi:         '',
        jenis:             'infrastruktur',
        anggaran:          '',
        tanggal_mulai:     '',
        tanggal_selesai:   '',
        lokasi:            '',
        penanggung_jawab:  '',
        kontraktor:        '',
        tahun_anggaran:    currentYear,
        apbdes_id:         '',
    });

    const selectedApbdes = apbdesList.find(a => String(a.id) === String(data.apbdes_id));
    const sisaAnggaran   = selectedApbdes ? Number(selectedApbdes.sisa_anggaran) : null;
    const exceedsBudget  = sisaAnggaran !== null && Number(data.anggaran) > sisaAnggaran;

    const handleSubmit = (e) => {
        e.preventDefault();
        if (exceedsBudget) {
            Swal.fire({ icon: 'error', title: 'Anggaran Melebihi Sisa', text: `Anggaran proyek tidak boleh melebihi sisa APBDes (${formatRupiah(sisaAnggaran)})`, customClass: { popup: 'rounded-3xl' } });
            return;
        }
        post(route('anggaran.store-proyek'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Proyek Desa">
            <Head title="Tambah Proyek Desa" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Building2 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Tambah Proyek Desa</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Daftarkan Proyek Pembangunan Baru</p>
                            </div>
                        </div>
                        <Link href={route('transparansi-desa.proyek')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                        </Link>
                    </div>
                </div>

                {apbdesList.length === 0 && (
                    <div className="bg-yellow-50 border border-yellow-100 rounded-2xl p-5 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-yellow-700 uppercase tracking-tighter italic">Tidak Ada Rekening APBDes Belanja Tersedia</p>
                            <p className="text-[10px] font-bold text-yellow-600 uppercase tracking-wider mt-0.5">
                                Semua rekening belanja sudah habis atau belum ada. Silakan tambah rekening APBDes jenis belanja terlebih dahulu.
                            </p>
                            <Link href={route('anggaran.create-tahunan')} className="inline-flex items-center gap-2 mt-3 text-[9px] font-black text-yellow-800 uppercase tracking-widest underline">
                                Tambah Rekening APBDes →
                            </Link>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {/* Main Form */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Section 1: Informasi Dasar */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3">Informasi Proyek</h3>
                                <InputField label="Nama Proyek" error={errors.nama_proyek}>
                                    <input type="text" value={data.nama_proyek} onChange={e => setData('nama_proyek', e.target.value)}
                                        placeholder="Contoh: Pembangunan Jalan Desa RT 03" className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                </InputField>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Jenis Proyek" error={errors.jenis}>
                                        <select value={data.jenis} onChange={e => setData('jenis', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                                            {JENIS_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                                        </select>
                                    </InputField>
                                    <InputField label="Lokasi" error={errors.lokasi}>
                                        <input type="text" value={data.lokasi} onChange={e => setData('lokasi', e.target.value)}
                                            placeholder="Lokasi pelaksanaan proyek" className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>

                                <InputField label="Deskripsi (Opsional)" error={errors.deskripsi}>
                                    <textarea value={data.deskripsi} onChange={e => setData('deskripsi', e.target.value)}
                                        rows={3} placeholder="Uraian singkat tentang proyek ini..." className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none" />
                                </InputField>
                            </div>

                            {/* Section 2: Waktu & PIC */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3">Jadwal & Penanggung Jawab</h3>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Tanggal Mulai" error={errors.tanggal_mulai}>
                                        <input type="date" value={data.tanggal_mulai} onChange={e => setData('tanggal_mulai', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                    <InputField label="Tanggal Selesai (Target)" error={errors.tanggal_selesai}>
                                        <input type="date" value={data.tanggal_selesai} onChange={e => setData('tanggal_selesai', e.target.value)}
                                            min={data.tanggal_mulai} className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Penanggung Jawab" error={errors.penanggung_jawab}>
                                        <input type="text" value={data.penanggung_jawab} onChange={e => setData('penanggung_jawab', e.target.value)}
                                            placeholder="Nama PIC / koordinator" className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                    <InputField label="Kontraktor (Opsional)" error={errors.kontraktor}>
                                        <input type="text" value={data.kontraktor} onChange={e => setData('kontraktor', e.target.value)}
                                            placeholder="Nama perusahaan / CV" className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500" />
                                    </InputField>
                                </div>
                            </div>

                            {/* Section 3: Anggaran & APBDes */}
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-5">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter border-b border-gray-100 pb-3">Anggaran & Sumber Dana</h3>

                                <InputField label="Rekening APBDes (Belanja)" error={errors.apbdes_id}>
                                    <select value={data.apbdes_id} onChange={e => setData('apbdes_id', e.target.value)}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
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
                                        <p className="text-[9px] font-black text-blue-500 uppercase tracking-widest">Sisa Anggaran Tersedia</p>
                                        <p className="text-2xl font-black text-blue-900">{formatRupiah(sisaAnggaran)}</p>
                                        <AnggaranProgressBar anggaran={Number(selectedApbdes.anggaran)} realisasi={Number(selectedApbdes.realisasi)} height="h-1.5" showLabels={false} />
                                    </div>
                                )}

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <InputField label="Anggaran Proyek (Rp)" error={errors.anggaran}>
                                        <input type="number" value={data.anggaran} onChange={e => setData('anggaran', e.target.value)}
                                            max={sisaAnggaran ?? undefined} min="0"
                                            className={cn("w-full border rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500",
                                                exceedsBudget ? 'border-red-300 bg-red-50' : 'border-gray-200')} />
                                        {exceedsBudget && (
                                            <div className="flex items-center gap-1.5 mt-1.5">
                                                <AlertTriangle className="w-3 h-3 text-red-500" />
                                                <p className="text-[9px] font-black text-red-500 uppercase tracking-wider">Melebihi sisa anggaran APBDes ({formatRupiah(sisaAnggaran)})</p>
                                            </div>
                                        )}
                                    </InputField>
                                    <InputField label="Tahun Anggaran" error={errors.tahun_anggaran}>
                                        <select value={data.tahun_anggaran} onChange={e => setData('tahun_anggaran', e.target.value)}
                                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer">
                                            {(tahunList.length ? tahunList : [currentYear]).map(t => <option key={t} value={t}>{t}</option>)}
                                        </select>
                                    </InputField>
                                </div>
                            </div>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-4 sticky top-6 self-start">
                            <div className="bg-green-50 rounded-2xl border border-green-100 p-6">
                                <div className="flex items-center gap-3 mb-4">
                                    <div className="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                        <Info className="w-4 h-4 text-green-600" />
                                    </div>
                                    <h3 className="text-xs font-black text-green-800 uppercase italic tracking-tighter">Info Penting</h3>
                                </div>
                                <ul className="space-y-2 text-[9px] font-bold text-green-700 uppercase tracking-wider leading-relaxed">
                                    <li>• Proyek baru otomatis berstatus <b>Perencanaan</b></li>
                                    <li>• Anggaran proyek akan dikurangi dari rekening APBDes terpilih</li>
                                    <li>• Update realisasi bisa dilakukan di halaman Daftar Proyek</li>
                                    <li>• Proyek 100% realisasi akan otomatis berubah menjadi <b>Selesai</b></li>
                                </ul>
                            </div>

                            <button type="submit" disabled={processing || exceedsBudget || apbdesList.length === 0}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'BUAT PROYEK'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
