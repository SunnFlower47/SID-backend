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
    UserRound,
    Lock
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
        <div className="min-h-screen bg-slate-50/50 flex w-full overflow-hidden font-sans">
            {/* Mobile Navigation Header */}
            <header className="md:hidden fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-6 bg-slate-950 text-white border-b border-slate-900 z-50 shadow-md">
                <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <Radio className="w-5 h-5 animate-pulse" />
                    </div>
                    <span className="font-black text-lg text-white tracking-tight">
                        DESA<span className="text-indigo-400 font-extrabold">SAAS</span>
                    </span>
                </div>
                <button 
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="p-2 hover:bg-slate-900 rounded-lg transition-colors text-slate-350"
                >
                    {sidebarOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
                </button>
            </header>

            {/* Mobile Sidebar Overlay */}
            {sidebarOpen && (
                <div 
                    className="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[90] md:hidden transition-all duration-300"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar (Desktop & Mobile Drawer) */}
            <aside className={cn(
                "fixed inset-y-0 left-0 z-[100] bg-slate-950 text-slate-300 border-r border-slate-900/60 flex flex-col transition-all duration-300 ease-in-out md:sticky md:top-0 md:h-screen md:flex shrink-0",
                collapsed ? "w-20" : "w-64",
                sidebarOpen ? "translate-x-0" : "-translate-x-full md:translate-x-0"
            )}>
                {/* Logo Section */}
                <div className="h-20 flex items-center justify-between px-6 border-b border-slate-900/60 shrink-0">
                    <div className="flex items-center gap-3 overflow-hidden">
                        <div className="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30 shrink-0">
                            <Radio className="w-5 h-5 animate-pulse" />
                        </div>
                        {!collapsed && (
                            <span className="font-black text-lg text-white tracking-tight animate-in fade-in duration-300 whitespace-nowrap">
                                DESA<span className="text-indigo-400 font-black">SAAS</span>
                            </span>
                        )}
                    </div>
                    
                    {/* Sidebar Toggle Button (Desktop Only) */}
                    <button 
                        onClick={toggleSidebar}
                        className="hidden md:flex items-center justify-center w-7 h-7 bg-slate-900 hover:bg-slate-800 text-slate-400 hover:text-white rounded-lg border border-slate-800/80 transition-colors shrink-0"
                    >
                        {collapsed ? <ChevronRight className="w-4 h-4" /> : <ChevronLeft className="w-4 h-4" />}
                    </button>
                </div>

                {/* Sidebar Navigation */}
                <nav className="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scrollbar">
                    {menuItems.filter(item => item.show).map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                onClick={() => setSidebarOpen(false)}
                                className={cn(
                                    "flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-bold transition-all duration-300 group relative",
                                    item.active
                                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25 scale-[1.02]'
                                        : 'hover:bg-slate-900 hover:text-slate-200 text-slate-400'
                                )}
                                title={collapsed ? item.name : undefined}
                            >
                                <Icon className={cn("w-5 h-5 shrink-0 transition-transform duration-300 group-hover:scale-105", item.active ? "text-white" : "text-slate-450 group-hover:text-indigo-400")} />
                                {!collapsed && (
                                    <span className="animate-in fade-in duration-300 whitespace-nowrap truncate">{item.name}</span>
                                )}
                                {collapsed && (
                                    <div className="absolute left-full ml-4 px-3 py-1.5 bg-slate-950 text-white text-xs font-black uppercase tracking-wider rounded-xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-slate-800 shadow-xl z-50">
                                        {item.name}
                                    </div>
                                )}
                            </Link>
                        );
                    })}
                </nav>

                {/* User Profile Summary (When Sidebar is Expanded) */}
                {!collapsed && (
                    <div className="p-4 border-t border-slate-900/60 shrink-0">
                        <div className="flex items-center gap-3 px-4 py-3 rounded-2xl bg-slate-900/40 border border-slate-900/60">
                            <div className="w-10 h-10 rounded-full bg-slate-900 flex items-center justify-center text-slate-350 font-bold border border-slate-800 shrink-0 select-none">
                                {auth?.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A'}
                            </div>
                            <div className="flex-1 min-w-0">
                                <p className="text-xs font-black text-white truncate uppercase tracking-tight">{auth?.user?.name || 'Super Admin'}</p>
                                <p className="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mt-0.5">Diskominfo</p>
                            </div>
                        </div>
                    </div>
                )}
            </aside>

            {/* Main Area */}
            <div className="flex-1 flex flex-col min-w-0 h-screen overflow-hidden pt-16 md:pt-0">
                {/* Desktop Header Navbar */}
                <header className="hidden md:flex h-20 items-center justify-between px-8 bg-white border-b border-gray-100 shadow-sm shrink-0 sticky top-0 z-40 backdrop-blur-md bg-white/80">
                    {/* Left: Section Title */}
                    <div>
                        <h1 className="text-base font-black text-gray-950 uppercase tracking-tight leading-none italic">{activeMenu}</h1>
                        <p className="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mt-1">Diskominfo Central Panel</p>
                    </div>

                    {/* Right: User Menu */}
                    <div className="flex items-center gap-4">
                        <Link 
                            href={route('landlord.monitoring.index')}
                            className="p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 hover:border-transparent text-gray-400 hover:text-indigo-600 rounded-2xl transition-all shadow-sm"
                            title="Pemantauan Sistem"
                        >
                            <Activity className="w-4.5 h-4.5" />
                        </Link>

                        {/* Profile Dropdown */}
                        <div className="relative" ref={profileRef}>
                            <button
                                onClick={() => setProfileDropdownOpen(!profileDropdownOpen)}
                                className={cn(
                                    "flex items-center gap-2.5 p-2 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-indigo-50/50 hover:border-indigo-100 transition-all shadow-sm group",
                                    profileDropdownOpen && "bg-indigo-50/50 border-indigo-100 shadow-md"
                                )}
                            >
                                <div className="w-8 h-8 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-lg shadow-indigo-600/25 group-hover:scale-105 transition-transform select-none">
                                    {auth?.user?.name ? auth.user.name.charAt(0).toUpperCase() : 'A'}
                                </div>
                                <div className="text-left hidden lg:block pr-1 select-none">
                                    <p className="text-xs font-black text-gray-900 leading-none">{auth?.user?.name || 'Super Admin'}</p>
                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-wider mt-0.5">Diskominfo</p>
                                </div>
                            </button>

                            {/* Dropdown Menu */}
                            {profileDropdownOpen && (
                                <div className="absolute right-0 mt-3 w-56 bg-white border border-gray-100 rounded-3xl shadow-2xl z-50 p-2 animate-in fade-in slide-in-from-top-2 duration-200">
                                    <div className="px-4 py-3 border-b border-gray-50">
                                        <p className="text-xs font-black text-gray-950 uppercase tracking-tight truncate leading-none">{auth?.user?.name || 'Super Admin'}</p>
                                        <p className="text-[10px] text-gray-400 font-bold mt-1 truncate">{auth?.user?.email || 'admin@diskominfo.go.id'}</p>
                                    </div>
                                    <div className="p-1 space-y-1">
                                        <Link
                                            href={route('landlord.settings.index')}
                                            onClick={() => setProfileDropdownOpen(false)}
                                            className="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-gray-750 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                                        >
                                            <Settings className="w-4 h-4 text-gray-450" />
                                            Pengaturan Sistem
                                        </Link>
                                    </div>
                                    <div className="p-1 border-t border-gray-50">
                                        <button
                                            onClick={handleLogout}
                                            className="w-full flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                                        >
                                            <LogOut className="w-4 h-4" />
                                            Keluar Sesi (Log Out)
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </header>

                {/* Main Scrollable Content */}
                <main className="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
                    <div className="mx-auto max-w-[85rem] w-full">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
