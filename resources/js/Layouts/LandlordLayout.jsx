import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
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
    Settings
} from 'lucide-react';

export default function LandlordLayout({ children }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);

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
        <div className="min-h-screen bg-slate-50 flex flex-col md:flex-row">
            {/* Mobile Navigation Header */}
            <header className="md:hidden h-16 flex items-center justify-between px-6 bg-slate-900 text-white border-b border-slate-800 z-30">
                <div className="flex items-center gap-3">
                    <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <Radio className="w-5 h-5 animate-pulse" />
                    </div>
                    <span className="font-black text-lg text-white tracking-tight">
                        DESA<span className="text-indigo-400">SAAS</span>
                    </span>
                </div>
                <button 
                    onClick={() => setSidebarOpen(!sidebarOpen)}
                    className="p-2 hover:bg-slate-800 rounded-lg transition-colors text-slate-300"
                >
                    {sidebarOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
                </button>
            </header>

            {/* Mobile Sidebar Drawer Overlay */}
            {sidebarOpen && (
                <div 
                    className="fixed inset-0 bg-slate-900/60 z-20 md:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar Desktop & Mobile Drawer */}
            <aside className={`
                fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 text-slate-300 border-r border-slate-800 flex flex-col transition-transform duration-300 transform md:translate-x-0 md:sticky md:top-0 md:h-screen md:flex shrink-0
                ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}
            `}>
                {/* Logo Section */}
                <div className="h-16 hidden md:flex items-center gap-3 px-6 border-b border-slate-800">
                    <div className="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30">
                        <Radio className="w-5 h-5 animate-pulse" />
                    </div>
                    <span className="font-black text-lg text-white tracking-tight">
                        DESA<span className="text-indigo-400">SAAS</span>
                    </span>
                </div>

                <nav className="flex-1 px-4 py-6 space-y-1">
                    {menuItems.filter(item => item.show).map((item) => {
                        const Icon = item.icon;
                        return (
                            <Link
                                key={item.name}
                                href={item.href}
                                onClick={() => setSidebarOpen(false)}
                                className={`flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all duration-300 ${
                                    item.active
                                        ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 scale-[1.02]'
                                        : 'hover:bg-slate-800 hover:text-slate-200'
                                }`}
                            >
                                <Icon className="w-5 h-5 shrink-0" />
                                {item.name}
                            </Link>
                        );
                    })}
                </nav>

                {/* Footer Section */}
                <div className="p-4 border-t border-slate-800">
                    <div className="flex items-center gap-3 px-4 py-3 rounded-xl bg-slate-850 border border-slate-800/50">
                        <div className="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-300 font-bold border border-slate-700">
                            <User className="w-5 h-5" />
                        </div>
                        <div className="flex-1 min-w-0">
                            <p className="text-sm font-bold text-white truncate">{auth?.user?.name || 'Super Admin'}</p>
                            <p className="text-xs text-slate-500 truncate">Diskominfo</p>
                        </div>
                    </div>
                    <Link
                        href={route('landlord.logout')}
                        method="post"
                        as="button"
                        className="w-full flex items-center justify-center gap-2 mt-3 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-red-900/20 hover:text-red-400 text-slate-400 transition-colors border border-slate-800"
                    >
                        <LogOut className="w-4 h-4" />
                        Log Out
                    </Link>
                </div>
            </aside>

            {/* Content Area */}
            <div className="flex-1 flex flex-col min-w-0 overflow-y-auto">
                <main className="flex-1 p-6 md:p-10">
                    <div className="mx-auto max-w-[85rem] w-full">
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
