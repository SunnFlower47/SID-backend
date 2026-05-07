import React, { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { MessageSquare, ArrowLeft, Edit, MapPin, User, Mail, Phone, Hash, Clock, CheckCircle, AlertTriangle, XCircle, FileText, Image as ImageIcon, X } from 'lucide-react';

const PRIORITY_COLORS = {
    rendah: 'bg-green-100 text-green-800',
    sedang: 'bg-yellow-100 text-yellow-800',
    tinggi: 'bg-orange-100 text-orange-800',
    darurat: 'bg-red-100 text-red-800 animate-pulse',
};

const STATUS_COLORS = {
    baru: { bg: 'bg-blue-100', text: 'text-blue-800', icon: AlertTriangle },
    diproses: { bg: 'bg-purple-100', text: 'text-purple-800', icon: Clock },
    selesai: { bg: 'bg-green-100', text: 'text-green-800', icon: CheckCircle },
    ditolak: { bg: 'bg-gray-100', text: 'text-gray-800', icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.baru;
    const Icon = cfg.icon;
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest ${cfg.bg} ${cfg.text}`}>
            <Icon className="w-4 h-4" />
            {status}
        </span>
    );
}

function PriorityBadge({ prioritas }) {
    const color = PRIORITY_COLORS[prioritas] || PRIORITY_COLORS.rendah;
    return (
        <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest ${color}`}>
            {prioritas}
        </span>
    );
}

function InfoRow({ icon: Icon, label, value, isMono }) {
    if (!value) return null;
    return (
        <div className="flex items-start gap-3 py-3 border-b border-gray-50 last:border-0">
            <div className="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center shrink-0 mt-0.5">
                <Icon className="w-4 h-4 text-gray-500" />
            </div>
            <div>
                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{label}</p>
                <p className={`text-sm font-bold text-gray-900 mt-0.5 ${isMono ? 'font-mono' : ''}`}>{value}</p>
            </div>
        </div>
    );
}

