import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { MessageSquare, ArrowLeft, Edit, User, Mail, Phone, MapPin, Star, Clock, CheckCircle, XCircle, Trash2 } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Show({ auth, testimoni }) {
    const formatDateTime = (dateStr) => {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return `${d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}, ${d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}`;
    };

    const handleStatusUpdate = (status) => {
        const title = status === 'approved' ? 'Setujui Testimoni?' : 'Tolak Testimoni?';
        const color = status === 'approved' ? '#10b981' : '#f59e0b';
        
        Swal.fire({
            title: title,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: color,
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('testimoni.update-status', testimoni.id), { status }, {
                    onSuccess: () => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Status testimoni telah diperbarui.',
                            timer: 1500,
                            showConfirmButton: false,
                            customClass: { popup: 'rounded-2xl' }
                        });
                    }
                });
            }
        });
    };

    const handleDelete = () => {
        Swal.fire({
            title: 'Hapus Testimoni?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('testimoni.destroy', testimoni.id));
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Testimoni">
            <Head title={`Testimoni: ${testimoni.nama}`} />

            <div className="space-y-6 sm:space-y-8 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Detail Testimoni"
                    subtitle={`ID: #${testimoni.id.toString().padStart(5, '0')} • ${formatDateTime(testimoni.created_at)}`}
                    icon={MessageSquare}
                    backHref={route('testimoni.index')}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6 sm:space-y-8">
                        {/* Isi Testimoni */}
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden p-6 sm:p-8">
                            <div className="flex items-center justify-between mb-8">
                                <div className="flex gap-1">
                                    {[...Array(5)].map((_, i) => (
                                        <Star key={i} className={`w-5 h-5 ${i < testimoni.rating ? 'fill-orange-400 text-orange-400' : 'text-gray-100'}`} />
                                    ))}
                                </div>
                                <span className={`px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border ${
                                    testimoni.status === 'approved' ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 
                                    testimoni.status === 'pending' ? 'bg-amber-100 text-amber-800 border-amber-200' : 'bg-rose-100 text-rose-800 border-rose-200'
                                }`}>
                                    {testimoni.status}
                                </span>
                            </div>

                            <div className="relative p-8 bg-gray-50/50 rounded-3xl border border-gray-100 italic">
                                <div className="absolute top-4 left-4 text-6xl text-gray-200 font-serif opacity-50">"</div>
                                <p className="text-xl text-gray-700 leading-relaxed relative z-10 text-center">
                                    {testimoni.testimoni}
                                </p>
                                <div className="absolute bottom-4 right-4 text-6xl text-gray-200 font-serif opacity-50 rotate-180">"</div>
                            </div>

                            {/* Action Buttons */}
                            {testimoni.status === 'pending' && (
                                <div className="mt-10 flex flex-wrap gap-4 justify-center">
                                    <button
                                        onClick={() => handleStatusUpdate('approved')}
                                        className="px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-emerald-100 transition-all hover:scale-105 active:scale-95 flex items-center gap-2"
                                    >
                                        <CheckCircle className="w-4 h-4" />
                                        SETUJUI TESTIMONI
                                    </button>
                                    <button
                                        onClick={() => handleStatusUpdate('rejected')}
                                        className="px-8 py-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-100 transition-all hover:scale-105 active:scale-95 flex items-center gap-2"
                                    >
                                        <XCircle className="w-4 h-4" />
                                        TOLAK TESTIMONI
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Sidebar Info */}
                    <div className="space-y-6 sm:space-y-8">
                        <div className="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Profil Pengirim</h3>
                            </div>
                            <div className="p-6 space-y-4">
                                <div className="flex items-center gap-4 py-3 border-b border-gray-50">
                                    <div className="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                                        <User className="w-5 h-5 text-indigo-600" />
                                    </div>
                                    <div className="overflow-hidden">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Lengkap</p>
                                        <p className="text-sm font-bold text-gray-900 truncate">{testimoni.nama}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-4 py-3 border-b border-gray-50">
                                    <div className="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                                        <Mail className="w-5 h-5 text-indigo-600" />
                                    </div>
                                    <div className="overflow-hidden">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</p>
                                        <p className="text-sm font-bold text-gray-900 truncate">{testimoni.email || '—'}</p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-4 py-3 border-b border-gray-50">
                                    <div className="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                                        <Phone className="w-5 h-5 text-indigo-600" />
                                    </div>
                                    <div className="overflow-hidden">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Telepon</p>
                                        <p className="text-sm font-bold text-gray-900">{testimoni.telepon || '—'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Danger Zone */}
                        <div className="bg-rose-50 rounded-[2.5rem] border border-rose-100 p-6">
                            <h4 className="text-[10px] font-black text-rose-600 uppercase tracking-[0.2em] mb-4">Zona Bahaya</h4>
                            <button
                                onClick={handleDelete}
                                className="w-full py-4 bg-white hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2"
                            >
                                <Trash2 className="w-4 h-4" />
                                HAPUS DATA INI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
