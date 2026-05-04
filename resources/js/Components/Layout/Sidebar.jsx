import React, { useState, useEffect } from 'react';
import { Link, usePage } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import { cn } from '@/lib/utils';

const getIcon = (name) => {
    return Icons[name] || Icons.HelpCircle || Icons.AlertCircle;
};

const menuGroups = [
    {
        name: 'Dashboard',
        icon: 'LayoutDashboard',
        href: 'dashboard',
        color: 'from-green-600 to-green-700',
        hoverBg: 'hover:from-green-50 hover:to-green-100 hover:text-green-700'
    },
    {
        name: 'Data Kependudukan',
        icon: 'Users',
        color: 'from-green-600 to-green-700',
        hoverBg: 'hover:from-green-50 hover:to-green-100 hover:text-green-700',
        items: [
            { name: 'Data Penduduk', href: 'penduduk.index', icon: 'Users' },
            { name: 'Kartu Keluarga', href: 'kk.index', icon: 'Home' },
            { name: 'Penduduk Domisili', href: 'domisili.index', icon: 'MapPin' },
            { name: 'Data Mutasi', href: 'mutasi.data.index', icon: 'ArrowLeftRight' },
        ]
    },
    {
        name: 'Layanan Administrasi',
        icon: 'FileText',
        color: 'from-purple-600 to-purple-700',
        hoverBg: 'hover:from-purple-50 hover:to-purple-100 hover:text-purple-700',
        items: [
            { name: 'Layanan Surat', href: 'admin.surat-pengajuan.index', icon: 'FileText' },
            { name: 'Master Jenis Surat', href: 'admin.surat-type.index', icon: 'ScrollText' },
            { name: 'Bantuan Sosial', href: 'bantuan-sosial.index', icon: 'HeartHandshake' },
            { name: 'Pengaduan Warga', href: 'pengaduan.index', icon: 'MessageSquare' },
            { name: 'Pesan Kontak', href: 'contact-messages.index', icon: 'Mail' },
        ]
    },
    {
        name: 'Informasi & Web Desa',
        icon: 'Globe',
        color: 'from-blue-600 to-blue-700',
        hoverBg: 'hover:from-blue-50 hover:to-blue-100 hover:text-blue-700',
        items: [
            { name: 'Struktur Desa', href: 'struktur-desa.index', icon: 'Users' },
            { name: 'Kontak Desa', href: 'kontak-desa.index', icon: 'Phone' },
            { name: 'Fasilitas Desa', href: 'fasilitas-desa.index', icon: 'MapPin' },
            { name: 'Data UMKM', href: 'umkm.index', icon: 'Store' },
            { name: 'Berita & Pengumuman', href: 'berita.index', icon: 'Newspaper' },
            { name: 'Testimoni Warga', href: 'testimoni.index', icon: 'Star' },
            { name: 'Pengaturan Web', href: 'web-desa.settings', icon: 'Settings' },
        ]
    },
    {
        name: 'Keuangan Desa',
        icon: 'Wallet',
        color: 'from-emerald-600 to-emerald-700',
        hoverBg: 'hover:from-emerald-50 hover:to-emerald-100 hover:text-emerald-700',
        items: [
            { name: 'Dashboard Keuangan', href: 'transparansi-desa.index', icon: 'BarChart' },
            { name: 'Perencanaan (APBDes)', href: 'anggaran.create-tahunan', icon: 'FileSignature' },
            { name: 'Realisasi Pengeluaran', href: 'anggaran.create-pengeluaran', icon: 'Wallet' },
            { name: 'Proyek Pembangunan', href: 'anggaran.create-proyek', icon: 'Construction' },
        ]
    },
    {
        name: 'Laporan & Analisis',
        icon: 'TrendingUp',
        color: 'from-indigo-600 to-indigo-700',
        hoverBg: 'hover:from-indigo-50 hover:to-indigo-100 hover:text-indigo-700',
        items: [
            { name: 'Laporan', href: 'laporan.index', icon: 'FileText' },
            { name: 'Statistik', href: 'statistics.index', icon: 'LineChart' },
            { name: 'Perbandingan', href: 'comparison.index', icon: 'ArrowLeftRight' },
        ]
    },
    {
        name: 'Pusat Data & Wilayah',
        icon: 'Database',
        color: 'from-cyan-600 to-cyan-700',
        hoverBg: 'hover:from-cyan-50 hover:to-cyan-100 hover:text-cyan-700',
        items: [
            { name: 'Master Wilayah', href: 'settings.wilayah.index', icon: 'Map' },
            { name: 'Issue Queue', href: 'settings.wilayah.import-conflicts.index', icon: 'AlertTriangle' },
            { name: 'Import Data', href: 'import.index', icon: 'Download' },
            { name: 'Export Data', href: 'export-import.index', icon: 'Upload' },
            { name: 'Sampah Penduduk', href: 'settings.trash.penduduk.index', icon: 'Trash2' },
            { name: 'Backup', href: 'backup.index', icon: 'Database' },
        ]
    },
    {
        name: 'Pengaturan & Keamanan',
        icon: 'ShieldAlert',
        color: 'from-red-600 to-red-700',
        hoverBg: 'hover:from-red-50 hover:to-red-100 hover:text-red-700',
        items: [
            { name: 'Audit Log', href: 'audit-log.index', icon: 'History' },
            { name: 'Pengaturan', href: 'settings.index', icon: 'Settings' },
        ]
    }
];

