import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Home, ArrowLeft, Edit, Crown, Users, Eye, CheckCircle, Trash2, AlertTriangle, UserCircle, MapPin, Calendar, Briefcase, GraduationCap, Heart, User } from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function Show({ auth, kk, kartuKeluarga, kepalaKeluarga, anggotaKeluarga, nkk }) {
    

    const getStatusStyle = (status) => {
        const s = (status || '').toLowerCase();
        if (s === 'aktif') return 'bg-green-100 text-green-800 border-green-200';
        if (s === 'meninggal') return 'bg-red-100 text-red-800 border-red-200';
        if (s === 'pindah') return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        return 'bg-gray-100 text-gray-800 border-gray-200';
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Kartu Keluarga">
            <Head title={`Detail KK - ${nkk}`} />

            <div className="space-y-6 animate-in fade-in duration-500">
                {/* Header Card */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 text-white relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Home className="w-8 h-8 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black tracking-tight uppercase italic leading-none">Detail Kartu Keluarga</h1>
                                <p className="text-emerald-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">No KK: {nkk}</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link 
                                href={route('kk.index')}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2" /> KEMBALI
                            </Link>
                            <Link 
                                href={route('kk.edit', nkk)}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <Edit className="w-4 h-4 mr-2" /> EDIT KK
                            </Link>
                            {kk.status_kk && ['bermasalah', 'bermasalah_sementara'].includes(kk.status_kk) && (
                                <Link 
                                    href={route('kk.bermasalah', nkk)}
                                    className="flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg transition-all animate-pulse"
                                >
                                    <AlertTriangle className="w-4 h-4 mr-2" /> SELESAIKAN RESOLUSI
                                </Link>
                            )}
                        </div>
                    </div>
                </div>

                {/* Info Utama Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Kepala Keluarga Card */}
                    <div className="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                            <h3 className="text-sm font-black text-gray-900 flex items-center uppercase italic tracking-tighter">
                                <Crown className="w-5 h-5 text-yellow-500 mr-2" />
                                Informasi Kepala Keluarga
                            </h3>
                            {kepalaKeluarga && (
                                <Link 
                                    href={route('penduduk.show', kepalaKeluarga.id)}
                                    className="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest flex items-center"
                                >
                                    PROFIL LENGKAP <Eye className="ml-1.5 w-3.5 h-3.5" />
                                </Link>
                            )}
                        </div>
                        <div className="p-8">
                            {kepalaKeluarga ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div className="space-y-6">
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                                                <UserCircle className="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Nama Lengkap</p>
                                                <p className="text-sm font-black text-gray-900 uppercase italic tracking-tight leading-tight">{kepalaKeluarga.nama}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                                                <MapPin className="w-5 h-5 text-emerald-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">NIK</p>
                                                <p className="text-sm font-mono font-black text-gray-900">{kepalaKeluarga.nik}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                                                <Calendar className="w-5 h-5 text-purple-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tempat, Tanggal Lahir</p>
                                                <p className="text-sm font-bold text-gray-900">{kepalaKeluarga.tempat_lahir}, {new Date(kepalaKeluarga.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-6">
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center shrink-0">
                                                <Briefcase className="w-5 h-5 text-orange-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pekerjaan</p>
                                                <p className="text-sm font-bold text-gray-900">{kepalaKeluarga.pekerjaan}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                                                <GraduationCap className="w-5 h-5 text-indigo-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pendidikan</p>
                                                <p className="text-sm font-bold text-gray-900">{kepalaKeluarga.pendidikan}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start gap-4">
                                            <div className="w-10 h-10 bg-pink-50 rounded-xl flex items-center justify-center shrink-0">
                                                <Heart className="w-5 h-5 text-pink-600" />
                                            </div>
                                            <div>
                                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Perkawinan</p>
                                                <p className="text-sm font-bold text-gray-900">{kepalaKeluarga.status_perkawinan}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="md:col-span-2 pt-6 border-t border-gray-50 flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Penduduk:</span>
                                            <span className={cn("px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border", getStatusStyle(kepalaKeluarga.status))}>
                                                {kepalaKeluarga.status}
                                            </span>
                                        </div>
                                        <div className="text-right">
                                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alamat Domisili</p>
                                            <p className="text-xs font-bold text-gray-900">{kk.alamat}</p>
                                            <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">RT {kk.rt_label} / RW {kk.rw_label}, {kk.dusun_label}</p>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="py-12 text-center bg-red-50 rounded-3xl border border-red-100">
                                    <AlertTriangle className="w-12 h-12 text-red-500 mx-auto mb-4" />
                                    <h4 className="text-lg font-black text-red-900 uppercase italic leading-none">Kepala Keluarga Tidak Ditemukan!</h4>
                                    <p className="text-xs font-bold text-red-600 uppercase tracking-widest mt-2">Segera perbarui data kepala keluarga menggunakan tombol di atas.</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Ringkasan Keluarga Card */}
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div className="p-6 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-sm font-black text-gray-900 flex items-center uppercase italic tracking-tighter">
                                <Users className="w-5 h-5 text-blue-500 mr-2" />
                                Ringkasan Keluarga
                            </h3>
                        </div>
                        <div className="p-8 flex-1 flex flex-col justify-center space-y-8">
                            <div className="text-center">
                                <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <Users className="w-10 h-10 text-gray-400" />
                                </div>
                                <h4 className="text-3xl font-black text-gray-950 leading-none">{kartuKeluarga.length}</h4>
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-2">Total Anggota Keluarga</p>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div className="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-100">
                                    <p className="text-2xl font-black text-emerald-700 leading-none">{kartuKeluarga.filter(p => !p.deleted_at).length}</p>
                                    <p className="text-[9px] font-black text-emerald-600 uppercase tracking-widest mt-1">AKTIF</p>
                                </div>
                                <div className="bg-red-50 rounded-2xl p-4 text-center border border-red-100">
                                    <p className="text-2xl font-black text-red-700 leading-none">{kartuKeluarga.filter(p => p.deleted_at).length}</p>
                                    <p className="text-[9px] font-black text-red-600 uppercase tracking-widest mt-1">MUTASI/OFF</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Daftar Anggota Keluarga */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 className="text-sm font-black text-gray-900 flex items-center uppercase italic tracking-tighter">
                            <Users className="w-5 h-5 text-emerald-600 mr-2" />
                            Daftar Anggota Keluarga
                        </h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full text-left text-sm">
                            <thead className="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <tr>
                                    <th className="px-6 py-4">Nama Lengkap</th>
                                    <th className="px-6 py-4">NIK</th>
                                    <th className="px-6 py-4">Hubungan</th>
                                    <th className="px-6 py-4">Jenis Kelamin</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {anggotaKeluarga.map(p => (
                                    <tr key={p.id} className="hover:bg-gray-50 transition-all">
                                        <td className="px-6 py-4">
                                            <p className="font-black text-gray-900 uppercase text-xs tracking-tight">{p.nama}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="font-mono text-xs font-bold text-gray-600">{p.nik}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{p.kedudukan_keluarga || 'Anggota'}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <p className="font-bold text-gray-900">{p.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P'}</p>
                                        </td>
                                        <td className="px-6 py-4">
                                            {p.deleted_at ? (
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-red-100 text-red-800 uppercase italic">
                                                    {p.mutasis?.[0]?.jenis_mutasi_label || 'MUTASI'}
                                                </span>
                                            ) : (
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-black bg-emerald-100 text-emerald-800 uppercase italic">AKTIF</span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <Link 
                                                href={route('penduduk.show', p.id)}
                                                className="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                                            >
                                                <Eye className="w-3.5 h-3.5 mr-1.5" /> DETAIL
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                                {anggotaKeluarga.length === 0 && (
                                    <tr>
                                        <td colSpan="6" className="px-6 py-12 text-center text-gray-400">
                                            <User className="w-12 h-12 mx-auto mb-2 opacity-20" />
                                            <p className="text-xs font-black uppercase italic tracking-widest">Tidak ada anggota tambahan</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
