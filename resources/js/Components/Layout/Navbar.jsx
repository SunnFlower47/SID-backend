import React, { useState, useRef, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Bell,
    Search,
    User,
    LogOut,
    Settings as SettingsIcon,
    History,
    UserRound,
    Menu,
    AlignLeft,
    X,
    ChevronRight,
    Mail,
    Clock
} from 'lucide-react';
import { cn } from '@/lib/utils';
import CommandPalette from './CommandPalette';
import axios from 'axios';

export default function Navbar({ toggleMobileSidebar, toggleDesktopSidebar, sidebarCollapsed }) {
    const { auth } = usePage().props;
    const [showProfile, setShowProfile] = useState(false);
    const [showNotifications, setShowNotifications] = useState(false);
    const [isSearchOpen, setIsSearchOpen] = useState(false);

    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loadingNotifications, setLoadingNotifications] = useState(false);

    const profileRef = useRef(null);
    const notificationRef = useRef(null);

    // Fetch Notifications
    const fetchNotifications = async () => {
        try {
            setLoadingNotifications(true);
            const response = await axios.get(route('notifications.index'));
            // Based on Laravel controller structure - Fix data access path
            setNotifications(response.data.data?.notifications || []);
            setUnreadCount(response.data.data?.unread_count || 0);
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        } finally {
            setLoadingNotifications(false);
        }
    };

    useEffect(() => {
        fetchNotifications();

        // Polling every 2 minutes
        const interval = setInterval(fetchNotifications, 120000);

        const handleClickOutside = (event) => {
            if (profileRef.current && !profileRef.current.contains(event.target)) setShowProfile(false);
            if (notificationRef.current && !notificationRef.current.contains(event.target)) setShowNotifications(false);
        };

        const handleKeyDown = (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                setIsSearchOpen(true);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        window.addEventListener('keydown', handleKeyDown);

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            window.removeEventListener('keydown', handleKeyDown);
            clearInterval(interval);
        };
    }, []);

    const markAsRead = async (id) => {
        try {
            await axios.post(route('notifications.mark-read'), { id });
            setNotifications(prev => prev.map(n => n.id === id ? { ...n, read_at: new Date() } : n));
            setUnreadCount(prev => Math.max(0, prev - 1));
        } catch (error) {
            console.error('Failed to mark notification as read');
        }
    };

    return (
        <>
            <CommandPalette isOpen={isSearchOpen} onClose={() => setIsSearchOpen(false)} />

            <header className="bg-white border-b border-gray-200 h-20 px-4 md:px-8 flex items-center justify-between shrink-0 sticky top-0 z-40 transition-all duration-300">
                {/* Left side - Menu Toggles */}
                <div className="flex items-center gap-3 md:gap-4">
                    {/* Mobile Toggle */}
                    <button 
                        onClick={toggleMobileSidebar}
                        className="lg:hidden p-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-2xl transition-all duration-200 bg-gray-50/80 border border-gray-100 shadow-sm"
                    >
                        <Menu className="w-6 h-6" />
                    </button>


                    
                    <div className="lg:hidden w-10 h-10 bg-white p-0.5 rounded-xl flex items-center justify-center shadow-md shrink-0">
                        <img src="/assets/images/logo-desa-cibatu.png" alt="Logo" className="w-full h-full object-contain" />
                    </div>

                    {/* Branding - Only visible when sidebar is collapsed on desktop */}
                    {sidebarCollapsed && (
                        <div className="hidden lg:block animate-in fade-in slide-in-from-left-2 duration-500">
                            <h2 className="text-sm font-black text-gray-900 tracking-tighter uppercase italic leading-none">Sistem Informasi Desa</h2>
                            <p className="text-[10px] font-bold text-green-600 uppercase tracking-[0.2em] mt-0.5 leading-none">Cibatu • Purwakarta</p>
                        </div>
                    )}
                </div>

                {/* Right side - Search, Notifications, Profile */}
                <div className="flex items-center gap-2 sm:gap-4">
                    {/* Smart Search Bar */}
                    <div
                        onClick={() => setIsSearchOpen(true)}
                        className="hidden sm:flex items-center bg-gray-50 rounded-full px-6 py-2.5 border border-transparent hover:border-green-200 hover:bg-white cursor-pointer transition-all group w-64 lg:w-[400px] mr-2 shadow-sm shrink-0"
                    >
                        <Search className="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors shrink-0" />
                        <span className="text-[11px] font-black px-3 text-gray-400 uppercase tracking-tighter italic flex-1 whitespace-nowrap overflow-hidden text-ellipsis">Cari data warga, menu, atau surat...</span>
                        <span className="hidden lg:block text-[9px] font-black text-gray-400 bg-white px-2 py-1 rounded-lg shadow-sm border border-gray-100 shrink-0">⌘K</span>
                    </div>

                    <button 
                        onClick={() => setIsSearchOpen(true)}
                        className="sm:hidden p-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-2xl transition-all duration-200 bg-gray-50/80 border border-gray-100 shadow-sm"
                    >
                        <Search className="w-5 h-5" />
                    </button>

                    {/* Notifications */}
                    <div className="relative" ref={notificationRef}>
                        <button
                            onClick={() => {
                                setShowNotifications(!showNotifications);
                                if (!showNotifications) fetchNotifications();
                            }}
                            className={cn(
                                "p-3 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-2xl transition-all duration-200 relative bg-gray-50/80 border border-gray-100 shadow-sm",
                                showNotifications && "bg-white border-green-200 text-green-600 shadow-md"
                            )}
                        >
                            <Bell className="w-5 h-5" />
                            {unreadCount > 0 && (
                                <span className="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-[10px] font-black text-white flex items-center justify-center rounded-full animate-pulse border-2 border-white shadow-sm z-10">
                                    {unreadCount > 9 ? '9+' : unreadCount}
                                </span>
                            )}
                        </button>

                        {showNotifications && (
                            <div className="fixed sm:absolute top-20 sm:top-full left-4 right-4 sm:left-auto sm:right-0 mt-3 sm:w-96 bg-white rounded-[32px] shadow-2xl border border-gray-100 overflow-hidden z-[60] animate-in slide-in-from-top-2 duration-300">
                                <div className="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                                    <h3 className="text-sm font-black text-gray-950 uppercase tracking-widest italic">Notifikasi</h3>
                                    {unreadCount > 0 && (
                                        <span className="text-[9px] font-black bg-red-100 text-red-600 px-2.5 py-1 rounded-full uppercase">
                                            {unreadCount} BARU
                                        </span>
                                    )}
                                </div>
                                <div className="overflow-y-auto custom-scrollbar" style={{ maxHeight: '280px' }}>
                                    {loadingNotifications ? (
                                        <div className="py-12 flex flex-col items-center justify-center gap-3">
                                            <div className="w-6 h-6 border-2 border-green-600 border-t-transparent rounded-full animate-spin" />
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Memuat...</p>
                                        </div>
                                    ) : notifications.length > 0 ? (
                                        <div className="divide-y divide-gray-50">
                                            {notifications.map((notif, i) => (
                                                <div
                                                    key={i}
                                                    onClick={() => markAsRead(notif.id)}
                                                    className={cn(
                                                        "p-4 hover:bg-gray-50 transition-colors cursor-pointer flex gap-4 items-start group",
                                                        !notif.read_at ? "bg-green-50/20" : ""
                                                    )}
                                                >
                                                    <div className={cn(
                                                        "w-10 h-10 rounded-xl shrink-0 flex items-center justify-center shadow-sm",
                                                        notif.type?.includes('surat') ? "bg-blue-100 text-blue-600" : "bg-orange-100 text-orange-600"
                                                    )}>
                                                        <History className="w-5 h-5" />
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-xs font-black text-gray-950 uppercase tracking-tighter">{notif.title || 'Informasi Sistem'}</p>
                                                        <p className="text-[11px] font-medium text-gray-500 mt-0.5 line-clamp-2 leading-relaxed">{notif.message}</p>
                                                        <p className="text-[9px] font-black text-gray-400 mt-2 flex items-center gap-2">
                                                            <Clock className="w-3 h-3 text-green-600" />
                                                            <span>{notif.time || notif.created_at_label || 'Baru saja'}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <div className="py-16 text-center">
                                            <Bell className="w-12 h-12 text-gray-100 mx-auto mb-4" />
                                            <p className="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Belum ada notifikasi</p>
                                        </div>
                                    )}
                                </div>
                                <div className="p-4 bg-gray-50 border-t border-gray-100">
                                    <Link
                                        href={route('notifications.index')}
                                        onClick={() => setShowNotifications(false)}
                                        className="text-[10px] font-black text-green-600 uppercase tracking-widest hover:text-green-700 block w-full text-center py-2"
                                    >
                                        LIHAT SEMUA
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Profile */}
                    <div className="relative" ref={profileRef}>
                        <div
                            onClick={() => setShowProfile(!showProfile)}
                            className={cn(
                                "flex items-center rounded-2xl transition-all duration-200 cursor-pointer border-2 overflow-hidden",
                                showProfile 
                                    ? "bg-white border-green-200 shadow-md scale-[1.02]" 
                                    : "bg-gray-50/80 border-gray-100 hover:bg-white hover:border-green-100 shadow-sm",
                                "p-1 md:p-1.5 md:pr-4"
                            )}
                        >
                            <div className="w-9 h-9 md:w-10 md:h-10 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg shadow-green-100 shrink-0">
                                <User className="w-5 h-5 text-white" />
                            </div>
                            <div className="hidden md:block text-left ml-3">
                                <p className="text-sm font-black text-gray-950 leading-none tracking-tighter uppercase italic drop-shadow-sm">{auth?.user?.name || 'ADMIN DESA'}</p>
                                <p className="text-[9px] text-green-600 mt-1 uppercase font-black tracking-widest leading-none">Verified Admin</p>
                            </div>
                        </div>

                        {showProfile && (
                            <div className="fixed sm:absolute top-20 sm:top-full left-4 right-4 sm:left-auto sm:right-0 mt-3 sm:w-72 bg-white rounded-[32px] shadow-2xl border border-gray-100 overflow-hidden z-[60] animate-in slide-in-from-top-2 duration-300">
                                <div className="px-6 py-10 bg-white border-b border-gray-50 relative overflow-hidden">
                                    <div className="absolute right-0 top-0 w-32 h-32 bg-green-50 rounded-full blur-3xl -mr-16 -mt-16 opacity-40" />
                                    <div className="relative z-10">
                                        <h3 className="text-xl font-black tracking-tighter uppercase italic text-gray-950 leading-tight">
                                            {auth?.user?.name || 'ADMIN DESA'}
                                        </h3>
                                        <p className="text-[11px] text-green-600 truncate uppercase font-black tracking-widest mt-2">
                                            {auth?.user?.email || '-'}
                                        </p>
                                    </div>
                                </div>
                                <div className="py-4 px-2">
                                    <MenuLink
                                        href={route('profile.edit')}
                                        icon={<UserRound />}
                                        title="Edit Profil"
                                        desc="Kelola identitas Anda"
                                        color="green"
                                    />
                                    <MenuLink
                                        href={route('settings.index')}
                                        icon={<SettingsIcon />}
                                        title="Pengaturan"
                                        desc="Konfigurasi dashboard"
                                        color="blue"
                                    />
                                    <div className="h-px bg-gray-50 my-2 mx-4"></div>
                                    <Link
                                        href={route('logout')}
                                        method="post"
                                        as="button"
                                        className="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-red-600 hover:bg-red-50 transition-all group text-left"
                                    >
                                        <div className="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                            <LogOut className="w-5 h-5" />
                                        </div>
                                        <div>
                                            <p className="text-sm font-black uppercase tracking-tighter">KELUAR SISTEM</p>
                                            <p className="text-[10px] font-bold text-red-400 uppercase tracking-widest">Akhiri sesi ini</p>
                                        </div>
                                    </Link>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </header>
        </>
    );
}

function MenuLink({ href, icon, title, desc, color }) {
    const colors = {
        green: "text-green-600 bg-green-50 group-hover:bg-green-100",
        blue: "text-blue-600 bg-blue-50 group-hover:bg-blue-100",
    };

    return (
        <Link href={href} className="w-full flex items-center gap-4 px-5 py-4 rounded-2xl text-gray-700 hover:bg-gray-50 transition-all group">
            <div className={cn("w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-all", colors[color])}>
                {React.cloneElement(icon, { className: "w-5 h-5" })}
            </div>
            <div className="flex-1">
                <p className="text-sm font-black text-gray-950 uppercase tracking-tighter group-hover:text-green-700 transition-colors">{title}</p>
                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{desc}</p>
            </div>
            <ChevronRight className="w-4 h-4 text-gray-200 group-hover:text-gray-400 transition-all group-hover:translate-x-1" />
        </Link>
    );
}
