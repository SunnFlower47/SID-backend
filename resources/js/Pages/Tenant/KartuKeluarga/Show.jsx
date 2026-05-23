import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Home, Edit, Crown, Users, Eye, AlertTriangle, 
    UserCircle, MapPin, Calendar, Briefcase, GraduationCap, 
    Heart, User, ChevronRight, CheckCircle, Trash2, IdCard
} from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

// Shared Components
import { PageHeader, InfoRow, Badge } from '@/Components/Shared';

export default function Show({ auth, kk, kartuKeluarga, kepalaKeluarga, anggotaKeluarga, nkk }) {
    
    const getStatusStyle = (status) => {
        const s = (status || '').toLowerCase();
        if (s === 'aktif') return 'green';
        if (s === 'meninggal') return 'red';
        if (s === 'pindah') return 'orange';
        return 'gray';
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Kartu Keluarga">
            <Head title={`Detail KK - ${nkk}`} />

            <div className="space-y-6 animate-in fade-in duration-500 pb-12">
                
                {/* 1. CONSISTENT HEADER */}
                <PageHeader 
                    title="Detail Kartu Keluarga"
                    titleSize="sm"
                    subtitle={`No KK: ${nkk}`}
                    icon={Home}
                    backHref={route('kk.index')}
                    actions={[
                        ...(kk.status_kk && ['bermasalah', 'bermasalah_sementara'].includes(kk.status_kk) ? [{
                            label: 'SELESAIKAN RESOLUSI',
                            icon: AlertTriangle,
                            href: route('kk.bermasalah', nkk),
                            variant: 'danger',
                        }] : []),
                        {
                            label: 'EDIT KK',
                            icon: Edit,
                            href: route('kk.edit', nkk),
                            variant: 'ghost'
                        }
                    ]}
                />

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
                                    className="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest flex items-center bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-full transition-all"
                                >
                                    PROFIL LENGKAP <Eye className="ml-1.5 w-3 h-3" />
                                </Link>
                            )}
                        </div>
                        <div className="p-8">
                            {kepalaKeluarga ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                    <InfoRow label="Nama Lengkap" value={kepalaKeluarga.nama} icon={UserCircle} color="blue" />
                                    <InfoRow label="NIK" value={kepalaKeluarga.nik} icon={IdCard} color="teal" />
                                    <InfoRow label="Tempat, Tanggal Lahir" value={`${kepalaKeluarga.tempat_lahir}, ${new Date(kepalaKeluarga.tanggal_lahir).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}`} icon={Calendar} color="purple" />
                                    <InfoRow label="Pekerjaan" value={kepalaKeluarga.pekerjaan} icon={Briefcase} color="orange" />
                                    <InfoRow label="Pendidikan" value={kepalaKeluarga.pendidikan} icon={GraduationCap} color="indigo" />
                                    <InfoRow label="Status Perkawinan" value={kepalaKeluarga.status_perkawinan} icon={Heart} color="pink" />
                                    
                                    <div className="md:col-span-2 pt-6 border-t border-gray-50 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                        <div className="flex items-center gap-3">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status Penduduk:</span>
                                            <Badge color={getStatusStyle(kepalaKeluarga.status)}>{kepalaKeluarga.status}</Badge>
                                        </div>
                                        <div className="text-left md:text-right bg-gray-50 p-4 rounded-2xl border border-gray-100 w-full md:w-auto">
                                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Alamat Domisili</p>
                                            <p className="text-sm font-bold text-gray-900">{kk.alamat}</p>
                                            <p className="text-[10px] font-bold text-gray-500 uppercase tracking-widest">RT {kk.rt_label} / RW {kk.rw_label}, Dsn. {kk.dusun_label}</p>
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
                                <div className="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100">
                                    <Users className="w-10 h-10 text-blue-400" />
                                </div>
                                <h4 className="text-4xl font-black text-gray-950 leading-none">{kartuKeluarga.length}</h4>
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
                                    <th className="px-6 py-4 hidden md:table-cell">NIK</th>
                                    <th className="px-6 py-4">Hubungan</th>
                                    <th className="px-6 py-4">Jenis Kelamin</th>
                                    <th className="px-6 py-4">Status</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {anggotaKeluarga.map(p => (
                                    <tr key={p.id} className={cn("transition-all hover:bg-gray-50", p.id === kepalaKeluarga?.id && "bg-blue-50/30")}>
                                        <td className="px-6 py-4">
                                            <div className="flex items-center gap-3">
                                                <div className={cn("w-8 h-8 rounded-lg flex items-center justify-center border", p.id === kepalaKeluarga?.id ? "bg-blue-600 text-white border-blue-600" : "bg-white text-gray-300 border-gray-200")}>
                                                    {p.id === kepalaKeluarga?.id ? <Crown className="w-4 h-4" /> : <User className="w-4 h-4" />}
                                                </div>
                                                <p className="font-black text-gray-900 uppercase text-xs tracking-tight truncate">{p.nama}</p>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 font-mono text-xs font-bold text-gray-600 hidden md:table-cell">{p.nik}</td>
                                        <td className="px-6 py-4">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{p.kedudukan_keluarga || 'Anggota'}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <Badge color="gray">{p.jenis_kelamin === 'LAKI-LAKI' ? 'L' : 'P'}</Badge>
                                        </td>
                                        <td className="px-6 py-4">
                                            {p.deleted_at ? (
                                                <Badge color="red" size="sm">{p.mutasis?.[0]?.jenis_mutasi_label || 'MUTASI'}</Badge>
                                            ) : (
                                                <Badge color="green" size="sm">AKTIF</Badge>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <Link 
                                                href={route('penduduk.show', p.id)}
                                                className="inline-flex items-center justify-center w-8 h-8 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-xl transition-all shadow-sm active:scale-95"
                                                title="Lihat Detail Penduduk"
                                            >
                                                <ChevronRight className="w-4 h-4" />
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
