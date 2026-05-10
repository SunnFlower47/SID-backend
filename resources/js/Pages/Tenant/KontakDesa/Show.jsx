import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Phone, ArrowLeft, Edit, MapPin, Mail, Globe, Share2, MessageSquare, Clock, CheckCircle, XCircle, Info, Building } from 'lucide-react';

export default function Show({ auth, kontak }) {
    return (
        <AuthenticatedLayout user={auth.user} title={`Detail Kontak: ${kontak.nama}`}>
            <Head title={`Detail Kontak: ${kontak.nama} - Admin Panel`} />

            <div className="space-y-6 animate-in fade-in slide-in-from-top-4 duration-700 pb-20 text-left">
                {/* Header */}
                <div className="bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden text-left">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none"></div>
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Info className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Detail Kontak</h1>
                                <p className="text-blue-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">{kontak.nama}</p>
                            </div>
                        </div>
                        <div className="flex gap-2">
                            <Link 
                                href={route('kontak-desa.edit', kontak.id)}
                                className="flex items-center px-6 py-3 bg-white text-blue-700 hover:bg-blue-50 rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest shadow-lg shadow-black/10"
                            >
                                <Edit className="w-3.5 h-3.5 mr-2" />
                                EDIT DATA
                            </Link>
                            <Link 
                                href={route('kontak-desa.index')}
                                className="flex items-center px-6 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all hover:scale-105 uppercase tracking-widest backdrop-blur-md border border-white/10 shadow-lg"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Profile Card */}
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6 flex flex-col items-center text-center">
                        <div className="w-32 h-32 rounded-3xl overflow-hidden bg-gray-50 border-4 border-white shadow-xl">
                            {kontak.foto ? (
                                <img src={`/storage/${kontak.foto}`} className="w-full h-full object-cover" alt={kontak.nama} />
                            ) : (
                                <div className="w-full h-full flex items-center justify-center bg-green-50 text-green-600 font-black text-3xl italic">
                                    {kontak.nama.charAt(0)}
                                </div>
                            )}
                        </div>
                        <div className="space-y-1">
                            <h2 className="text-xl font-black text-gray-900 uppercase italic tracking-tighter">{kontak.nama}</h2>
                            <p className="text-[10px] font-black text-green-600 uppercase tracking-[0.2em]">{kontak.jabatan || kontak.jenis.replace('_', ' ')}</p>
                        </div>
                        <div className="flex gap-2">
                            {kontak.status_aktif ? (
                                <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[9px] font-black uppercase tracking-widest italic flex items-center gap-1.5 border border-green-200">
                                    <CheckCircle className="w-3 h-3" /> AKTIF
                                </span>
                            ) : (
                                <span className="px-3 py-1 bg-gray-100 text-gray-500 rounded-full text-[9px] font-black uppercase tracking-widest italic flex items-center gap-1.5 border border-gray-200">
                                    <XCircle className="w-3 h-3" /> NONAKTIF
                                </span>
                            )}
                            <span className="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[9px] font-black uppercase tracking-widest italic border border-blue-100">
                                #{kontak.urutan}
                            </span>
                        </div>
                        <div className="w-full pt-6 border-t border-gray-50 space-y-3 text-left">
                            <div className="flex items-start gap-3">
                                <Clock className="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                                <div>
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Jam Operasional</p>
                                    <p className="text-xs font-bold text-gray-700 mt-1">{kontak.jam_operasional || 'Tidak diatur'}</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <MapPin className="w-4 h-4 text-gray-400 shrink-0 mt-0.5" />
                                <div>
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Lokasi / Wilayah</p>
                                    <p className="text-xs font-bold text-gray-700 mt-1">
                                        {kontak.alamat}
                                        <br />
                                        <span className="text-[10px] text-gray-400">
                                            {kontak.dusun?.nama || 'Pusat'}, RW {kontak.rw?.kode || '-'}, RT {kontak.rt?.kode || '-'}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Contact Info & Socials */}
                    <div className="lg:col-span-2 space-y-6">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-8">
                            <div>
                                <div className="flex items-center gap-3 mb-6">
                                    <Phone className="w-5 h-5 text-blue-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase tracking-widest italic">Kontak & Informasi</h3>
                                </div>
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                                                <Phone className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Telepon</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1">{kontak.no_telepon || '-'}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600 border border-green-100">
                                                <MessageSquare className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">HP / WhatsApp</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1">{kontak.no_hp || '-'}</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
                                                <Mail className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Email</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1">{kontak.email || '-'}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="space-y-4">
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 border border-orange-100">
                                                <Globe className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Website</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1 truncate max-w-[200px]">
                                                    {kontak.website ? <a href={kontak.website} target="_blank" className="hover:text-orange-600 transition-colors underline decoration-dotted">{kontak.website}</a> : '-'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center text-red-600 border border-red-100">
                                                <Share2 className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Youtube</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1 truncate max-w-[200px]">
                                                    {kontak.youtube ? <a href={kontak.youtube} target="_blank" className="hover:text-red-600 transition-colors underline decoration-dotted">Channel Desa</a> : '-'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600 border border-pink-100">
                                                <Share2 className="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none">Instagram</p>
                                                <p className="text-sm font-bold text-gray-900 mt-1 truncate max-w-[200px]">
                                                    {kontak.instagram ? <a href={kontak.instagram} target="_blank" className="hover:text-pink-600 transition-colors underline decoration-dotted">@profile_desa</a> : '-'}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="pt-8 border-t border-gray-50">
                                <div className="flex items-center gap-3 mb-4">
                                    <Building className="w-5 h-5 text-gray-600" />
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter leading-none">Deskripsi & Catatan Tambahan</h3>
                                </div>
                                <div className="bg-gray-50 rounded-2xl p-6 text-xs font-bold text-gray-600 leading-relaxed italic border border-gray-100">
                                    {kontak.deskripsi || 'Tidak ada deskripsi tambahan yang disediakan.'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
