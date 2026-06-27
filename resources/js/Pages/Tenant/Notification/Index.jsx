import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { Bell, CheckCircle2, FileText, AlertTriangle, ArrowRight, ArrowLeft, X, Clock, ExternalLink, Megaphone } from 'lucide-react';
import { PageHeader } from '@/Components/Shared';
import { cn } from '@/lib/utils';
import axios from 'axios';

export default function NotificationIndex({ notifications, unreadCount, totalCount }) {
    const [selectedNotif, setSelectedNotif] = React.useState(null);

    const handleMarkAsRead = async (id, type) => {
        try {
            if (type !== 'announcement') {
                await axios.post(route('notifications.mark-read'), { id, type });
            }
            // Reload Inertia props to sync unreadCount and list
            router.reload({ only: ['notifications', 'unreadCount'] });
        } catch (error) {
            console.error('Failed to mark as read', error);
        }
    };

    const handleNotifClick = (notif) => {
        setSelectedNotif(notif);
        if (notif.status !== 'selesai' && notif.type !== 'announcement') {
            handleMarkAsRead(notif.id, notif.type);
        }
    };

    const getIcon = (type, iconClass) => {
        if (iconClass) {
            if (iconClass.includes('bullhorn') || iconClass.includes('megaphone')) return <Megaphone className="w-5 h-5 text-indigo-500" />;
            return <i className={cn(iconClass, "text-lg")} />;
        }
        if (type === 'surat') return <FileText className="w-5 h-5 text-blue-500" />;
        if (type === 'pengaduan') return <AlertTriangle className="w-5 h-5 text-yellow-500" />;
        return <Bell className="w-5 h-5 text-gray-500" />;
    };

    const getBgColor = (type, colorClass) => {
        if (colorClass) {
            if (colorClass.includes('red')) return 'bg-red-50 border-red-100';
            if (colorClass.includes('yellow') || colorClass.includes('amber')) return 'bg-amber-50 border-amber-100';
            if (colorClass.includes('green')) return 'bg-green-50 border-green-100';
            if (colorClass.includes('blue')) return 'bg-blue-50 border-blue-100';
        }
        if (type === 'surat') return 'bg-blue-50 border-blue-100';
        if (type === 'pengaduan') return 'bg-yellow-50 border-yellow-100';
        return 'bg-gray-50 border-gray-100';
    };

    return (
        <AuthenticatedLayout title="Pusat Notifikasi">
            <Head title="Notifikasi" />

            <div className="space-y-6 pb-20">
                {/* Header */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <PageHeader
                        title="Pusat Notifikasi"
                        subtitle={`Anda memiliki ${unreadCount} notifikasi baru dari total ${totalCount} aktivitas`}
                        icon={Bell}
                    />
                    
                    <Link
                        href={route('admin.dashboard')}
                        className="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm group"
                    >
                        <ArrowLeft className="w-4 h-4 mr-2 text-gray-400 group-hover:-translate-x-1 transition-transform" />
                        Kembali ke Dashboard
                    </Link>
                </div>

                {/* Content */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <h2 className="text-lg font-black text-gray-900 flex items-center gap-2">
                            <Bell className="w-5 h-5 text-indigo-500" />
                            Aktivitas Terbaru
                        </h2>
                        {unreadCount > 0 && (
                            <span className="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full animate-pulse">
                                {unreadCount} Baru
                            </span>
                        )}
                    </div>

                    <div className="divide-y divide-gray-100">
                        {notifications.length > 0 ? (
                            notifications.map((notif, index) => (
                                <button
                                    key={`${notif.type}-${notif.id}-${index}`}
                                    onClick={() => handleNotifClick(notif)}
                                    className={cn(
                                        "w-full flex items-start gap-4 p-5 text-left transition-all hover:bg-gray-50 group",
                                        notif.status !== 'selesai' && notif.type !== 'announcement' ? "bg-indigo-50/10" : ""
                                    )}
                                >
                                    <div className={cn("w-12 h-12 rounded-2xl flex items-center justify-center border shrink-0", getBgColor(notif.type, notif.color))}>
                                        {getIcon(notif.type, notif.icon)}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center justify-between gap-2 mb-1">
                                            <h4 className="text-sm font-black text-gray-900 truncate">
                                                {notif.title}
                                            </h4>
                                            <span className="text-xs font-medium text-gray-500 shrink-0 flex items-center gap-1">
                                                {notif.time}
                                            </span>
                                        </div>
                                        <div 
                                            className="text-sm text-gray-600 font-medium line-clamp-2 prose prose-slate max-w-none"
                                            dangerouslySetInnerHTML={{ __html: notif.message }}
                                        />
                                        
                                        <div className="mt-2 flex items-center gap-2">
                                            <span className={cn(
                                                "text-[10px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider",
                                                notif.status === 'selesai' || notif.type === 'announcement' ? "bg-green-100 text-green-700" :
                                                notif.status === 'ditolak' ? "bg-red-100 text-red-700" :
                                                notif.status === 'diproses' ? "bg-blue-100 text-blue-700" :
                                                "bg-yellow-100 text-yellow-700"
                                            )}>
                                                {notif.type === 'announcement' ? 'Broadcast' : notif.status}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 transition-colors">
                                        <ArrowRight className="w-4 h-4" />
                                    </div>
                                </button>
                            ))
                        ) : (
                            <div className="p-12 text-center flex flex-col items-center justify-center">
                                <div className="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <CheckCircle2 className="w-10 h-10 text-gray-300" />
                                </div>
                                <h3 className="text-lg font-black text-gray-900">Belum Ada Notifikasi</h3>
                                <p className="text-sm text-gray-500 mt-1 max-w-sm">Anda telah membaca semua aktivitas terbaru. Tidak ada pengajuan surat atau pengaduan baru saat ini.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Notification Detail Modal */}
            {selectedNotif && (
                <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4 overflow-y-auto animate-in fade-in duration-200">
                    <div className="bg-white rounded-3xl shadow-2xl border border-gray-150 max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-200 flex flex-col">
                        {/* Modal Header */}
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between">
                            <div className="flex items-center gap-3">
                                <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center border shrink-0", getBgColor(selectedNotif.type, selectedNotif.color))}>
                                    {getIcon(selectedNotif.type, selectedNotif.icon)}
                                </div>
                                <div>
                                    <h3 className="text-base font-black text-gray-950 uppercase tracking-tight leading-none">{selectedNotif.title}</h3>
                                    <p className="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1">{selectedNotif.type === 'announcement' ? 'Sistem Pusat' : 'Layanan Desa'}</p>
                                </div>
                            </div>
                            <button
                                onClick={() => setSelectedNotif(null)}
                                className="p-2 bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-600 rounded-xl transition-colors cursor-pointer"
                            >
                                <X className="w-4 h-4" />
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-6 space-y-4 flex-1">
                            {/* Meta Info */}
                            <div className="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-bold text-gray-500">
                                <span className="flex items-center gap-1">
                                    <Clock className="w-3.5 h-3.5 text-gray-400" />
                                    {selectedNotif.time}
                                </span>
                                <span className="h-3 w-px bg-gray-200"></span>
                                <span className={cn(
                                    "px-2 py-0.5 rounded-md font-black uppercase tracking-wider text-[9px]",
                                    selectedNotif.status === 'selesai' || selectedNotif.type === 'announcement' ? "bg-green-100 text-green-700" :
                                    selectedNotif.status === 'ditolak' ? "bg-red-100 text-red-700" :
                                    selectedNotif.status === 'diproses' ? "bg-blue-100 text-blue-700" :
                                    "bg-yellow-100 text-yellow-700"
                                )}>
                                    Status: {selectedNotif.type === 'announcement' ? 'Broadcast' : selectedNotif.status}
                                </span>
                            </div>

                            {/* Message Content */}
                            <div className="p-4 bg-gray-50/50 border border-gray-100 rounded-2xl">
                                <div 
                                    className="text-sm text-gray-700 font-medium leading-relaxed prose prose-slate max-w-none text-left"
                                    dangerouslySetInnerHTML={{ __html: selectedNotif.message }}
                                />
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="p-6 border-t border-gray-100 bg-gray-50/50 flex items-center justify-end gap-3">
                            <button
                                onClick={() => setSelectedNotif(null)}
                                className="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl text-xs font-bold transition-all active:scale-95 cursor-pointer"
                            >
                                Tutup
                            </button>
                            {selectedNotif.url && selectedNotif.type !== 'announcement' && (
                                <Link
                                    href={selectedNotif.url}
                                    className="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all active:scale-95 shadow-md shadow-indigo-600/10 cursor-pointer"
                                >
                                    <ExternalLink className="w-3.5 h-3.5" />
                                    Buka Halaman Detail
                                </Link>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
