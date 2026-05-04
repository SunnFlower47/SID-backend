import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    User, IdCard, Calendar, MapPin, Briefcase, 
    ArrowLeft, Edit, GraduationCap, Heart, 
    Users, Clock, ShieldCheck, Map,
    ChevronRight, FileText, History, Activity, Info
} from 'lucide-react';
import { cn } from '@/lib/utils';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

export default function Show({ auth, penduduk }) {
    const handleBack = (e) => {
        e.preventDefault();
        window.history.back();
    };

    const formatDate = (dateString, formatStr = 'dd MMMM yyyy') => {
        if (!dateString) return '-';
        try {
            return format(new Date(dateString), formatStr, { locale: localeId });
        } catch (e) {
            return '-';
        }
    };

    const InfoRow = ({ label, value, icon: Icon, color = "blue" }) => (
        <div className="flex items-center gap-4 p-4 bg-gray-50/50 rounded-2xl border border-gray-100 hover:bg-white hover:shadow-md transition-all group">
            <div className={cn(
                "w-10 h-10 rounded-xl flex items-center justify-center shrink-0 border transition-all group-hover:scale-110",
                color === "blue" && "bg-blue-50 text-blue-600 border-blue-100",
                color === "green" && "bg-green-50 text-green-600 border-green-100",
                color === "purple" && "bg-purple-50 text-purple-600 border-purple-100",
                color === "orange" && "bg-orange-50 text-orange-600 border-orange-100"
            )}>
                <Icon className="w-5 h-5" />
            </div>
            <div className="min-w-0 flex-1">
                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1.5">{label}</p>
                <p className="text-sm font-black text-gray-900 truncate leading-tight uppercase italic">{value || '-'}</p>
            </div>
        </div>
    );

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail - ${penduduk.nama}`}>
            <Head title={`Detail Penduduk - ${penduduk.nama}`} />

            <div className="max-w-7xl mx-auto space-y-6 animate-in fade-in duration-700 pb-12">
                
                {/* 1. CONSISTENT HEADER */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <User className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">Detail Penduduk</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 flex items-center gap-2 text-left">
                                    <ShieldCheck className="w-3 h-3 text-yellow-300" />
                                    Profil Terverifikasi Sistem
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <button 
                                onClick={handleBack}
                                className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all active:scale-95 uppercase tracking-widest group"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" />
                                KEMBALI
                            </button>
                            <Link 
                                href={route('penduduk.edit', penduduk.id)}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest"
                            >
                                <Edit className="w-4 h-4 mr-2" />
                                EDIT PROFIL
                            </Link>
                        </div>
                    </div>
                </div>

                {/* 2. MAIN GRID LAYOUT */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 items-start">
                    
                    {/* LEFT SIDE: SUMMARY CARD */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                            <div className="h-24 bg-gray-50 relative">
                                <div className="absolute inset-0 bg-gradient-to-b from-gray-100/50 to-transparent"></div>
                                <div className="absolute -bottom-12 left-1/2 -translate-x-1/2">
                                    <div className="w-24 h-24 bg-white rounded-[28px] p-1.5 shadow-2xl border border-gray-50">
                                        <div className="w-full h-full bg-gray-50 rounded-[20px] flex items-center justify-center overflow-hidden">
                                            <User className="w-12 h-12 text-gray-200" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="pt-14 pb-8 px-6 text-center">
                                <h2 className="text-lg font-black text-gray-900 uppercase tracking-tight italic mb-1">{penduduk.nama}</h2>
                                <div className="flex justify-center gap-2 mb-8">
                                    <span className="px-3 py-1 bg-blue-50 text-blue-600 border border-blue-100 rounded-full text-[9px] font-black uppercase tracking-widest">{penduduk.jenis_kelamin}</span>
                                    <span className="px-3 py-1 bg-green-50 text-green-700 border border-green-100 rounded-full text-[9px] font-black uppercase tracking-widest">{penduduk.kedudukan_keluarga}</span>
                                </div>

                                <div className="grid grid-cols-1 gap-4">
                                    <div className="p-5 bg-gray-50/50 rounded-3xl border border-gray-100 text-center group hover:bg-white hover:shadow-md transition-all">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">NIK Penduduk</p>
                                        <p className="text-lg md:text-xl font-black text-gray-950 font-mono tracking-wider">{penduduk.nik}</p>
                                    </div>
                                    <div className="p-5 bg-gray-50/50 rounded-3xl border border-gray-100 text-center group hover:bg-white hover:shadow-md transition-all">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Nomor Kartu Keluarga</p>
                                        <p className="text-lg md:text-xl font-black text-gray-700 font-mono tracking-wider">{penduduk.nkk}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* INFO LOG - DESKTOP ONLY HERE */}
                        <div className="hidden lg:block bg-white rounded-3xl border border-gray-100 shadow-lg p-6 space-y-4">
                            <h3 className="text-[10px] font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                <Clock className="w-4 h-4 text-gray-400" />
                                Timeline Sistem
                            </h3>
                            <div className="space-y-3 pt-2">
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Input Sistem</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(penduduk.created_at, 'dd/MM/yyyy')}</span>
                                </div>
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Update Terakhir</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(penduduk.updated_at, 'dd/MM/yyyy')}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* RIGHT SIDE: FULL DETAILS */}
                    <div className="lg:col-span-2 space-y-6 md:space-y-8">
                        
                        {/* BIODATA SECTION */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                            <div className="p-6 md:p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
                                <h3 className="text-sm font-black text-gray-900 uppercase tracking-[0.2em] flex items-center gap-3 italic">
                                    <FileText className="w-5 h-5 text-green-600" />
                                    Biodata Lengkap
                                </h3>
                                <Info className="w-4 h-4 text-gray-200" />
                            </div>
                            <div className="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                <InfoRow label="Tempat, Tanggal Lahir" value={`${penduduk.tempat_lahir}, ${formatDate(penduduk.tanggal_lahir)}`} icon={Calendar} color="blue" />
                                <InfoRow label="Usia Warga" value={`${penduduk.usia} Tahun`} icon={Clock} color="orange" />
                                <InfoRow label="Agama & Perkawinan" value={`${penduduk.agama} • ${penduduk.status_perkawinan}`} icon={Heart} color="purple" />
                                <InfoRow label="Pendidikan Terakhir" value={penduduk.pendidikan} icon={GraduationCap} color="green" />
                                <InfoRow label="Pekerjaan Utama" value={penduduk.pekerjaan} icon={Briefcase} color="blue" />
                                <InfoRow label="Alamat / Dusun" value={`${penduduk.alamat} (${penduduk.dusun_label})`} icon={MapPin} color="orange" />
                                <InfoRow label="RT / RW" value={`RT ${penduduk.rt_label} / RW ${penduduk.rw_label}`} icon={Map} color="purple" />
                                <InfoRow label="Orang Tua (Ayah/Ibu)" value={`${penduduk.nama_ayah || '-'} / ${penduduk.nama_ibu || '-'}`} icon={Users} color="green" />
                            </div>
                        </div>

                        {/* FAMILY SECTION */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                            <div className="p-6 md:p-8 border-b border-gray-50 flex items-center justify-between">
                                <h3 className="text-sm font-black text-gray-900 uppercase tracking-[0.2em] flex items-center gap-3 italic">
                                    <Users className="w-5 h-5 text-blue-600" />
                                    Anggota Keluarga
                                </h3>
                                <span className="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[9px] font-black uppercase tracking-widest border border-blue-100">{penduduk.kartu_keluarga?.penduduks?.length || 0} Jiwa</span>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="bg-gray-50/50 border-b border-gray-50">
                                            <th className="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Warga</th>
                                            <th className="px-6 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest hidden md:table-cell">NIK</th>
                                            <th className="px-8 py-5 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {penduduk.kartu_keluarga?.penduduks?.map((member) => (
                                            <tr key={member.id} className={cn("transition-all hover:bg-blue-50/20", member.id === penduduk.id && "bg-blue-50/40")}>
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className={cn("w-8 h-8 rounded-lg flex items-center justify-center border", member.id === penduduk.id ? "bg-blue-600 text-white" : "bg-white text-gray-300 border-gray-100 shadow-sm")}>
                                                            <User className="w-4 h-4" />
                                                        </div>
                                                        <div className="min-w-0">
                                                            <p className="text-xs font-black text-gray-900 uppercase tracking-tight truncate">{member.nama}</p>
                                                            <p className="text-[8px] font-bold text-gray-400 uppercase tracking-widest">{member.kedudukan_keluarga}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 font-mono text-[10px] text-gray-400 hidden md:table-cell">{member.nik}</td>
                                                <td className="px-6 py-4 text-right">
                                                    <Link href={route('penduduk.show', member.id)} className="p-2 bg-white border border-gray-100 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm inline-block active:scale-95">
                                                        <ChevronRight className="w-3 h-3" />
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {/* MUTATION SECTION */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden">
                            <div className="p-6 md:p-8 border-b border-gray-50 flex items-center gap-3 bg-orange-50/20">
                                <History className="w-5 h-5 text-orange-600" />
                                <h3 className="text-sm font-black text-gray-900 uppercase tracking-[0.2em] italic">Riwayat Mutasi</h3>
                            </div>
                            <div className="p-6 md:p-8">
                                {penduduk.mutasis?.length > 0 ? (
                                    <div className="space-y-6">
                                        {penduduk.mutasis.map((m) => (
                                            <div key={m.id} className="flex gap-4 group">
                                                <div className="w-10 h-10 bg-white rounded-xl flex items-center justify-center border-2 border-orange-100 shadow-sm shrink-0 group-hover:bg-orange-600 group-hover:text-white transition-all">
                                                    <Activity className="w-4 h-4" />
                                                </div>
                                                <div className="flex-1 border-b border-gray-50 pb-4 last:border-0">
                                                    <div className="flex justify-between items-start mb-1">
                                                        <h4 className="text-xs font-black text-gray-900 uppercase tracking-tight">{m.jenis_mutasi}</h4>
                                                        <span className="text-[9px] font-black text-gray-400 uppercase">{formatDate(m.tanggal_mutasi)}</span>
                                                    </div>
                                                    <p className="text-xs text-gray-500 italic leading-relaxed">{m.keterangan || 'Tidak ada catatan.'}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-10 bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-100">
                                        <History className="w-10 h-10 text-gray-200 mx-auto mb-3" />
                                        <p className="text-[10px] font-black text-gray-300 uppercase tracking-widest">Belum ada riwayat aktivitas</p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* INFO LOG - MOBILE ONLY HERE */}
                        <div className="lg:hidden bg-white rounded-3xl border border-gray-100 shadow-lg p-6 space-y-4 mb-6">
                            <h3 className="text-[10px] font-black text-gray-900 uppercase tracking-widest flex items-center gap-2">
                                <Clock className="w-4 h-4 text-gray-400" />
                                Timeline Sistem
                            </h3>
                            <div className="space-y-3 pt-2">
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Input Sistem</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(penduduk.created_at, 'dd/MM/yyyy')}</span>
                                </div>
                                <div className="flex justify-between items-center text-[10px]">
                                    <span className="font-bold text-gray-400 uppercase">Update Terakhir</span>
                                    <span className="font-black text-gray-800 uppercase italic">{formatDate(penduduk.updated_at, 'dd/MM/yyyy')}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