export default function Show({ auth, pengaduan }) {
    const [modalImage, setModalImage] = useState(null);

    const formatDateTime = (dateStr) => {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return `${d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}, ${d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Pengaduan">
            <Head title={`Detail Aduan: ${pengaduan.judul}`} />

            <div className="space-y-6 sm:space-y-8 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <MessageSquare className="w-6 h-6 sm:w-7 sm:h-7 text-green-50" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none line-clamp-1">
                                    Detail Pengaduan
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Lihat detail aduan dari masyarakat
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('pengaduan.index')}
                                className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                            <Link
                                href={route('pengaduan.edit', pengaduan.id)}
                                className="flex items-center px-4 py-2.5 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-lg"
                            >
                                <Edit className="w-3.5 h-3.5 mr-2" />
                                EDIT / TANGGAPI
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6 sm:space-y-8">
                        {/* Info Utama */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden p-6 sm:p-8">
                            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                <div>
                                    <h2 className="text-2xl font-black text-gray-900 leading-snug mb-3">{pengaduan.judul}</h2>
                                    <div className="flex flex-wrap gap-2">
                                        <StatusBadge status={pengaduan.status} />
                                        <PriorityBadge prioritas={pengaduan.prioritas} />
                                        <span className="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-gray-100 text-gray-700">
                                            {pengaduan.kategori}
                                        </span>
                                    </div>
                                </div>
                                <div className="text-left sm:text-right shrink-0">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dibuat Pada</p>
                                    <p className="text-sm font-bold text-gray-900 mt-1">{formatDateTime(pengaduan.created_at)}</p>
                                </div>
                            </div>

                            <div className="mb-8">
                                <h3 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <FileText className="w-4 h-4" /> Deskripsi Aduan
                                </h3>
                                <div className="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                                    <p className="text-gray-700 leading-relaxed whitespace-pre-wrap">{pengaduan.deskripsi}</p>
                                </div>
                            </div>

                            {pengaduan.lokasi && (
                                <div className="mb-8">
                                    <h3 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <MapPin className="w-4 h-4" /> Lokasi Kejadian
                                    </h3>
                                    <div className="flex items-center gap-3 p-4 bg-blue-50 text-blue-900 rounded-2xl border border-blue-100 font-medium">
                                        <MapPin className="w-5 h-5 text-blue-600 shrink-0" />
                                        {pengaduan.lokasi}
                                    </div>
                                </div>
                            )}

                            {/* Foto Bukti */}
                            {pengaduan.foto && pengaduan.foto.length > 0 && (
                                <div className="mb-8">
                                    <h3 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <ImageIcon className="w-4 h-4" /> Foto Pendukung
                                    </h3>
                                    <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                                        {pengaduan.foto.map((path, i) => (
                                            <div
                                                key={i}
                                                className="relative aspect-square rounded-2xl overflow-hidden group cursor-pointer border border-gray-200"
                                                onClick={() => setModalImage(`/storage/${path}`)}
                                            >
                                                <img src={`/storage/${path}`} alt={`Foto ${i + 1}`} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                                <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                                    <div className="w-10 h-10 bg-white/30 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity transform scale-50 group-hover:scale-100">
                                                        <ImageIcon className="w-5 h-5 text-white" />
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Tanggapan Admin */}
                            {pengaduan.tanggapan && (
                                <div>
                                    <h3 className="text-xs font-black text-gray-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                        <MessageSquare className="w-4 h-4" /> Tanggapan Desa
                                    </h3>
                                    <div className="bg-green-50 rounded-2xl p-5 border border-green-100 relative">
                                        <div className="absolute top-0 left-0 w-2 h-full bg-green-500 rounded-l-2xl" />
                                        <p className="text-gray-800 leading-relaxed whitespace-pre-wrap ml-2">{pengaduan.tanggapan}</p>
                                        
                                        {pengaduan.tanggal_tanggapan && (
                                            <p className="text-[10px] font-bold text-green-600 uppercase tracking-widest mt-4 ml-2 flex items-center gap-1.5">
                                                <Clock className="w-3 h-3" />
                                                Ditanggapi: {formatDateTime(pengaduan.tanggal_tanggapan)}
                                            </p>
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6 sm:space-y-8">
                        {/* Info Pelapor */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Pelapor</h3>
                            </div>
                            <div className="p-6">
                                <InfoRow icon={User} label="Nama Lengkap" value={pengaduan.nama_pelapor} />
                                <InfoRow icon={Hash} label="NIK" value={pengaduan.nik_pelapor} isMono />
                                <InfoRow icon={Phone} label="Telepon" value={pengaduan.telepon} />
                                <InfoRow icon={Mail} label="Email" value={pengaduan.email} />
                                <InfoRow icon={MapPin} label="Alamat" value={pengaduan.alamat} />
                            </div>
                        </div>

                        {/* Admin Penanggung Jawab */}
                        {pengaduan.user && (
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Admin Penanggung Jawab</h3>
                                </div>
                                <div className="p-6 flex items-center gap-4">
                                    <div className="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shrink-0">
                                        <User className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="font-black text-gray-900 text-sm">{pengaduan.user.name}</p>
                                        <p className="text-xs font-bold text-gray-400 mt-0.5">{pengaduan.user.email}</p>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Timeline Ringkas */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Timeline</h3>
                            </div>
                            <div className="p-6 space-y-5">
                                <div className="flex gap-4">
                                    <div className="flex flex-col items-center">
                                        <div className="w-3 h-3 rounded-full bg-green-500 ring-4 ring-green-50" />
                                        {(pengaduan.status !== 'baru' || pengaduan.tanggal_tanggapan) && (
                                            <div className="w-0.5 h-full bg-gray-100 my-1" />
                                        )}
                                    </div>
                                    <div className="-mt-1.5 pb-2">
                                        <p className="text-xs font-black text-gray-900 uppercase tracking-widest">Aduan Dibuat</p>
                                        <p className="text-[10px] font-bold text-gray-400 mt-0.5">{formatDateTime(pengaduan.created_at)}</p>
                                    </div>
                                </div>
                                
                                {pengaduan.status !== 'baru' && (
                                    <div className="flex gap-4">
                                        <div className="flex flex-col items-center">
                                            <div className="w-3 h-3 rounded-full bg-blue-500 ring-4 ring-blue-50" />
                                            {pengaduan.tanggal_tanggapan && (
                                                <div className="w-0.5 h-full bg-gray-100 my-1" />
                                            )}
                                        </div>
                                        <div className="-mt-1.5 pb-2">
                                            <p className="text-xs font-black text-gray-900 uppercase tracking-widest">Status Diperbarui</p>
                                            <p className="text-[10px] font-bold text-blue-600 mt-0.5">{pengaduan.status}</p>
                                            <p className="text-[10px] font-bold text-gray-400 mt-0.5">{formatDateTime(pengaduan.updated_at)}</p>
                                        </div>
                                    </div>
                                )}

                                {pengaduan.tanggal_tanggapan && (
                                    <div className="flex gap-4">
                                        <div className="flex flex-col items-center">
                                            <div className="w-3 h-3 rounded-full bg-purple-500 ring-4 ring-purple-50" />
                                        </div>
                                        <div className="-mt-1.5">
                                            <p className="text-xs font-black text-gray-900 uppercase tracking-widest">Ditanggapi Admin</p>
                                            <p className="text-[10px] font-bold text-gray-400 mt-0.5">{formatDateTime(pengaduan.tanggal_tanggapan)}</p>
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Image Modal Lightbox */}
            {modalImage && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-gray-900/90 backdrop-blur-sm animate-in fade-in duration-200" onClick={() => setModalImage(null)}>
                    <div className="relative max-w-5xl w-full h-full flex items-center justify-center" onClick={(e) => e.stopPropagation()}>
                        <button 
                            className="absolute top-0 right-0 sm:-right-4 p-2 text-white/70 hover:text-white bg-black/50 hover:bg-black/80 rounded-full transition-all"
                            onClick={() => setModalImage(null)}
                        >
                            <X className="w-6 h-6" />
                        </button>
                        <img src={modalImage} alt="Fullscreen" className="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl" />
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
