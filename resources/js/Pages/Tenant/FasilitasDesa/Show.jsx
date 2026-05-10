import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    Building2, ArrowLeft, MapPin, 
    Clock, Phone, Info, CheckCircle, 
    XCircle, FileText, Calendar
} from 'lucide-react';

export default function Show({ auth, fasilitas }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Detail Fasilitas: ${fasilitas.nama}`}>
            <Head title={`Detail Fasilitas: ${fasilitas.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 text-left">
                        <div className="flex items-center space-x-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0 text-left">
                                <Building2 className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div className="text-left">
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none text-left">{fasilitas.nama}</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic text-left">Detail Informasi Sarana & Prasarana Desa</p>
                            </div>
                        </div>
                        <Link 
                            href={route('fasilitas-desa.index')}
                            className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg"
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
                            {fasilitas.foto && (
                                <div className="aspect-video w-full overflow-hidden">
                                    <img src={`/storage/${fasilitas.foto}`} alt={fasilitas.nama} className="w-full h-full object-cover" />
                                </div>
                            )}
                            <div className="p-8 sm:p-10 text-left">
                                <div className="flex items-center justify-between mb-8 text-left">
                                    <div className="flex items-center gap-4 text-left">
                                        <div className="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-left">
                                            <Building2 className="w-6 h-6 text-green-600" />
                                        </div>
                                        <div className="text-left">
                                            <h3 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter text-left">Deskripsi Fasilitas</h3>
                                            <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none text-left">Gambaran Umum Sarana</p>
                                        </div>
                                    </div>
                                    {fasilitas.status_aktif ? (
                                        <div className="px-4 py-2 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                            <CheckCircle className="w-4 h-4" /> BUKA
                                        </div>
                                    ) : (
                                        <div className="px-4 py-2 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                                            <XCircle className="w-4 h-4" /> TUTUP
                                        </div>
                                    )}
                                </div>

                                <div className="prose prose-sm max-w-none text-left">
                                    <p className="text-gray-600 font-bold leading-relaxed whitespace-pre-line text-left">
                                        {fasilitas.deskripsi || 'Tidak ada deskripsi tambahan untuk fasilitas ini.'}
                                    </p>
                                </div>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10 pt-10 border-t border-gray-50 text-left">
                                    <div className="space-y-4 text-left">
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-left">
                                                <Clock className="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest text-left leading-none mb-1">Jam Operasional</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">{fasilitas.jam_operasional || 'Tidak Diketahui'}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center text-left">
                                                <Phone className="w-5 h-5 text-purple-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest text-left leading-none mb-1">Nomor Kontak</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">{fasilitas.kontak || '-'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-4 text-left">
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-left">
                                                <MapPin className="w-5 h-5 text-red-600" />
                                            </div>
                                            <div className="text-left text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Lokasi Wilayah</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">
                                                    DUSUN {fasilitas.dusun?.nama || 'PUSAT'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4 text-left">
                                            <div className="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-left">
                                                <Calendar className="w-5 h-5 text-orange-600" />
                                            </div>
                                            <div className="text-left">
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1 text-left">Terakhir Diperbarui</p>
                                                <p className="text-xs font-black text-gray-900 uppercase italic text-left">{new Date(fasilitas.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
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
                            <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter mb-6 flex items-center gap-3">
                                <MapPin className="w-5 h-5 text-red-600" />
                                Alamat Lengkap
                            </h3>
                            <div className="p-4 bg-gray-50 rounded-2xl text-left">
                                <p className="text-xs font-bold text-gray-600 leading-relaxed text-left uppercase">
                                    {fasilitas.alamat}
                                </p>
                            </div>
                            
                            {fasilitas.latitude && fasilitas.longitude && (
                                <div className="mt-6 text-left">
                                    <a 
                                        href={`https://www.google.com/maps/search/?api=1&query=${fasilitas.latitude},${fasilitas.longitude}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-full flex items-center justify-center gap-3 px-6 py-4 bg-blue-600 text-white rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-blue-200 hover:scale-[1.02] active:scale-[0.98] transition-all"
                                    >
                                        <MapPin className="w-4 h-4" />
                                        BUKA GOOGLE MAPS
                                    </a>
                                </div>
                            )}
                        </div>

                        <div className="bg-gradient-to-br from-gray-900 to-gray-800 rounded-[2.5rem] shadow-xl p-8 text-white text-left">
                            <div className="flex items-center gap-4 mb-6 text-left">
                                <div className="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/10 text-left">
                                    <Info className="w-5 h-5 text-yellow-400" />
                                </div>
                                <h3 className="text-sm font-black uppercase italic tracking-tighter text-left">Administrasi</h3>
                            </div>
                            <div className="space-y-4 text-left">
                                <div className="flex justify-between items-center text-left">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis</span>
                                    <span className="text-xs font-black uppercase italic text-yellow-400">{fasilitas.jenis.replace('_', ' ')}</span>
                                </div>
                                <div className="flex justify-between items-center text-left">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Dusun</span>
                                    <span className="text-xs font-black uppercase italic">{fasilitas.dusun?.nama || '-'}</span>
                                </div>
                                <div className="flex justify-between items-center text-left">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">RW / RT</span>
                                    <span className="text-xs font-black uppercase italic">{fasilitas.rw?.kode || '-' } / {fasilitas.rt?.kode || '-'}</span>
                                </div>
                            </div>
                            <Link 
                                href={route('fasilitas-desa.edit', fasilitas.id)}
                                className="w-full flex items-center justify-center gap-3 px-6 py-4 bg-white text-gray-900 rounded-xl font-black uppercase tracking-widest text-[10px] mt-8 hover:bg-gray-100 transition-all active:scale-95"
                            >
                                <Edit3 className="w-4 h-4" />
                                EDIT DATA FASILITAS
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
