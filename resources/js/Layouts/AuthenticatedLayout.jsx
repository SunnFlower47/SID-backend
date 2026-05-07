import React, { useState } from 'react';
import Sidebar from '@/Components/Layout/Sidebar';
import Navbar from '@/Components/Layout/Navbar';
import { Head, usePage } from '@inertiajs/react';
import Swal from 'sweetalert2';
import { useEffect } from 'react';

export default function AuthenticatedLayout({ children, title }) {
    const { flash } = usePage().props;
    const [sidebarCollapsed, setSidebarCollapsed] = useState(false);
    const [isMobileOpen, setIsMobileOpen] = useState(false);

    useEffect(() => {
        if (flash?.success) {
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL!',
                text: flash.success,
                showConfirmButton: true,
                confirmButtonText: 'OKE SIAP!',
                confirmButtonColor: '#10b981',
                timer: 5000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1f2937',
                iconColor: '#10b981',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black tracking-tighter uppercase italic',
                    confirmButton: 'rounded-2xl px-8 py-3 font-black uppercase tracking-widest text-[10px]'
                }
            });
        }
        if (flash?.error) {
            Swal.fire({
                icon: 'error',
                title: 'TERJADI KESALAHAN!',
                text: flash.error,
                showConfirmButton: true,
                confirmButtonColor: '#ef4444',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-3xl border-none shadow-2xl',
                    title: 'font-black tracking-tighter uppercase italic',
                    confirmButton: 'rounded-2xl px-8 py-3 font-black uppercase tracking-widest text-[10px]'
                }
            });
        }
    }, [flash]);

    return (
        <div className="flex h-screen w-full bg-gray-50/50 overflow-hidden font-sans">
            <Head title={title} />

            {/* Sidebar - Desktop */}
            <div className="hidden lg:block shrink-0 h-full">
                <Sidebar
                    collapsed={sidebarCollapsed}
                    toggleDesktop={setSidebarCollapsed}
                />
            </div>

            {/* Main Content Area */}
            <div className="flex flex-col flex-1 min-w-0 h-full overflow-hidden">
                <Navbar
                    toggleMobileSidebar={() => setIsMobileOpen(!isMobileOpen)}
                    toggleDesktopSidebar={() => setSidebarCollapsed(!sidebarCollapsed)}
                    sidebarCollapsed={sidebarCollapsed}
                />

                <main className="flex-1 overflow-y-scroll p-4 md:p-8">
                    <div className="mx-auto max-w-7xl">
                        {children}
                    </div>
                </main>
            </div>

            {/* Mobile Sidebar & Overlay */}
            <div className={`fixed inset-0 z-[100] lg:hidden transition-all duration-300 ${isMobileOpen ? 'opacity-100 visible pointer-events-auto' : 'opacity-0 invisible pointer-events-none'}`}>
                <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setIsMobileOpen(false)} />
                <div className={`absolute inset-y-0 left-0 w-72 transition-transform duration-300 transform ${isMobileOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                    <Sidebar
                        collapsed={false}
                        isMobile={true}
                        closeMobile={() => setIsMobileOpen(false)}
                    />
                </div>
            </div>
        </div>
    );
}
