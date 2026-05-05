import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    FileSignature, ArrowLeft, User, 
    Calendar, MapPin, Info, Printer,
    CheckCircle2, XCircle, Clock, Edit,
    FileText, Hash, BadgeCheck, ShieldCheck,
    Download, Settings2
} from 'lucide-react';
import { cn } from '@/lib/utils';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import Swal from 'sweetalert2';

dayjs.locale('id');

export default function Show({ auth, suratPengajuan, statusList }) {
    const p = suratPengajuan;
    
    const getStatusStyle = (status) => {
        switch (status) {
            case 'selesai':
                return { bg: 'bg-green-100', border: 'border-green-200', text: 'text-green-700', icon: <BadgeCheck className="w-4 h-4 mr-1.5" /> };
            case 'diproses':
                return { bg: 'bg-blue-100', border: 'border-blue-200', text: 'text-blue-700', icon: <Clock className="w-4 h-4 mr-1.5" /> };
            case 'ditolak':
                return { bg: 'bg-red-100', border: 'border-red-200', text: 'text-red-700', icon: <XCircle className="w-4 h-4 mr-1.5" /> };
            default:
                return { bg: 'bg-yellow-100', border: 'border-yellow-200', text: 'text-yellow-700', icon: <Clock className="w-4 h-4 mr-1.5" /> };
        }
    };

    const statusStyle = getStatusStyle(p.status);


    return (
        <AuthenticatedLayout user={auth.user} title="Detail Pengajuan Surat">
            <Head title={`Detail Surat - ${p.nomor_surat || 'Draft'}`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header Section */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 text-left">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <FileSignature className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Detail Pengajuan Surat
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-2 opacity-80">
                                    ID: #{p.id} • Dibuat pada {dayjs(p.created_at).format('DD MMMM YYYY HH:mm')}
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link 
                                href={route('admin.surat-pengajuan.edit', p.id)}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 border border-white/10 text-white rounded-xl text-[10px] font-black transition-all uppercase tracking-widest"
                            >
                                <Edit className="w-3.5 h-3.5 mr-2" />
                                EDIT DATA
                            </Link>
                            <a 
                                href={route('admin.surat-pengajuan.pdf', p.id)}
                                target="_blank"
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Printer className="w-3.5 h-3.5 mr-2" />
                                CETAK SURAT
                            </a>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    {/* Main Content (Left) */}
                    <div className="xl:col-span-2 space-y-6">
                        {/* Status Card */}
                        <div className={cn(
                            "p-6 rounded-3xl border shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4 animate-in slide-in-from-left-6 duration-500",
                            statusStyle.bg, statusStyle.border
                        )}>
                            <div className="flex items-center text-left">
                                <div className={cn("w-12 h-12 rounded-2xl flex items-center justify-center mr-4 bg-white/50", statusStyle.text)}>
                                    {statusStyle.icon}
                                </div>
                                <div className="text-left">
                                    <h3 className={cn("text-xs font-black uppercase tracking-widest", statusStyle.text)}>Status Pengajuan</h3>
                                    <p className={cn("text-lg font-black uppercase italic tracking-tighter", statusStyle.text)}>
                                        {statusList[p.status] || p.status}
                                    </p>
                                </div>
                            </div>
                            {p.completed_at && (
                                <div className="text-left sm:text-right">
                                    <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Selesai Pada</p>
                                    <p className="text-xs font-black text-gray-700 uppercase tracking-tight">
                                        {dayjs(p.completed_at).format('DD MMMM YYYY HH:mm')}
                                    </p>
                                </div>
                            )}
                        </div>

                        {/* General Information */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-6 duration-500 delay-100">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                <Info className="w-5 h-5 text-gray-400" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Umum</h3>
                            </div>
                            <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                <div className="space-y-1 text-left">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nomor Surat</p>
                                    <p className="text-lg font-black text-gray-900 tracking-tighter uppercase italic">{p.nomor_surat || 'BELUM TERBIT'}</p>
                                </div>
                                <div className="space-y-1 text-left">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Surat</p>
                                    <p className="text-md font-black text-gray-700 tracking-widest uppercase">{p.surat_type_name || p.jenis_surat}</p>
                                </div>
                                <div className="space-y-1 text-left">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal Surat</p>
                                    <p className="text-md font-bold text-gray-700 uppercase">{dayjs(p.tanggal_surat).format('DD MMMM YYYY')}</p>
                                </div>
                                <div className="space-y-1 text-left">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanda Tangan Oleh</p>
                                    <p className="text-md font-bold text-gray-700 uppercase">{p.penandatangan?.replace('_', ' ') || 'KEPALA DESA'}</p>
                                </div>
                                <div className="md:col-span-2 space-y-1 text-left">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Keperluan / Maksud</p>
                                    <p className="text-sm font-bold text-gray-800 leading-relaxed">{p.keperluan || '-'}</p>
                                </div>
                                {p.tujuan && (
                                    <div className="md:col-span-2 space-y-1 text-left">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tujuan Penggunaan</p>
                                        <p className="text-sm font-bold text-gray-800 leading-relaxed">{p.tujuan}</p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Data Tambahan (Dynamic Fields) */}
                        {p.data_tambahan && Object.keys(p.data_tambahan).length > 0 && (
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-6 duration-500 delay-200">
                                <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3">
                                    <Settings2 className="w-5 h-5 text-gray-400" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Spesifik Surat</h3>
                                </div>
                                <div className="p-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 text-left">
                                    {Object.entries(p.data_tambahan).map(([key, value]) => {
                                        // Skip internal/redundant keys if any
                                        if (['id', 'created_at', 'updated_at'].includes(key)) return null;
                                        
                                        // Humanize key
                                        const label = key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                                        
                                        return (
                                            <div key={key} className="space-y-1 text-left">
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{label}</p>
                                                <p className="text-sm font-bold text-gray-800 uppercase tracking-tight">{value?.toString() || '-'}</p>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Sidebar Information (Right) */}
                    <div className="space-y-6 animate-in slide-in-from-right-6 duration-500 delay-300">
                        {/* Penduduk Card */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center gap-3 text-left">
                                <User className="w-5 h-5 text-gray-400" />
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Penduduk</h3>
                            </div>
                            <div className="p-6 text-center">
                                <div className="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4 text-green-700 shadow-inner">
                                    <User className="w-10 h-10" />
                                </div>
                                <h4 className="text-lg font-black text-gray-900 uppercase italic tracking-tighter mb-1">
                                    {p.penduduk?.nama || p.data_tambahan?.nama || 'NAMA TIDAK TERSEDIA'}
                                </h4>
                                <p className="text-xs font-bold text-gray-400 tracking-widest uppercase mb-4">
                                    NIK: {p.penduduk?.nik || p.data_tambahan?.nik || '-'}
                                </p>
                                
                                {p.penduduk && (
                                    <div className="space-y-3 pt-4 border-t border-gray-50 text-left">
                                        <div className="flex items-start gap-3">
                                            <MapPin className="w-4 h-4 text-gray-300 shrink-0 mt-0.5" />
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Alamat</p>
                                                <p className="text-[10px] font-bold text-gray-600 uppercase leading-relaxed">
                                                    {p.penduduk.alamat || '-'} <br />
                                                    RW {p.penduduk.rw_label} / RT {p.penduduk.rt_label} <br />
                                                    {p.penduduk.dusun_label}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Admin Info */}
                        <div className="bg-gray-50 rounded-3xl p-6 border border-gray-100 space-y-4 text-left">
                            <div className="flex items-center gap-3">
                                <ShieldCheck className="w-5 h-5 text-blue-600" />
                                <h3 className="text-[10px] font-black text-gray-900 uppercase tracking-widest">Petugas Pemroses</h3>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-blue-700 shadow-sm border border-blue-50 font-black text-xs">
                                    {p.admin?.name?.substring(0, 2).toUpperCase() || 'AD'}
                                </div>
                                <div className="text-left">
                                    <p className="text-xs font-black text-gray-800 uppercase italic tracking-tighter">{p.admin?.name || 'Sistem'}</p>
                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{p.admin?.email || '-'}</p>
                                </div>
                            </div>
                        </div>

                        {/* Admin Notes */}
                        {p.keterangan_tambahan && (
                            <div className="bg-yellow-50 rounded-3xl p-6 border border-yellow-100 space-y-3 text-left">
                                <div className="flex items-center gap-3">
                                    <Info className="w-4 h-4 text-yellow-600" />
                                    <h3 className="text-[10px] font-black text-yellow-900 uppercase tracking-widest">Catatan Admin</h3>
                                </div>
                                <p className="text-[11px] font-bold text-yellow-800 leading-relaxed italic">
                                    "{p.keterangan_tambahan}"
                                </p>
                            </div>
                        )}

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
