import React from 'react';
import { Link } from '@inertiajs/react';
import { 
    LayoutDashboard, 
    Building2, 
    HardDriveDownload, 
    Megaphone, 
    User, 
    UserCheck, 
    Activity, 
    Settings, 
    Radio, 
    ChevronLeft, 
    ChevronRight,
    X
} from 'lucide-react';
import { cn } from '@/lib/utils';

export default function LandlordSidebar({ collapsed, sidebarOpen, setSidebarOpen, auth }) {
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

    return (
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
                            <p className="text-[9px] text-slate-500 font-medium tracking-wider mt-0.5">Admin Panel Central</p>
                        </div>
                    </div>
                </div>
            )}
        </aside>
    );
}
