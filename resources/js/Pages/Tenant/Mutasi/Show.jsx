import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { 
  ArrowLeft, 
  Edit, 
  Calendar, 
  MapPin, 
  User, 
  Users, 
  Baby, 
  UserX, 
  History, 
  Info,
  Clock,
  RotateCcw,
  XCircle,
  FileText,
  Split,
  ChevronRight,
  ShieldCheck,
  Eye
} from 'lucide-react';
import Swal from 'sweetalert2';
import axios from 'axios';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';
import { cn } from '@/lib/utils';

export default function Show({ auth, mutasi }) {
    // Gunakan attribute dari backend (bukan hardcode list) agar selalu sinkron
    const isSoftDelete = mutasi.is_soft_delete_type ?? false;
    const isPembaruanKK = mutasi.is_pembaruan_kk ?? false;
    const isUndoBlocked = mutasi.is_undo_blocked ?? false;

    const formatDate = (dateString) => {
        if (!dateString) return '-';
        try {
            return format(new Date(dateString), 'dd MMMM yyyy', { locale: localeId });
        } catch (e) {
            return dateString;
        }
    };

    const handleAction = () => {
        const actionLabel = isPembaruanKK 
            ? 'Undo Pembaruan KK'
            : isSoftDelete ? 'Undo (Kembalikan Data)' : 'Cancel (Batalkan Mutasi)';
        const confirmMsg = isPembaruanKK
            ? 'Apakah Anda yakin? Kedudukan penduduk akan dikembalikan ke status sebelumnya dan KK akan kembali berstatus bermasalah.'
            : isSoftDelete 
                ? `Apakah Anda yakin ingin melakukan Undo pada mutasi ini? Data penduduk akan dikembalikan ke status aktif.`
                : `Apakah Anda yakin ingin membatalkan mutasi ini? Data yang baru dibuat akan dihapus secara permanen.`;
    
        Swal.fire({
            title: isPembaruanKK ? 'Undo Pembaruan KK?' : isSoftDelete ? 'Undo Mutasi?' : 'Batalkan Mutasi?',
            text: confirmMsg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: `Ya, ${isPembaruanKK ? 'Undo' : isSoftDelete ? 'Undo' : 'Batalkan'}!`
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    // pembaruan_kk dan soft-delete type selalu pakai route undo
                    const response = isSoftDelete 
                        ? await axios.post(route('mutasi.undo', mutasi.id))
                        : await axios.delete(route('mutasi.cancel', mutasi.id));
                    
                    if (response.data.success) {
                        Swal.fire('Berhasil!', response.data.message || (actionLabel + ' berhasil'), 'success').then(() => {
                            router.visit(route('mutasi.data.index'));
                        });
                    }
                } catch (error) {
                    Swal.fire('Error', error.response?.data?.message || 'Gagal memproses permintaan', 'error');
                }
            }
        });
    };

    const DetailSection = ({ icon: Icon, title, color = 'blue', children }) => (
        <div className={cn("bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mb-8")}>
            <div className={cn("px-8 py-5 border-b border-gray-50 flex items-center gap-4", 
                color === 'red' ? 'bg-red-50/30' : 
                color === 'rose' ? 'bg-rose-50/30' : 
                color === 'emerald' ? 'bg-emerald-50/30' : 
                color === 'purple' ? 'bg-purple-50/30' : 
                'bg-blue-50/30'
            )}>
                <div className={cn("w-12 h-12 rounded-2xl flex items-center justify-center shadow-inner", 
                    color === 'red' ? 'bg-red-100 text-red-600' : 
                    color === 'rose' ? 'bg-rose-100 text-rose-600' : 
                    color === 'emerald' ? 'bg-emerald-100 text-emerald-600' : 
                    color === 'purple' ? 'bg-purple-100 text-purple-600' : 
                    'bg-blue-100 text-blue-600'
                )}>
                    <Icon className="w-6 h-6" />
                </div>
                <h3 className="text-lg font-black text-gray-900 uppercase tracking-tight italic">{title}</h3>
            </div>
            <div className="p-8">
                {children}
            </div>
        </div>
    );

    const DataItem = ({ label, value, mono = false }) => (
        <div className="space-y-1">
            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest">{label}</label>
            <p className={cn("text-sm font-bold text-gray-900 leading-tight", mono && "font-mono tracking-tighter")}>{value || '-'}</p>
        </div>
    );

    const renderMutasiSpecifics = () => {
        switch (mutasi.jenis_mutasi) {
            case 'kematian':
                const death = mutasi.data_kematian || {};
                const burial = mutasi.data_pemakaman || {};
                return (
                    <>
                        <DetailSection icon={UserX} title="Detail Kematian" color="red">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                <DataItem label="Tanggal Meninggal" value={formatDate(mutasi.tanggal_mutasi)} />
                                <DataItem label="Hari Meninggal" value={death.hari} />
                                <DataItem label="Jam Meninggal" value={death.jam} />
                                <DataItem label="Bertempat Di" value={death.bertempat_di} />
                                <div className="md:col-span-2">
                                    <DataItem label="Penyebab Kematian" value={mutasi.alasan} />
                                </div>
                            </div>
                        </DetailSection>

                        <DetailSection icon={MapPin} title="Detail Pemakaman" color="rose">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                                <DataItem label="Hari Pemakaman" value={burial.hari} />
                                <DataItem label="Tanggal Pemakaman" value={formatDate(burial.tanggal)} />
                                <DataItem label="Jam Pemakaman" value={burial.jam} />
                                <DataItem label="Lokasi Pemakaman" value={burial.lokasi} />
                            </div>
                        </DetailSection>
                    </>
                );

            case 'kelahiran':
                return (
                    <DetailSection icon={Baby} title="Detail Kelahiran" color="emerald">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <DataItem label="Tempat Lahir" value={mutasi.penduduk?.tempat_lahir} />
                            <DataItem label="Tanggal Lahir" value={formatDate(mutasi.penduduk?.tanggal_lahir)} />
                            <DataItem label="No Kartu Keluarga" value={mutasi.penduduk?.nkk} mono />
                            <DataItem label="Nama Ayah" value={mutasi.penduduk?.nama_ayah} />
                            <DataItem label="Nama Ibu" value={mutasi.penduduk?.nama_ibu} />
                            <DataItem label="Tanggal Mutasi" value={formatDate(mutasi.tanggal_mutasi)} />
                        </div>
                    </DetailSection>
                );

            case 'pisah_kk':
                const snapshot = mutasi.data_snapshot || {};
                return (
                    <DetailSection icon={Split} title="Detail Pisah KK" color="purple">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <DataItem label="Tanggal Pisah KK" value={formatDate(mutasi.tanggal_mutasi)} />
                            <DataItem label="Kategori" value={mutasi.kategori_mutasi?.replace('_', ' ').toUpperCase()} />
                            <DataItem label="Asal / Tujuan" value={mutasi.asal_tujuan} />
                            <DataItem label="Alasan" value={mutasi.alasan} />
                        </div>
                        {snapshot.anggota_pindah?.length > 0 && (
                            <div className="mt-10">
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Anggota yang Ikut Pindah</label>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {snapshot.anggota_pindah.map((member, i) => (
                                        <div key={i} className="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 group hover:border-purple-200 transition-all shadow-sm">
                                            <div className="flex items-center gap-4">
                                                <div className="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-[10px] font-black text-purple-600 border border-purple-100 shadow-sm group-hover:scale-110 transition-transform">
                                                    {i + 1}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-black text-gray-950 uppercase tracking-tighter italic">{member.nama}</p>
                                                    <p className="text-[10px] font-mono font-bold text-gray-400">{member.nik}</p>
                                                </div>
                                            </div>
                                            <span className="px-2.5 py-1 bg-white border border-gray-200 rounded-lg text-[9px] font-black text-gray-500 uppercase tracking-widest">
                                                {member.kedudukan_asal}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </DetailSection>
                );

            case 'pindah_rt_rw':
                const [asal, tujuan] = (mutasi.asal_tujuan || '').split(' → ');
                return (
                    <DetailSection icon={MapPin} title="Detail Pindah RT/RW" color="blue">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <DataItem label="Asal (RT/RW)" value={asal} />
                            <DataItem label="Tujuan (RT/RW)" value={tujuan} />
                            <DataItem label="Tanggal Mutasi" value={formatDate(mutasi.tanggal_mutasi)} />
                            <DataItem label="Alasan" value={mutasi.alasan} />
                        </div>
                    </DetailSection>
                );

            default:
                return (
                    <DetailSection icon={Info} title="Detail Mutasi" color="blue">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <DataItem label="Jenis Mutasi" value={mutasi.jenis_mutasi_label} />
                            <DataItem label="Kategori" value={mutasi.kategori_mutasi?.replace('_', ' ').toUpperCase()} />
                            <DataItem label="Tanggal" value={formatDate(mutasi.tanggal_mutasi)} />
                            <div className="md:col-span-2">
                                <DataItem label="Asal / Tujuan" value={mutasi.asal_tujuan} />
                            </div>
                            <div className="md:col-span-3">
                                <DataItem label="Alasan" value={mutasi.alasan} />
                            </div>
                        </div>
                    </DetailSection>
                );
        }
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Detail Mutasi - ${mutasi.penduduk?.nama}`}>
            <Head title={`Detail Mutasi - ${mutasi.penduduk?.nama}`} />

            <div className="space-y-6 md:space-y-8 animate-in fade-in duration-700 pb-20">
                
                {/* 1. CONSISTENT PREMIUM HEADER */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
                    <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <History className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Detail Mutasi</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 flex items-center gap-2">
                                    <ShieldCheck className="w-3 h-3 text-yellow-300" />
                                    Arsip Riwayat Kependudukan
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <Link 
                                href={route('mutasi.data.index')}
                                className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all active:scale-95 uppercase tracking-widest group"
                            >
                                <ArrowLeft className="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" />
                                KEMBALI
                            </Link>
                            <Link 
                                href={route('mutasi.data.edit', mutasi.id)}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 active:scale-95 uppercase tracking-widest"
                            >
                                <Edit className="w-4 h-4 mr-2" />
                                EDIT DATA
                            </Link>
                            {isUndoBlocked ? (
                                <div className="relative group">
                                    <button 
                                        disabled
                                        className="flex items-center px-6 py-3 rounded-xl text-[10px] sm:text-xs font-black text-white/60 bg-gray-400 cursor-not-allowed uppercase tracking-widest opacity-60"
                                    >
                                        <ShieldCheck className="w-4 h-4 mr-2" />
                                        TERKUNCI
                                    </button>
                                    <div className="absolute bottom-full right-0 mb-2 px-4 py-2 bg-gray-900 text-white text-[10px] font-bold rounded-xl whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none shadow-xl z-50">
                                        KK sudah diselesaikan secara permanen. Undo tidak tersedia.
                                    </div>
                                </div>
                            ) : (
                                <button 
                                    onClick={handleAction}
                                    className={cn(
                                        "flex items-center px-6 py-3 rounded-xl text-[10px] sm:text-xs font-black text-white transition-all shadow-lg active:scale-95 uppercase tracking-widest",
                                        isSoftDelete 
                                            ? "bg-orange-500 hover:bg-orange-400 shadow-orange-900/20" 
                                            : "bg-rose-500 hover:bg-rose-400 shadow-rose-900/20"
                                    )}
                                >
                                    {isSoftDelete ? <RotateCcw className="w-4 h-4 mr-2" /> : <XCircle className="w-4 h-4 mr-2" />}
                                    {isPembaruanKK ? 'UNDO PEMBARUAN KK' : isSoftDelete ? 'UNDO MUTASI' : 'CANCEL MUTASI'}
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {/* Left Column: Resident Info Card */}
                    <div className="lg:col-span-1">
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden sticky top-8 animate-in slide-in-from-left-4 duration-700">
                            <div className="p-10 text-center border-b border-gray-50 bg-gradient-to-b from-gray-50 to-white">
                                <div className="w-28 h-28 bg-white rounded-3xl flex items-center justify-center mx-auto mb-6 border border-gray-100 shadow-xl relative group">
                                    <div className="absolute inset-0 bg-green-500/0 group-hover:bg-green-500/5 transition-colors rounded-3xl"></div>
                                    <User className="w-14 h-14 text-gray-200 group-hover:text-green-200 transition-colors" />
                                </div>
                                <h2 className="text-xl font-black text-gray-950 uppercase tracking-tighter italic leading-tight">
                                    {mutasi.penduduk?.nama || 'Data Terhapus'}
                                </h2>
                                <p className="text-xs font-mono font-bold text-gray-400 mt-2 bg-gray-50 inline-block px-3 py-1 rounded-full border border-gray-100">
                                    {mutasi.penduduk?.nik || '-'}
                                </p>
                                
                                <div className="mt-8 flex flex-wrap justify-center gap-2">
                                    <span className={cn(
                                        "px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm",
                                        mutasi.jenis_mutasi === 'kematian' ? "bg-red-50 text-red-600 border border-red-100" :
                                        mutasi.jenis_mutasi === 'kelahiran' ? "bg-blue-50 text-blue-600 border border-blue-100" :
                                        mutasi.jenis_mutasi === 'pindah_masuk' ? "bg-green-50 text-green-600 border border-green-100" :
                                        mutasi.jenis_mutasi === 'pindah_keluar' ? "bg-orange-50 text-orange-600 border border-orange-100" :
                                        mutasi.jenis_mutasi === 'pindah_rt_rw' ? "bg-purple-50 text-purple-600 border border-purple-100" :
                                        "bg-teal-50 text-teal-600 border border-teal-100"
                                    )}>
                                        {mutasi.jenis_mutasi_label}
                                    </span>
                                    <span className="px-4 py-1.5 bg-gray-50 text-gray-500 border border-gray-100 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                                        {mutasi.kategori_mutasi?.replace('_', ' ')}
                                    </span>
                                </div>
                            </div>
                            <div className="p-10 space-y-8">
                                <div className="grid grid-cols-2 gap-6">
                                    <DataItem label="Jenis Kelamin" value={mutasi.penduduk?.jenis_kelamin} />
                                    <DataItem label="Agama" value={mutasi.penduduk?.agama} />
                                </div>
                                <DataItem label="Status Kawin" value={mutasi.penduduk?.status_perkawinan} />
                                <DataItem label="Pekerjaan" value={mutasi.penduduk?.pekerjaan} />
                                
                                <div className="pt-8 border-t border-gray-50">
                                    <DataItem label="Alamat Domisili" value={mutasi.penduduk?.alamat_lengkap} />
                                </div>
                                
                                {mutasi.penduduk && (
                                    <Link 
                                        href={route('penduduk.show', mutasi.penduduk.id)}
                                        className="flex items-center justify-center gap-3 w-full py-4 bg-gray-950 text-white rounded-[24px] text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all active:scale-95 shadow-xl shadow-black/10 group"
                                    >
                                        PROFIL LENGKAP
                                        <ChevronRight className="w-4 h-4 transition-transform group-hover:translate-x-1" />
                                    </Link>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Mutation Specifics & Documents */}
                    <div className="lg:col-span-2 space-y-8 animate-in slide-in-from-right-4 duration-700">
                        {renderMutasiSpecifics()}

                        {/* Documents Section */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-8 md:p-10">
                            <div className="flex items-center gap-4 mb-8 pb-4 border-b border-gray-50">
                                <div className="p-2 bg-gray-50 rounded-xl">
                                    <FileText className="w-6 h-6 text-gray-400" />
                                </div>
                                <h3 className="text-lg font-black text-gray-900 uppercase tracking-tight italic">Arsip Dokumen</h3>
                            </div>
                            
                            {mutasi.dokumen_pendukung ? (
                                <div className="p-6 bg-gradient-to-r from-gray-50 to-white rounded-3xl border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-6 hover:shadow-lg transition-shadow">
                                    <div className="flex items-center gap-5">
                                        <div className="w-16 h-16 bg-white rounded-2xl flex items-center justify-center border border-gray-100 shadow-sm relative overflow-hidden group">
                                            <div className="absolute inset-0 bg-blue-500/0 group-hover:bg-blue-500/5 transition-colors"></div>
                                            <FileText className="w-8 h-8 text-blue-600" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-black text-gray-950 uppercase tracking-tighter">Scan Dokumen Pendukung</p>
                                            <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Lampiran Terverifikasi</p>
                                        </div>
                                    </div>
                                    <a 
                                        href={`/storage/${mutasi.dokumen_pendukung}`}
                                        target="_blank"
                                        className="w-full sm:w-auto px-10 py-4 bg-white border border-gray-200 rounded-[20px] text-[10px] font-black text-gray-600 hover:text-blue-600 hover:border-blue-200 transition-all shadow-md active:scale-95 uppercase tracking-widest text-center"
                                    >
                                        DOWNLOAD FILE
                                    </a>
                                </div>
                            ) : (
                                <div className="py-20 border-2 border-dashed border-gray-100 rounded-3xl flex flex-col items-center justify-center text-center bg-gray-50/30">
                                    <div className="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-inner">
                                        <XCircle className="w-10 h-10 text-gray-100" />
                                    </div>
                                    <h4 className="text-sm font-black text-gray-400 uppercase tracking-[0.2em]">Tidak Ada Lampiran</h4>
                                    <p className="text-[10px] text-gray-300 font-bold uppercase mt-2">Dokumen fisik tidak diunggah ke sistem</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
