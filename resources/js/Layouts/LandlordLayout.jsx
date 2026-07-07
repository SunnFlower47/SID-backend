import React, { useState, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';
import { Radio, Menu, X } from 'lucide-react';
import LandlordSidebar from '@/Components/Layout/LandlordSidebar';
import LandlordNavbar from '@/Components/Layout/LandlordNavbar';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

export default function LandlordLayout({ children }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [collapsed, setCollapsed] = useState(false);

    // Persist collapsed state
    useEffect(() => {
        const stored = localStorage.getItem('landlord_sidebar_collapsed');
        if (stored === 'true') {
            setCollapsed(true);
        }
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

    const activeMenuMap = {
        'landlord.dashboard': 'Dashboard',
        'tenants.index': 'Manajemen Desa',
        'landlord.allocations.index': 'Alokasi Resource',
        'announcements.index': 'Siaran Pengumuman',
        'users.index': 'User Central',
        'tenant-users.index': 'User Tenant',
        'landlord.monitoring.index': 'Pemantauan Sistem',
        'landlord.settings.index': 'Pengaturan'
    };

    const activeMenuKey = Object.keys(activeMenuMap).find(routeKey => route().current(routeKey)) || '';
    const activeMenu = activeMenuMap[activeMenuKey] || 'Central Panel';

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

            {/* Modular Sidebar Component */}
            <LandlordSidebar
                collapsed={collapsed}
                sidebarOpen={sidebarOpen}
                setSidebarOpen={setSidebarOpen}
                auth={auth}
            />

            {/* Main Area */}
            <div className="flex-1 flex flex-col min-w-0 h-screen overflow-hidden pt-16 md:pt-0">
                {/* Modular Navbar Component */}
                <LandlordNavbar
                    collapsed={collapsed}
                    toggleSidebar={toggleSidebar}
                    activeMenu={activeMenu}
                    auth={auth}
                    handleLogout={handleLogout}
                />

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
