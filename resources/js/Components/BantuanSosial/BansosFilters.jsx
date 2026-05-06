import React from 'react';
import { router } from '@inertiajs/react';
import { Search, X } from 'lucide-react';

const JENIS_OPTIONS = [
    { value: 'BLT', label: 'BLT (Bantuan Langsung Tunai)' },
    { value: 'PKH', label: 'PKH (Program Keluarga Harapan)' },
    { value: 'BPNT', label: 'BPNT (Bantuan Pangan Non Tunai)' },
    { value: 'Bansos Lainnya', label: 'Bansos Lainnya' },
];

const STATUS_OPTIONS = [
    { value: 'aktif', label: 'Aktif' },
    { value: 'selesai', label: 'Selesai' },
    { value: 'ditangguhkan', label: 'Ditangguhkan' },
];

const currentYear = new Date().getFullYear();
const TAHUN_OPTIONS = Array.from({ length: currentYear - 2019 }, (_, i) => currentYear - i);

export default function BansosFilters({ filters = {} }) {
    const [local, setLocal] = React.useState({
        search: filters.search ?? '',
        status: filters.status ?? '',
        jenis_bantuan: filters.jenis_bantuan ?? '',
        tahun: filters.tahun ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        router.get(route('bantuan-sosial.index'), local, { preserveState: true, replace: true });
    };

    const handleReset = () => {
        const empty = { search: '', status: '', jenis_bantuan: '', tahun: '' };
        setLocal(empty);
        router.get(route('bantuan-sosial.index'), {}, { preserveState: true, replace: true });
    };

    const inputClass =
        'w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition-all';

    return (
        <div className="bg-white border border-gray-100 rounded-2xl shadow-sm p-5 sm:p-6">
            <form onSubmit={handleSubmit}>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {/* Search */}
                    <div className="relative">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input
                            type="text"
                            placeholder="Cari nama program..."
                            value={local.search}
                            onChange={(e) => setLocal({ ...local, search: e.target.value })}
                            className={`${inputClass} pl-10`}
                        />
                    </div>

                    {/* Status */}
                    <select
                        value={local.status}
                        onChange={(e) => setLocal({ ...local, status: e.target.value })}
                        className={inputClass}
                    >
                        <option value="">Semua Status</option>
                        {STATUS_OPTIONS.map((o) => (
                            <option key={o.value} value={o.value}>{o.label}</option>
                        ))}
                    </select>

                    {/* Jenis Bantuan */}
                    <select
                        value={local.jenis_bantuan}
                        onChange={(e) => setLocal({ ...local, jenis_bantuan: e.target.value })}
                        className={inputClass}
                    >
                        <option value="">Semua Jenis</option>
                        {JENIS_OPTIONS.map((o) => (
                            <option key={o.value} value={o.value}>{o.label}</option>
                        ))}
                    </select>

                    {/* Tahun */}
                    <select
                        value={local.tahun}
                        onChange={(e) => setLocal({ ...local, tahun: e.target.value })}
                        className={inputClass}
                    >
                        <option value="">Semua Tahun</option>
                        {TAHUN_OPTIONS.map((y) => (
                            <option key={y} value={y}>{y}</option>
                        ))}
                    </select>
                </div>

                <div className="flex items-center gap-3 mt-4">
                    <button
                        type="submit"
                        className="flex items-center px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-sm"
                    >
                        <Search className="w-3.5 h-3.5 mr-2" />
                        FILTER
                    </button>
                    <button
                        type="button"
                        onClick={handleReset}
                        className="flex items-center px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                    >
                        <X className="w-3.5 h-3.5 mr-2" />
                        RESET
                    </button>
                </div>
            </form>
        </div>
    );
}
