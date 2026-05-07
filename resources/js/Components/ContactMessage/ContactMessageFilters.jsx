import React, { useState, useEffect } from 'react';
import { Search, Filter, Mail, MailWarning, MailCheck, MailOpen, Archive, RefreshCw } from 'lucide-react';
import { router } from '@inertiajs/react';

export default function ContactMessageFilters({ filters = {} }) {
    const [local, setLocal] = useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
    });

    useEffect(() => {
        const timer = setTimeout(() => {
            const currentSearch = filters.search || '';
            const currentStatus = filters.status || '';

            if (local.search !== currentSearch || local.status !== currentStatus) {
                router.get(
                    route('contact-messages.index'),
                    local,
                    { preserveState: true, preserveScroll: true, replace: true }
                );
            }
        }, 500);

        return () => clearTimeout(timer);
    }, [local.search, local.status, filters.search, filters.status]);

    const handleReset = () => {
        setLocal({ search: '', status: '' });
        router.get(route('contact-messages.index'));
    };

    const statusOptions = [
        { value: '', label: 'SEMUA PESAN', icon: Mail },
        { value: 'unread', label: 'BELUM DIBACA', icon: MailWarning },
        { value: 'read', label: 'SUDAH DIBACA', icon: MailOpen },
        { value: 'replied', label: 'SUDAH DIJAWAB', icon: MailCheck },
        { value: 'archived', label: 'DIARSIPKAN', icon: Archive },
    ];

    const isFiltered = local.search !== '' || local.status !== '';

    return (
        <div className="bg-white rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 p-3 sm:p-4 mb-4 shadow-black/5">
            <div className="flex flex-col lg:flex-row gap-3">
                {/* Search */}
                <div className="flex-1">
                    <div className="relative group">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none transition-colors group-focus-within:text-green-500">
                            <Search className="w-4 h-4 text-gray-400 group-focus-within:text-green-500 transition-colors" />
                        </div>
                        <input
                            type="text"
                            value={local.search}
                            onChange={(e) => setLocal({ ...local, search: e.target.value })}
                            className="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-xl sm:rounded-2xl text-[10px] sm:text-[11px] font-black transition-all placeholder:font-bold placeholder:text-gray-400 uppercase tracking-widest"
                            placeholder="CARI NAMA, EMAIL ATAU SUBJEK..."
                        />
                    </div>
                </div>

                {/* Filter Status */}
                <div className="flex-1 lg:max-w-xs">
                    <div className="relative">
                        <div className="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <Filter className="w-4 h-4 text-gray-400" />
                        </div>
                        <select
                            value={local.status}
                            onChange={(e) => setLocal({ ...local, status: e.target.value })}
                            className="w-full pl-10 pr-10 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 rounded-xl sm:rounded-2xl text-[10px] sm:text-[11px] font-black uppercase tracking-widest transition-all appearance-none text-gray-700 cursor-pointer"
                        >
                            {statusOptions.map((opt) => (
                                <option key={opt.value} value={opt.value} className="font-bold">
                                    {opt.label}
                                </option>
                            ))}
                        </select>
                        <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span className="text-gray-400 text-[9px]">▼</span>
                        </div>
                    </div>
                </div>

                {/* Reset Filters */}
                {isFiltered && (
                    <button
                        onClick={handleReset}
                        className="px-5 py-2.5 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-xl sm:rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-2 active:scale-95 shadow-sm shadow-red-100"
                    >
                        <RefreshCw className="w-3 h-3" />
                        RESET
                    </button>
                )}
            </div>
        </div>
    );
}
