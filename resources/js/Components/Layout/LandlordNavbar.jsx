import React, { useState, useRef, useEffect } from 'react';
import { Link } from '@inertiajs/react';
import { 
    ChevronLeft, 
    ChevronRight, 
    ChevronDown, 
    Activity, 
    Settings, 
    LogOut 
} from 'lucide-react';
import { cn } from '@/lib/utils';

export default function LandlordNavbar({ collapsed, toggleSidebar, activeMenu, auth, handleLogout }) {
    const [profileDropdownOpen, setProfileDropdownOpen] = useState(false);
    const profileRef = useRef(null);

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (profileRef.current && !profileRef.current.contains(event.target)) {
                setProfileDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    return (
        <header className="hidden md:flex h-16 items-center justify-between px-8 bg-white/90 border-b border-slate-100 shrink-0 sticky top-0 z-40 backdrop-blur-md shadow-[0_1px_3px_0_rgba(0,0,0,0.01),0_1px_2px_-1px_rgba(0,0,0,0.01)]">
            {/* Left: Toggle & Breadcrumbs */}
            <div className="flex items-center gap-3">
                {/* Sidebar Toggle Button */}
                <button 
                    onClick={toggleSidebar}
                    className="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-slate-500 hover:text-indigo-600 rounded-xl transition-all mr-1 shrink-0 cursor-pointer"
                    title={collapsed ? "Buka Sidebar" : "Tutup Sidebar"}
                >
                    {collapsed ? <ChevronRight className="w-4.5 h-4.5" /> : <ChevronLeft className="w-4.5 h-4.5" />}
                </button>

                <div className="flex items-center gap-2 text-[10px] font-bold text-slate-400 select-none uppercase tracking-wider">
                    <span>SaaS Central</span>
                    <ChevronRight className="w-3.5 h-3.5 text-slate-300" />
                    <span className="text-slate-900 font-black tracking-tight">{activeMenu}</span>
                </div>
            </div>

            {/* Right: Actions */}
            <div className="flex items-center gap-3">
                <Link 
                    href={route('landlord.monitoring.index')}
                    className="w-9 h-9 flex items-center justify-center bg-slate-50 hover:bg-indigo-50 border border-slate-100 hover:border-indigo-100 text-slate-550 hover:text-indigo-600 rounded-xl transition-all shadow-sm"
                    title="Pemantauan Sistem"
                >
                    <Activity className="w-4.5 h-4.5" />
                </Link>

                {/* Profile Dropdown */}
                <div className="relative" ref={profileRef}>
                    <button
                        onClick={() => setProfileDropdownOpen(!profileDropdownOpen)}
                        className={cn(
                            "flex items-center gap-2.5 px-3 py-1.5 bg-slate-50 hover:bg-indigo-50/50 border border-slate-100 hover:border-indigo-100 rounded-xl transition-all shadow-sm group cursor-pointer",
                            profileDropdownOpen && "bg-indigo-50/50 border-indigo-100 shadow-md"
                        )}
                    >
                        <div className="w-7 h-7 rounded-lg bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-black text-xs flex items-center justify-center shadow-md shadow-indigo-600/15 group-hover:scale-105 transition-transform select-none shrink-0">
                            {auth?.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A'}
                        </div>
                        <div className="text-left hidden lg:block select-none shrink-0">
                            <p className="text-xs font-black text-slate-800 leading-none">{auth?.user?.name || 'Super Admin'}</p>
                            <p className="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Diskominfo</p>
                        </div>
                        <ChevronDown className="w-3.5 h-3.5 text-slate-400 group-hover:text-slate-600 shrink-0" />
                    </button>

                    {/* Dropdown Menu */}
                    {profileDropdownOpen && (
                        <div className="absolute right-0 mt-2.5 w-64 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 p-2 animate-in fade-in slide-in-from-top-2 duration-150">
                            {/* Mini Profile Widget */}
                            <div className="flex items-center gap-3 px-3 py-3 border-b border-slate-50">
                                <div className="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-indigo-600/15 shrink-0 select-none">
                                    {auth?.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A'}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-xs font-black text-slate-900 uppercase tracking-tight truncate leading-none">{auth?.user?.name || 'Super Admin'}</p>
                                    <p className="text-[10px] text-slate-400 font-medium mt-1 truncate">{auth?.user?.email || 'admin@diskominfo.go.id'}</p>
                                    <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-indigo-50 text-indigo-655 uppercase mt-1 border border-indigo-100/50">
                                        Diskominfo Central
                                    </span>
                                </div>
                            </div>
                            
                            {/* Action Links */}
                            <div className="p-1 space-y-0.5">
                                <Link
                                    href={route('landlord.settings.index')}
                                    onClick={() => setProfileDropdownOpen(false)}
                                    className="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-indigo-50/40 hover:text-indigo-700 transition-colors group"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-450 flex items-center justify-center shrink-0 group-hover:bg-indigo-100/50 group-hover:text-indigo-600 transition-colors">
                                        <Settings className="w-4.5 h-4.5" />
                                    </div>
                                    <div>
                                        <p className="text-xs font-bold text-slate-800 leading-none">Pengaturan Sistem</p>
                                        <p className="text-[9px] text-slate-400 font-medium mt-0.5">Konfigurasi & alokasi resource</p>
                                    </div>
                                </Link>
                                
                                <Link
                                    href={route('landlord.monitoring.index')}
                                    onClick={() => setProfileDropdownOpen(false)}
                                    className="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-indigo-50/40 hover:text-indigo-700 transition-colors group"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-slate-50 text-slate-450 flex items-center justify-center shrink-0 group-hover:bg-indigo-100/50 group-hover:text-indigo-600 transition-colors">
                                        <Activity className="w-4.5 h-4.5" />
                                    </div>
                                    <div>
                                        <p className="text-xs font-bold text-slate-800 leading-none">Pemantauan Sistem</p>
                                        <p className="text-[9px] text-slate-400 font-medium mt-0.5">Status log & statistik server</p>
                                    </div>
                                </Link>
                            </div>
                            
                            {/* Logout Button */}
                            <div className="p-1 border-t border-slate-50">
                                <button
                                    onClick={(e) => {
                                        setProfileDropdownOpen(false);
                                        handleLogout(e);
                                    }}
                                    className="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-left hover:bg-red-50/85 hover:text-red-700 transition-colors group cursor-pointer"
                                >
                                    <div className="w-8 h-8 rounded-lg bg-red-50 text-red-400 flex items-center justify-center shrink-0 group-hover:bg-red-100/50 group-hover:text-red-600 transition-colors">
                                        <LogOut className="w-4.5 h-4.5" />
                                    </div>
                                    <div>
                                        <p className="text-xs font-bold text-red-650 leading-none">Keluar Sesi</p>
                                        <p className="text-[9px] text-red-400 font-medium mt-0.5">Akhiri login super admin</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </header>
    );
}
