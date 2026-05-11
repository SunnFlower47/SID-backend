import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Store, ArrowLeft, MapPin, 
    CheckCircle, XCircle, FileText, 
    Calendar, User, ShieldCheck,
    Star, Mail, Phone, Users,
    LayoutGrid, Image as ImageIcon
} from 'lucide-react';
import { cn } from '@/lib/utils';

export default function Show({ auth, umkm }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Detail UMKM: ${umkm.nama_usaha}`}>
            <Head title={`Detail UMKM: ${umkm.nama_usaha} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden text-left">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 text-left">
                        <div className="flex items-center space-x-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0 text-left">
                                <Store className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300 text-left" />
                            </div>
                            <div className="text-left">
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">{umkm.nama_usaha}</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Informasi Lengkap Potensi Ekonomi Desa</p>
                            </div>
                        </div>
                        <Link 
                            href={route('umkm.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg text-left"
                        >
                            <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                            KEMBALI
                        </Link>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 text-left">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6 text-left">
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden text-left">
                            {/* Gallery Header */}
                            {Array.isArray(umkm.foto_usaha) && umkm.foto_usaha.length > 0 && (
                                <div className="grid grid-cols-2 md:grid-cols-3 gap-1 p-1 bg-gray-50 text-left">
                                    {umkm.foto_usaha.map((foto, i) => (
                                        <div key={i} className={cn("overflow-hidden", i === 0 ? "col-span-2 row-span-2 aspect-video" : "aspect-square")}>
                                            <img src={`/storage/${foto}`} className="w-full h-full object-cover hover:scale-110 transition-all duration-500 text-left" />
                                        </div>
                                    ))}
                                </div>
                            )}

                            <div className="p-8 sm:p-10 text-left">
                                <div className="flex flex-wrap items-center justify-between gap-4 mb-8 text-left">
                                    <div className="flex items-center gap-4 text-left">
                                        <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-left">
                                            <LayoutGrid className="w-6 h-6 text-green-600" />
                                        </div>
                                        <div className="text-left">
                                            <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Profil Usaha</h3>
                                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Deskripsi & Aktivitas Bisnis</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-2 text-left">
                                        {umkm.is_unggulan && (
                                            <div className="px-4 py-2 bg-orange-100 text-orange-700 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 text-left">
                                                <Star className="w-4 h-4 fill-current" /> UNGGULAN
                                            </div>
                                        )}
                                        {umkm.is_verified && (
                                            <div className="px-4 py-2 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 text-left">
                                                <ShieldCheck className="w-4 h-4" /> VERIFIED
                                            </div>
                                        )}
                                        <div className={cn(
                                            "px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2 text-left",
                                            umkm.status_usaha === 'aktif' ? "bg-green-100 text-green-700" : "bg-gray-100 text-gray-500"
                                        )}>
                                            {umkm.status_usaha === 'aktif' ? <CheckCircle className="w-4 h-4" /> : <XCircle className="w-4 h-4" />}
                                            {umkm.status_usaha.toUpperCase()}
                                        </div>
                                    </div>
                                </div>

                                <div className="prose prose-sm max-w-none text-left">
                                    <p className="text-gray-600 font-bold leading-relaxed whitespace-pre-line text-left">
                                        {umkm.deskripsi_usaha || 'Tidak ada deskripsi tambahan untuk usaha ini.'}
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10 pt-10 border-t border-gray-50 text-left">
                                    <div className="space-y-4 text-left">
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-left">
                                                <User className="w-5 h-5 text-purple-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Pemilik Usaha</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">{umkm.nama_pemilik}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-left text-left">
                                                <Mail className="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Email Bisnis</p>
                                                <p className="text-xs font-black text-gray-900 text-left">{umkm.email || '-'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-4 text-left">
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-left">
                                                <Phone className="w-5 h-5 text-green-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">WhatsApp / Telepon</p>
                                                <p className="text-xs font-black text-gray-900 text-left">{umkm.no_telepon || '-'}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-left text-left">
                                                <Calendar className="w-5 h-5 text-orange-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Berdiri Sejak</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">
                                                    {umkm.tanggal_berdiri ? new Date(umkm.tanggal_berdiri).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-'}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Sidebar Info */}
                    <div className="space-y-6 text-left">
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 text-left">
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter mb-6 flex items-center gap-3 text-left">
                                <MapPin className="w-5 h-5 text-red-600" />
                                Lokasi Usaha
                            </h3>
                            <div className="p-4 bg-gray-50 rounded-2xl mb-4 text-left">
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-left">Wilayah</p>
                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">DUSUN {umkm.dusun?.nama || 'PUSAT'}</p>
                            </div>
                            <div className="p-4 bg-gray-50 rounded-2xl text-left">
                                <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 text-left">Alamat Lengkap</p>
                                <p className="text-xs font-bold text-gray-600 leading-relaxed text-left">
                                    {umkm.alamat_usaha}
                                </p>
                            </div>
                        </div>

                        <div className="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] shadow-xl p-8 text-white text-left">
                            <div className="flex items-center gap-4 mb-6 text-left">
                                <div className="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/10 text-left">
                                    <Users className="w-5 h-5 text-yellow-400" />
                                </div>
                                <h3 className="text-sm font-black uppercase italic tracking-tighter text-left">Statistik Bisnis</h3>
                            </div>
                            <div className="space-y-4 text-left">
                                <div className="flex justify-between items-center text-left">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Karyawan</span>
                                    <span className="text-xs font-black uppercase italic text-yellow-400">{umkm.jumlah_karyawan} ORANG</span>
                                </div>
                                <div className="flex justify-between items-center text-left">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis Usaha</span>
                                    <span className="text-xs font-black uppercase italic">{umkm.jenis_usaha.toUpperCase()}</span>
                                </div>
                            </div>
                            
                            <div className="mt-8 pt-8 border-t border-white/10 text-left">
                                <div className="flex items-center gap-4 mb-4 text-left">
                                    <ShieldCheck className="w-5 h-5 text-blue-400" />
                                    <span className="text-[10px] font-black uppercase tracking-widest text-left">Legalitas</span>
                                </div>
                                <p className="text-[10px] font-bold text-gray-400 leading-relaxed text-left">
                                    {umkm.is_verified ? "Usaha ini telah divalidasi oleh Pemerintah Desa." : "Usaha ini sedang dalam proses verifikasi dokumen."}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
