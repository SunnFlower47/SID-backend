import React, { useState, useEffect, useRef } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import { 
    LayoutDashboard, 
    Building2, 
    HardDriveDownload, 
    LogOut, 
    User, 
    Menu, 
    X,
    Radio,
    Megaphone,
    Activity,
    UserCheck,
    Settings,
    ChevronLeft,
    ChevronRight,
    Bell,
    ChevronDown
} from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

export default function LandlordLayout({ children }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(false);
    const [profileDropdownOpen, setProfileDropdownOpen] = useState(false);
    const profileRef = useRef(null);

    // Persist collapsed state
    useEffect(() => {
        const stored = localStorage.getItem('landlord_sidebar_collapsed');
        if (stored === 'true') {
            setCollapsed(true);
        }

        const handleClickOutside = (event) => {
            if (profileRef.current && !profileRef.current.contains(event.target)) {
                setProfileDropdownOpen(false);
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const toggleSidebar = () => {
        const next = !collapsed;
        setCollapsed(next);
        localStorage.setItem('landlord_sidebar_collapsed', String(next));
    };

    const handleLogout = (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'Keluar dari Sistem?',
            text: 'Sesi administrasi Anda di Landlord Panel akan diakhiri.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Log Out',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(route('landlord.logout'));
            }
        });
    };

    const menuItems = [
        {
            name: 'Dashboard',
            href: route('landlord.dashboard'),
            active: route().current('landlord.dashboard'),
            icon: LayoutDashboard,
            show: true
        },
        {
            name: 'Manajemen Desa',
            href: route('tenants.index'),
            active: route().current('tenants.*'),
            icon: Building2,
            show: auth?.can?.manage_tenants
        },
        {
            name: 'Alokasi Resource',
            href: route('landlord.allocations.index'),
            active: route().current('landlord.allocations.*'),
            icon: HardDriveDownload,
            show: auth?.can?.manage_allocations
        },
        {
            name: 'Siaran Pengumuman',
            href: route('announcements.index'),
            active: route().current('announcements.*'),
            icon: Megaphone,
            show: auth?.can?.broadcast_announcements
        },
        {
            name: 'User Central',
            href: route('users.index'),
            active: route().current('users.*'),
            icon: User,
            show: auth?.can?.manage_central_users
        },
        {
            name: 'User Tenant',
            href: route('tenant-users.index'),
            active: route().current('tenant-users.*'),
            icon: UserCheck,
            show: auth?.can?.manage_tenants
        },
        {
            name: 'Pemantauan Sistem',
            href: route('landlord.monitoring.index'),
            active: route().current('landlord.monitoring.*'),
            icon: Activity,
            show: true
        },
        {
            name: 'Pengaturan',
            href: route('landlord.settings.index'),
            active: route().current('landlord.settings.*'),
            icon: Settings,
            show: auth?.can?.manage_central_users
        }
    ];

    const activeMenu = menuItems.find(item => item.active)?.name || 'Central Panel';

    return (
        <div className="min-h-screen bg-[#f8fafc] flex w-full overflow-hidden font-sans antialiased">
            {/* Mobile Navigation Header */}
            <header className="md:hidden fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-6 bg-[#0f172a] text-white border-b border-slate-900 z-50 shadow-sm">
                <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/20">
                        <Radio className="w-4 h-4 animate-pulse" />
                    </div>
                    <span className="font-extrabold text-base tracking-tight">
                        DESA<span className="text-indigo-400">SAAS</span>
                    </span>
                </div>
                <button 
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="p-2 hover:bg-slate-900 rounded-xl transition-colors text-slate-400 hover:text-white"
                >
                    {sidebarOpen ? <X className="w-5.5 h-5.5" /> : <Menu className="w-5.5 h-5.5" />}
                </button>
            </header>

            {/* Mobile Sidebar Overlay */}
            {sidebarOpen && (
                <div 
                    className="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[90] md:hidden transition-all duration-300"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar (Desktop & Mobile Drawer) */}
            <aside className={cn(
                "fixed inset-y-0 left-0 z-[100] bg-[#0f172a] text-slate-350 border-r border-slate-900/40 flex flex-col transition-all duration-300 ease-in-out md:sticky md:top-0 md:h-screen md:flex shrink-0 overflow-x-hidden",
                collapsed ? "w-20" : "w-60",
                sidebarOpen ? "translate-x-0" : "-translate-x-full md:translate-x-0"
            )}>
                {/* Logo Section */}
                <div className={cn("h-16 flex items-center border-b border-slate-900/40 shrink-0", collapsed ? "justify-center px-0" : "justify-between px-5")}>
                    <div className="flex items-center gap-2.5 overflow-hidden">
                        <div className="w-8.5 h-8.5 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/25 shrink-0">
                            <Radio className="w-4.5 h-4.5 animate-pulse" />
                        </div>
                        {!collapsed && (
                            <span className="font-black text-base text-white tracking-tight animate-in fade-in duration-300 whitespace-nowrap">
                                DESA<span className="text-indigo-400 font-extrabold">SAAS</span>
                            </span>
                        )}
                    </div>
                </div>

                {/* Sidebar Navigation */}
                <nav className="flex-1 px-3 py-5 space-y-1 overflow-y-auto overflow-x-hidden custom-scrollbar">
                    {menuItems.filter(item => item.show).map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                onClick={() => setSidebarOpen(false)}
                                className={cn(
                                    "flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-bold transition-all duration-200 group relative",
                                    item.active
                                        ? 'bg-indigo-500/5 text-white'
                                        : 'hover:bg-slate-900/50 hover:text-slate-200 text-slate-400'
                                )}
                                title={collapsed ? item.name : undefined}
                            >
                                {/* Left Indicator Line */}
                                {item.active && (
                                    <div className="absolute left-0 top-1/2 -translate-y-1/2 w-0.75 h-5 bg-indigo-500 rounded-r" />
                                )}
                                
                                <Icon className={cn("w-4.5 h-4.5 shrink-0 transition-all duration-200", item.active ? "text-indigo-400" : "text-slate-500 group-hover:text-slate-300")} />
                                
                                {!collapsed && (
                                    <span className="animate-in fade-in duration-300 whitespace-nowrap truncate">{item.name}</span>
                                )}
                                
                                {collapsed && (
                                    <div className="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-950 text-white text-[10px] font-black uppercase tracking-wider rounded-lg opacity-0 group-hover:opacity-100 transition-all pointer-events-none whitespace-nowrap border border-slate-800 shadow-xl z-50">
                                        {item.name}
                                    </div>
                                )}
                            </Link>
                        );
                    })}
                </nav>

                {/* User Profile Summary (When Sidebar is Expanded) */}
                {!collapsed && (
                    <div className="p-3 border-t border-slate-900/40 shrink-0">
                        <div className="flex items-center gap-2.5 px-3 py-2.5 rounded-xl bg-slate-900/30 border border-slate-900/40">
                            <div className="w-8 h-8 rounded-lg bg-indigo-600/10 text-indigo-400 font-black text-xs flex items-center justify-center border border-indigo-500/20 shrink-0 select-none">
                                {auth?.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A'}
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-[11px] font-bold text-slate-200 truncate uppercase tracking-tight">{auth?.user?.name || 'Super Admin'}</p>
                                <p className="text-[9px] text-slate-500 font-medium tracking-wider mt-0.5">Diskominfo Central</p>
                            </div>
                        </div>
                    </div>
                )}
            </aside>

            {/* Main Area */}
            <div className="flex-1 flex flex-col min-w-0 h-screen overflow-hidden pt-16 md:pt-0">
                {/* Desktop Header Navbar */}
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
                                            <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[8px] font-black bg-indigo-50 text-indigo-650 uppercase mt-1 border border-indigo-100/50">
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
                                            onClick={handleLogout}
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

                {/* Main Scrollable Content */}
                <main className="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                    <div className="mx-auto max-w-[85rem] w-full">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
