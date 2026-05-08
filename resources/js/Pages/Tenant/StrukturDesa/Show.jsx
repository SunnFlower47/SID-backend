import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { User, Shield, MapPin, Calendar, FileText, Phone, Mail, ArrowLeft, Edit2, CheckCircle, XCircle } from 'lucide-react';

export default function Show({ auth, strukturDesa }) {
    const detailRows = [
        { label: 'Nama Lengkap', value: strukturDesa.nama, icon: User, color: 'text-blue-500' },
        { label: 'NIK (KTP)', value: strukturDesa.nik || '-', icon: Shield, color: 'text-indigo-500' },
        { label: 'Nomor Telepon', value: strukturDesa.no_hp || '-', icon: Phone, color: 'text-green-500' },
        { label: 'Alamat Email', value: strukturDesa.email || '-', icon: Mail, color: 'text-red-500' },
        { label: 'Wilayah Tugas', value: `${strukturDesa.dusun_label || 'Pusat'}, RT ${strukturDesa.rt_label}/RW ${strukturDesa.rw_label}`, icon: MapPin, color: 'text-orange-500' },
        { label: 'Masa Jabatan', value: `${strukturDesa.tanggal_pengangkatan ? new Date(strukturDesa.tanggal_pengangkatan).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'} s/d ${strukturDesa.tanggal_berakhir ? new Date(strukturDesa.tanggal_berakhir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Sekarang'}`, icon: Calendar, color: 'text-amber-500' },
    ];

    return (
        <AuthenticatedLayout user={auth.user} title="Profil Perangkat Desa">
            <Head title={`Profil ${strukturDesa.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <Link 
                                href={route('struktur-desa.index')}
                                className="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/20 shadow-inner hover:bg-white/30 transition-all"
                            >
                                <ArrowLeft className="w-5 h-5 text-white" />
                            </Link>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Detail Perangkat</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Informasi Lengkap Struktur Desa</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-2">
                            <Link 
                                href={route('struktur-desa.edit', strukturDesa.id)}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Edit2 className="w-3.5 h-3.5 mr-2" />
                                EDIT PROFIL
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Profil Card */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm text-center relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-4">
                                {strukturDesa.status_aktif ? (
                                    <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest">AKTIF</span>
                                ) : (
                                    <span className="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[9px] font-black uppercase tracking-widest">NONAKTIF</span>
                                )}
                            </div>
                            
                            <div className="w-32 h-44 sm:w-40 sm:h-52 bg-gray-50 rounded-2xl border-4 border-white shadow-xl mx-auto overflow-hidden shrink-0 mb-6">
                                {strukturDesa.foto ? (
                                    <img 
                                        src={`/storage/${strukturDesa.foto}`} 
                                        alt={strukturDesa.nama} 
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black text-2xl uppercase italic">
                                        {strukturDesa.nama.charAt(0)}
                                    </div>
                                )}
                            </div>
                            
                            <h2 className="text-xl font-black text-gray-900 uppercase italic tracking-tight leading-tight mb-1">{strukturDesa.nama}</h2>
                            <p className="text-xs font-bold text-green-600 uppercase tracking-widest mb-6">{strukturDesa.jabatan}</p>
                            
                            <div className="grid grid-cols-2 gap-3 pt-6 border-t border-gray-50">
                                <div className="p-3 bg-gray-50 rounded-xl">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Kategori</p>
                                    <p className="text-[10px] font-black text-gray-700 uppercase italic">{strukturDesa.kategori.replace('_', ' ')}</p>
                                </div>
                                <div className="p-3 bg-gray-50 rounded-xl">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Urutan</p>
                                    <p className="text-[10px] font-black text-gray-700 uppercase italic">Posisi Ke-{strukturDesa.urutan}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                            <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <MapPin className="w-4 h-4 text-red-500" />
                                Alamat Domisili
                            </h4>
                            <p className="text-xs font-bold text-gray-600 leading-relaxed italic uppercase">
                                {strukturDesa.alamat || 'Alamat belum diatur.'}
                            </p>
                        </div>
                    </div>

                    {/* Detail Information */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h4 className="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2 italic">
                                    <Shield className="w-4 h-4 text-blue-600" />
                                    Informasi Lengkap Perangkat
                                </h4>
                            </div>
                            <div className="divide-y divide-gray-50">
                                {detailRows.map((row, index) => (
                                    <div key={index} className="p-6 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-10 hover:bg-gray-50/50 transition-colors">
                                        <div className="sm:w-1/3 flex items-center gap-3">
                                            <row.icon className={`w-4 h-4 ${row.color}`} />
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{row.label}</span>
                                        </div>
                                        <div className="sm:flex-1">
                                            <span className="text-sm font-black text-gray-800 uppercase italic tracking-tight">{row.value}</span>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50">
                                <h4 className="text-xs font-black text-gray-900 uppercase tracking-widest flex items-center gap-2 italic">
                                    <FileText className="w-4 h-4 text-amber-600" />
                                    Tugas & Wewenang Jabatan
                                </h4>
                            </div>
                            <div className="p-8">
                                <div className="prose prose-sm max-w-none">
                                    {strukturDesa.tugas_wewenang ? (
                                        <div className="text-xs font-bold text-gray-600 leading-loose italic whitespace-pre-wrap uppercase tracking-tight">
                                            {strukturDesa.tugas_wewenang}
                                        </div>
                                    ) : (
                                        <div className="flex flex-col items-center justify-center py-10 opacity-30 italic">
                                            <FileText className="w-12 h-12 mb-4" />
                                            <p className="font-black uppercase tracking-widest text-xs">Belum ada rincian tugas</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