export default function Sidebar({ collapsed, isMobile = false, closeMobile, toggleDesktop }) {
    const { url } = usePage();
    const [openGroup, setOpenGroup] = useState(null);

    const isRouteActive = (routeName) => {
        if (!routeName) return false;
        try {
            const current = route().current();
            if (!current) return false;

            // Exact match
            if (current === routeName) return true;

            // Handle resource sub-routes (e.g. penduduk.create matches penduduk.index)
            if (routeName.endsWith('.index')) {
                const resourceBase = routeName.replace('.index', '');
                // Check if current route starts with resourceBase followed by a dot
                if (current.startsWith(resourceBase + '.')) return true;
            }

            // Fallback for custom patterns
            const routeParts = routeName.split('.');
            const currentParts = current.split('.');
            if (routeParts.length >= 2 && currentParts.length >= 2) {
                return routeParts[0] === currentParts[0] && routeParts[1] === currentParts[1];
            }
            
            return false;
        } catch (e) {
            return false;
        }
    };

    // Auto-open active group
    useEffect(() => {
        const activeGroup = menuGroups.find(group => 
            group.items && group.items.some(item => isRouteActive(item.href))
        );
        if (activeGroup) {
            setOpenGroup(activeGroup.name);
        }
    }, [url]);

    const toggleGroup = (name) => {
        if (collapsed) {
            toggleDesktop(false);
            setOpenGroup(name);
        } else {
            setOpenGroup(openGroup === name ? null : name);
        }
    };

    const safeRoute = (name) => {
        try { return route(name); } catch (e) { return '#'; }
    };

    const ChevronDown = getIcon('ChevronDown');

    return (
        <div className={cn(
            "h-full bg-white border-r border-gray-200 transition-all duration-300 flex flex-col shadow-2xl overflow-hidden",
            collapsed ? "w-20" : "w-72"
        )}>
            {/* Brand Logo - Fixed Height h-20 to match Navbar */}
            <div className={cn(
                "border-b border-gray-200 shrink-0 bg-gradient-to-br from-white to-gray-50 h-20 flex items-center",
                collapsed ? "justify-center px-4" : "px-6"
            )}>
                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 md:w-12 md:h-12 bg-white p-0.5 rounded-2xl flex items-center justify-center shadow-md shrink-0 overflow-hidden">
                        <img src="/assets/images/logo-desa-cibatu.png" alt="Logo" className="w-full h-full object-contain" />
                    </div>
                    {!collapsed && (
                        <div className="animate-in fade-in duration-500">
                            <h1 className="text-lg font-bold text-gray-900 leading-none tracking-tighter uppercase">SID Cibatu</h1>
                            <p className="text-green-600 text-[10px] font-black mt-1 uppercase tracking-widest">Sistem Informasi Desa</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Navigation Menu */}
            <nav className="flex-1 overflow-y-auto p-4 space-y-2 scrollbar-hide">
                {menuGroups.map((group) => {
                    // Dashboard as direct link
                    if (!group.items) {
                        const active = isRouteActive(group.href);
                        const Icon = getIcon(group.icon);
                        return (
                            <Link
                                key={group.name}
                                href={safeRoute(group.href)}
                                onClick={() => isMobile && closeMobile && closeMobile()}
                                className={cn(
                                    "flex items-center rounded-2xl transition-all duration-300",
                                    collapsed ? "justify-center p-3" : "px-4 py-3",
                                    active 
                                        ? `bg-gradient-to-r ${group.color} text-white shadow-lg shadow-green-200` 
                                        : `text-gray-700 ${group.hoverBg}`
                                )}
                            >
                                <Icon className={cn("w-5 h-5 shrink-0", !collapsed && "mr-3")} />
                                {!collapsed && <span className="font-bold text-sm tracking-tight">{group.name}</span>}
                            </Link>
                        );
                    }

                    const groupActive = group.items.some(item => isRouteActive(item.href));
                    const isOpen = openGroup === group.name;
                    const GroupIcon = getIcon(group.icon);

                    return (
                        <div key={group.name} className="space-y-1">
                            <button
                                onClick={() => toggleGroup(group.name)}
                                className={cn(
                                    "flex items-center w-full rounded-2xl transition-all duration-300 text-gray-700 group",
                                    collapsed ? "justify-center p-3" : "justify-between px-4 py-3",
                                    groupActive ? "bg-gray-50/50" : group.hoverBg
                                )}
                            >
                                <div className="flex items-center">
                                    <GroupIcon className={cn("w-5 h-5 shrink-0 transition-colors", !collapsed && "mr-3", groupActive ? "text-green-600" : "text-gray-400 group-hover:text-green-700")} />
                                    {!collapsed && <span className="font-bold text-sm tracking-tight">{group.name}</span>}
                                </div>
                                {!collapsed && (
                                    <ChevronDown className={cn("w-4 h-4 transition-transform text-gray-300", isOpen && "rotate-180 text-green-600")} />
                                )}
                            </button>

                            {(!collapsed && isOpen) && (
                                <div className={cn(
                                    "space-y-1 animate-in slide-in-from-top-2 duration-300",
                                    !collapsed && "ml-4 pl-4 border-l border-gray-100 mt-2"
                                )}>
                                    {group.items.map((item) => {
                                        const active = isRouteActive(item.href);
                                        const ItemIcon = getIcon(item.icon);
                                        return (
                                            <Link
                                                key={item.name}
                                                href={safeRoute(item.href)}
                                                onClick={() => isMobile && closeMobile && closeMobile()}
                                                className={cn(
                                                    "flex items-center rounded-xl transition-all duration-300 relative",
                                                    collapsed ? "justify-center p-2.5" : "px-4 py-2.5",
                                                    active 
                                                        ? `bg-gradient-to-r ${group.color} text-white shadow-md shadow-gray-200` 
                                                        : `text-gray-600 ${group.hoverBg}`
                                                )}
                                            >
                                                <ItemIcon className={cn("w-4 h-4 shrink-0", !collapsed && "mr-3", active ? "text-white" : "text-gray-400")} />
                                                {!collapsed && <span className="text-sm font-semibold tracking-tight">{item.name}</span>}
                                                {active && collapsed && (
                                                    <div className="absolute right-0 w-1 h-6 bg-white rounded-l-full" />
                                                )}
                                            </Link>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                    );
                })}
            </nav>

            {/* Sidebar Footer - Desktop Toggle */}
            {!isMobile && (
                <div className="p-4 border-t border-gray-100 bg-gray-50/30">
                    <button
                        onClick={() => toggleDesktop(prev => !prev)}
                        className={cn(
                            "flex items-center w-full p-3 rounded-2xl transition-all duration-300 group overflow-hidden",
                            collapsed ? "justify-center" : "justify-start px-4 bg-white border border-gray-100 shadow-sm hover:border-green-200"
                        )}
                    >
                        <Icons.Menu className={cn(
                            "w-5 h-5 transition-transform duration-500",
                            collapsed ? "text-gray-400 group-hover:text-green-600 rotate-90" : "mr-3 text-gray-500 group-hover:text-green-600"
                        )} />
                        {!collapsed && (
                            <span className="text-xs font-black text-gray-700 uppercase tracking-widest group-hover:text-green-700">Tutup Sidebar</span>
                        )}
                    </button>
                </div>
            )}
        </div>
    );
}
