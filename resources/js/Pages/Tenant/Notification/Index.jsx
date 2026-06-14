import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { Bell, CheckCircle2, FileText, AlertTriangle, ArrowRight, ArrowLeft } from 'lucide-react';
import { PageHeader } from '@/Components/Shared';
import { cn } from '@/lib/utils';
import axios from 'axios';

export default function NotificationIndex({ notifications, unreadCount, totalCount }) {
    // Reusing the markAsRead logic from Navbar but with a router reload to keep state synced
    const handleMarkAsRead = async (id, type, url) => {
        try {
            await axios.post(route('notifications.mark-read'), { id, type });
            router.visit(url); // Navigate to the item
        } catch (error) {
            console.error('Failed to mark as read', error);
            router.visit(url); // Navigate anyway
        }
    };

    const getIcon = (type) => {
        if (type === 'surat') return <FileText className="w-5 h-5 text-blue-500" />;
        if (type === 'pengaduan') return <AlertTriangle className="w-5 h-5 text-yellow-500" />;
        return <Bell className="w-5 h-5 text-gray-500" />;
    };

    const getBgColor = (type) => {
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
                                    onClick={() => handleMarkAsRead(notif.id, notif.type, notif.url)}
                                    className={cn(
                                        "w-full flex items-start gap-4 p-5 text-left transition-all hover:bg-gray-50 group",
                                        notif.status !== 'selesai' && notif.status !== 'diproses' ? "bg-indigo-50/30" : ""
                                    )}
                                >
                                    <div className={cn("w-12 h-12 rounded-2xl flex items-center justify-center border shrink-0", getBgColor(notif.type))}>
                                        {getIcon(notif.type)}
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
                                        <p className="text-sm text-gray-600 font-medium line-clamp-2">
                                            {notif.message}
                                        </p>
                                        
                                        <div className="mt-2 flex items-center gap-2">
                                            <span className={cn(
                                                "text-[10px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider",
                                                notif.status === 'selesai' ? "bg-green-100 text-green-700" :
                                                notif.status === 'ditolak' ? "bg-red-100 text-red-700" :
                                                notif.status === 'diproses' ? "bg-blue-100 text-blue-700" :
                                                "bg-yellow-100 text-yellow-700"
                                            )}>
                                                {notif.status}
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
        </AuthenticatedLayout>
    );
}
