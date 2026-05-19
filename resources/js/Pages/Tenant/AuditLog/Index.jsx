import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import { 
    History, Search, Filter, Calendar, User, Eye, 
    Database, Globe, Info, FileSpreadsheet, ArrowRight, RefreshCw
} from 'lucide-react';
import { cn } from '@/lib/utils';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

const LottieComponent = Lottie?.default || Lottie;

export default function Index({ auth, activities, users, events, subjectTypes, stats, filters }) {
    const [showFilters, setShowFilters] = useState(
        filters.search || filters.user_id || filters.event || filters.subject_type || filters.start_date || filters.end_date ? true : false
    );

    const [searchQuery, setSearchQuery] = useState(filters.search || '');
    const [userId, setUserId] = useState(filters.user_id || '');
    const [eventFilter, setEventFilter] = useState(filters.event || '');
    const [modelFilter, setModelFilter] = useState(filters.subject_type || '');
    const [startDate, setStartDate] = useState(filters.start_date || '');
    const [endDate, setEndDate] = useState(filters.end_date || '');

    const handleSearch = (e) => {
        if (e) e.preventDefault();
        router.get(route('audit-log.index'), {
            search: searchQuery,
            user_id: userId,
            event: eventFilter,
            subject_type: modelFilter,
            start_date: startDate,
            end_date: endDate,
        }, { preserveState: true });
    };

    const handleReset = () => {
        setSearchQuery('');
        setUserId('');
        setEventFilter('');
        setModelFilter('');
        setStartDate('');
        setEndDate('');
        router.get(route('audit-log.index'), {}, { preserveState: false });
    };

    const getEventBadge = (event) => {
        switch (event) {
            case 'created':
                return 'bg-green-100 text-green-800 border border-green-200';
            case 'updated':
                return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
            case 'deleted':
                return 'bg-red-100 text-red-800 border border-red-200';
            default:
                return 'bg-gray-100 text-gray-800 border border-gray-200';
        }
    };

    const getMethodBadge = (method) => {
        switch (method) {
            case 'GET':
                return 'bg-blue-50 text-blue-700';
            case 'POST':
                return 'bg-green-50 text-green-700';
            case 'PUT':
            case 'PATCH':
                return 'bg-yellow-50 text-yellow-700';
            case 'DELETE':
                return 'bg-red-50 text-red-700';
            default:
                return 'bg-gray-50 text-gray-700';
        }
    };

    const getModelName = (classPath) => {
        if (!classPath) return '-';
        return classPath.split('\\').pop();
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Audit Log">
            <Head title="Audit Log" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <History className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div className="text-left">
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Audit Log</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 text-left">Riwayat Aktivitas & Perubahan Data Sistem</p>
                            </div>
                        </div>
                        <div className="flex gap-2 sm:gap-3">
                            <a 
                                href={route('audit-log.export.excel', { start_date: startDate, end_date: endDate })}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <FileSpreadsheet className="w-3.5 h-3.5 mr-2" />
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                {/* Info Box */}
                <div className="bg-green-50 border border-green-100 rounded-3xl p-6 flex items-start gap-4 text-left">
                    <div className="p-2 bg-green-100 text-green-600 rounded-xl">
                        <Info className="w-5 h-5" />
                    </div>
                    <div>
                        <p className="text-xs font-bold text-green-800 uppercase tracking-widest mb-1 italic">Informasi Keamanan</p>
                        <p className="text-[11px] text-green-700/80 font-medium leading-relaxed">
                            Semua aktivitas administratif, seperti penambahan, pembaruan, dan penghapusan data kependudukan atau konfigurasi desa direkam secara otomatis untuk kebutuhan kepatuhan audit dan keamanan sistem.
                        </p>
                    </div>
                </div>

                {/* Statistics Cards */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    {[
                        { label: 'Total Log', value: stats.total?.toLocaleString('id-ID'), bg: 'bg-blue-50', text: 'text-blue-700', dot: 'bg-blue-500' },
                        { label: 'Hari Ini', value: stats.today?.toLocaleString('id-ID'), bg: 'bg-emerald-50', text: 'text-emerald-700', dot: 'bg-emerald-500' },
                        { label: 'Minggu Ini', value: stats.this_week?.toLocaleString('id-ID'), bg: 'bg-purple-50', text: 'text-purple-700', dot: 'bg-purple-500' },
                        { label: 'Bulan Ini', value: stats.this_month?.toLocaleString('id-ID'), bg: 'bg-orange-50', text: 'text-orange-700', dot: 'bg-orange-500' },
                    ].map((s, i) => (
                        <div key={i} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-left">
                            <div className="flex items-center gap-3">
                                <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center shrink-0', s.bg)}>
                                    <span className={cn('w-2.5 h-2.5 rounded-full', s.dot)} />
                                </div>
                                <div>
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{s.label}</p>
                                    <p className={cn('text-2xl font-black italic leading-none', s.text)}>{s.value}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* Penduduk-style Collapsible Filters */}
                <div className="space-y-4">
                    <div className="flex justify-between items-center bg-white p-3 sm:p-4 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm transition-all">
                        <div className="flex items-center gap-2 sm:gap-4">
                            <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                                <Search className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                            </div>
                            <div>
                                <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter leading-none mb-1 text-left">Konfigurasi Data</h3>
                                <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">Pencarian & Filter Audit Log</p>
                            </div>
                        </div>
                        <button
                            onClick={() => setShowFilters(!showFilters)}
                            className={cn(
                                "flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95",
                                showFilters
                                    ? "bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20"
                                    : "bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100"
                            )}
                        >
                            <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                            {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                        </button>
                    </div>

                    {showFilters && (
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-3 sm:p-4 animate-in slide-in-from-top-2 duration-300">
                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-12 gap-3 sm:gap-4 text-left items-center">
                                <div className="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-4 xl:col-span-3">
                                    <input 
                                        type="text" 
                                        value={searchQuery} 
                                        placeholder="Cari deskripsi log..."
                                        onChange={e => setSearchQuery(e.target.value)}
                                        onKeyDown={e => e.key === 'Enter' && handleSearch()}
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                    />
                                </div>
                                <div className="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-4 xl:col-span-2">
                                    <select 
                                        value={userId} 
                                        onChange={e => setUserId(e.target.value)} 
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        <option value="">Semua User</option>
                                        {users.map(u => (
                                            <option key={u.id} value={u.id}>{u.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-4 xl:col-span-2">
                                    <select 
                                        value={eventFilter} 
                                        onChange={e => setEventFilter(e.target.value)} 
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        <option value="">Semua Event</option>
                                        {events.map(ev => (
                                            <option key={ev} value={ev}>{ev.toUpperCase()}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-6 xl:col-span-2">
                                    <select 
                                        value={modelFilter} 
                                        onChange={e => setModelFilter(e.target.value)} 
                                        className="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                    >
                                        <option value="">Semua Model</option>
                                        {subjectTypes.map(st => (
                                            <option key={st} value={st}>{getModelName(st)}</option>
                                        ))}
                                    </select>
                                </div>
                                <div className="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-6 xl:col-span-3">
                                    <div className="flex items-center gap-2 bg-gray-50 px-3 py-2 rounded-xl shadow-inner border border-gray-200 w-full h-[38px]">
                                        <Calendar className="w-3.5 h-3.5 text-gray-400 shrink-0" />
                                        <input 
                                            type="date"
                                            value={startDate}
                                            onChange={e => setStartDate(e.target.value)}
                                            className="bg-transparent border-none text-[11px] font-bold focus:ring-0 p-0 w-full cursor-pointer"
                                        />
                                        <span className="text-gray-400 text-xs font-bold shrink-0">s/d</span>
                                        <input 
                                            type="date"
                                            value={endDate}
                                            onChange={e => setEndDate(e.target.value)}
                                            className="bg-transparent border-none text-[11px] font-bold focus:ring-0 p-0 w-full cursor-pointer"
                                        />
                                    </div>
                                </div>
                            </div>
                            
                            <div className="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-3 sm:mt-4">
                                <button onClick={handleReset} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-gray-50 text-gray-500 text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-gray-100 hover:text-gray-700 transition-all border border-gray-200">
                                    <RefreshCw className="w-3.5 h-3.5" /> RESET FILTER
                                </button>
                                <button onClick={() => handleSearch()} className="flex-1 sm:flex-none flex items-center justify-center gap-2 px-6 py-2 rounded-xl bg-green-600 text-white text-[10px] sm:text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95">
                                    <Filter className="w-3.5 h-3.5" /> TERAPKAN FILTER
                                </button>
                            </div>
                        </div>
                    )}
                </div>

                {/* Table View */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-gray-50/80 border-b border-gray-100">
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Waktu</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Model</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">IP Address</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Method</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {activities.data.length > 0 ? (
                                    activities.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-green-50/20 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <p className="text-xs font-bold text-gray-900">
                                                    {dayjs(item.created_at).format('DD MMM YYYY')}
                                                </p>
                                                <p className="text-[10px] font-medium text-gray-400 mt-0.5">
                                                    {dayjs(item.created_at).format('HH:mm:ss')}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center gap-2">
                                                    <div className="w-7 h-7 bg-green-50 text-green-700 rounded-lg flex items-center justify-center shrink-0">
                                                        <User className="w-3.5 h-3.5" />
                                                    </div>
                                                    <div>
                                                        <p className="text-xs font-bold text-gray-900">{item.causer?.name ?? 'System'}</p>
                                                        {item.causer?.email && <p className="text-[9px] text-gray-400 font-medium">{item.causer.email}</p>}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={cn(
                                                    "inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider",
                                                    getEventBadge(item.event)
                                                )}>
                                                    {item.event}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center gap-1.5 text-xs font-bold text-gray-700">
                                                    <Database className="w-3.5 h-3.5 text-gray-400" />
                                                    {getModelName(item.subject_type)}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="flex items-center gap-1.5 text-xs font-mono font-bold text-gray-500">
                                                    <Globe className="w-3.5 h-3.5 text-gray-400" />
                                                    {item.ip_address || '-'}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {item.method ? (
                                                    <span className={cn(
                                                        "inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black tracking-widest",
                                                        getMethodBadge(item.method)
                                                    )}>
                                                        {item.method}
                                                    </span>
                                                ) : '-'}
                                            </td>
                                            <td className="px-6 py-4 text-right whitespace-nowrap">
                                                <div className="flex justify-end">
                                                    <Link 
                                                        href={route('audit-log.show', item.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                        title="Detail"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="px-6 py-12 text-center">
                                            <div className="w-48 h-48 mx-auto">
                                                <LottieComponent animationData={noDataAnimation} loop={true} />
                                            </div>
                                            <p className="text-sm font-black text-gray-900 mt-2">Belum Ada Audit Log</p>
                                            <p className="text-xs text-gray-500 mt-1">Tidak ada data audit log yang tercatat.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {activities.links && activities.links.length > 3 && (
                        <div className="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap justify-center gap-1">
                            <Pagination 
                                links={activities.links} 
                                from={activities.from}
                                to={activities.to}
                                total={activities.total}
                            />
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
