import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Swal from 'sweetalert2';
import { MessageSquare, ArrowLeft, Edit, MapPin, User, Mail, Phone, Hash, Clock, CheckCircle, AlertTriangle, XCircle, FileText, Image as ImageIcon, X } from 'lucide-react';

// Shared Components
import { PageHeader, FormCard, Badge, InfoRow } from '@/Components/Shared';

const PRIORITY_COLORS = {
    rendah: { color: 'green', pulse: false },
    sedang: { color: 'yellow', pulse: false },
    tinggi: { color: 'orange', pulse: false },
    darurat: { color: 'red', pulse: true },
};

const STATUS_COLORS = {
    baru: { color: 'blue', icon: AlertTriangle },
    diproses: { color: 'purple', icon: Clock },
    selesai: { color: 'green', icon: CheckCircle },
    ditolak: { color: 'gray', icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.baru;
    return <Badge color={cfg.color} icon={cfg.icon}>{status}</Badge>;
}

function PriorityBadge({ prioritas }) {
    const cfg = PRIORITY_COLORS[prioritas] || PRIORITY_COLORS.rendah;
    return <Badge color={cfg.color} size="sm" pulse={cfg.pulse}>{prioritas}</Badge>;
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
                <PageHeader 
                    title="Detail Pengaduan"
                    subtitle="Lihat detail aduan dari masyarakat"
                    icon={MessageSquare}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('pengaduan.index'),
                            variant: 'ghost'
                        },
                        ...(!(pengaduan.status === 'selesai' || pengaduan.status === 'ditolak') ? [{
                            label: 'EDIT / TANGGAPI',
                            icon: Edit,
                            href: route('pengaduan.edit', pengaduan.id),
                            variant: 'white'
                        }] : [])
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6 sm:space-y-8">
                        {/* Info Utama */}
                        <FormCard icon={FileText} title="Detail Aduan">
                            <div className="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                                <div>
                                    <h2 className="text-2xl font-black text-gray-900 leading-snug mb-3">{pengaduan.judul}</h2>
                                    <div className="flex flex-wrap gap-2 items-center">
                                        <StatusBadge status={pengaduan.status} />
                                        <PriorityBadge prioritas={pengaduan.prioritas} />
                                        <Badge color="gray" size="sm">{pengaduan.kategori}</Badge>
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
                        </FormCard>
                    </div>

                        {/* Form Tanggapan Langsung - Hanya muncul jika belum selesai/ditolak */}
                        {!(pengaduan.status === 'selesai' || pengaduan.status === 'ditolak') && (
                            <FormCard icon={MessageSquare} title="Berikan Tanggapan & Kirim Email">
                                
                                <form onSubmit={(e) => {
                                    e.preventDefault();
                                    const formData = new FormData(e.target);
                                    const data = {
                                        tanggapan: formData.get('tanggapan'),
                                        status: formData.get('status'),
                                        prioritas: pengaduan.prioritas,
                                        judul: pengaduan.judul,
                                        kategori: pengaduan.kategori,
                                        nama_pelapor: pengaduan.nama_pelapor,
                                        nik_pelapor: pengaduan.nik_pelapor,
                                        telepon: pengaduan.telepon,
                                        email: pengaduan.email,
                                        alamat: pengaduan.alamat
                                    };
                                    
                                    router.put(route('pengaduan.update', pengaduan.id), data, {
                                        onSuccess: () => {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'BERHASIL!',
                                                text: 'Tanggapan telah disimpan dan email balasan telah dikirim.',
                                                timer: 3000,
                                                showConfirmButton: false,
                                                customClass: { popup: 'rounded-3xl' }
                                            });
                                        }
                                    });
                                }} className="space-y-5">
                                    <div>
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Pilih Status Terbaru</label>
                                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                            {['baru', 'diproses', 'selesai', 'ditolak'].map((s) => (
                                                <label key={s} className={`
                                                    relative flex items-center justify-center p-3 rounded-2xl border-2 cursor-pointer transition-all active:scale-95
                                                    ${pengaduan.status === s ? 'border-green-600 bg-green-50 shadow-inner' : 'border-gray-100 bg-white hover:border-gray-200'}
                                                `}>
                                                    <input type="radio" name="status" value={s} defaultChecked={pengaduan.status === s} className="sr-only" />
                                                    <span className={`text-[10px] font-black uppercase tracking-wider ${pengaduan.status === s ? 'text-green-700' : 'text-gray-400'}`}>{s}</span>
                                                </label>
                                            ))}
                                        </div>
                                    </div>

                                    <div>
                                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Isi Tanggapan / Balasan Email</label>
                                        <textarea 
                                            name="tanggapan" 
                                            rows="4" 
                                            defaultValue={pengaduan.tanggapan}
                                            placeholder="Ketik tanggapan resmi dari desa di sini..."
                                            className="w-full px-5 py-4 bg-gray-50 border-none focus:ring-2 focus:ring-green-500 rounded-2xl text-sm font-medium text-gray-700 placeholder:text-gray-300 transition-all shadow-inner"
                                            required
                                        ></textarea>
                                    </div>

                                    <div className="flex items-center justify-between gap-4 pt-2">
                                        <div className="flex items-center gap-2 text-gray-400">
                                            <Mail className="w-4 h-4" />
                                            <span className="text-[10px] font-bold uppercase tracking-widest">
                                                {pengaduan.email ? `Akan dikirim ke: ${pengaduan.email}` : 'Email tidak tersedia'}
                                            </span>
                                        </div>
                                        <button
                                            type="submit"
                                            className="px-8 py-3.5 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-green-100 transition-all hover:scale-105 active:scale-95 flex items-center gap-2"
                                        >
                                            <CheckCircle className="w-4 h-4" />
                                            SIMPAN & KIRIM EMAIL
                                        </button>
                                    </div>
                                </form>
                            </FormCard>
                        )}

                    {/* Sidebar */}
                    <div className="space-y-6 sm:space-y-8">
                        {/* Info Pelapor */}
                        <FormCard icon={User} title="Informasi Pelapor" bodyClass="p-6">
                            <InfoRow icon={User} label="Nama Lengkap" value={pengaduan.nama_pelapor} />
                            <InfoRow icon={Hash} label="NIK" value={pengaduan.nik_pelapor} isMono />
                            <InfoRow icon={Phone} label="Telepon" value={pengaduan.telepon} />
                            <InfoRow icon={Mail} label="Email" value={pengaduan.email} />
                            <InfoRow icon={MapPin} label="Alamat" value={pengaduan.alamat} />
                        </FormCard>

                        {/* Admin Penanggung Jawab */}
                        {pengaduan.user && (
                            <FormCard icon={User} title="Admin Penanggung Jawab" bodyClass="p-6">
                                <div className="flex items-center gap-4">
                                    <div className="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shrink-0">
                                        <User className="w-6 h-6" />
                                    </div>
                                    <div>
                                        <p className="font-black text-gray-900 text-sm">{pengaduan.user.name}</p>
                                        <p className="text-xs font-bold text-gray-400 mt-0.5">{pengaduan.user.email}</p>
                                    </div>
                                </div>
                            </FormCard>
                        )}

                        {/* Timeline Ringkas */}
                        <FormCard icon={Clock} title="Timeline" bodyClass="p-6">
                            <div className="space-y-5">
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
                        </FormCard>
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
