import { Head, Link, usePage, useForm } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, Badge } from '@/Components/Shared';
import { useState } from 'react';
import { HardDrive, Edit3, Check, X } from 'lucide-react';

function AllocationRow({ allocation }) {
    const [isEditing, setIsEditing] = useState(false);
    
    const { data, setData, put, processing, errors } = useForm({
        max_users: allocation.max_users,
        storage_limit_mb: allocation.storage_limit_mb,
        is_active: allocation.is_active,
    });

    const submit = (e) => {
        e.preventDefault();
        put(route('landlord.allocations.update', allocation.id), {
            onSuccess: () => setIsEditing(false),
        });
    };

    const formattedStorage = allocation.storage_limit_mb >= 1024
        ? `${(allocation.storage_limit_mb / 1024).toFixed(1)} GB`
        : `${allocation.storage_limit_mb} MB`;

    if (isEditing) {
        return (
            <tr className="bg-indigo-50/50 transition-colors border-b border-indigo-100">
                <td className="px-6 py-4 text-sm font-bold text-slate-800 border-l-2 border-indigo-500">
                    {allocation.tenant?.name || 'N/A'}
                </td>
                <td colSpan="4" className="px-6 py-4">
                    <form onSubmit={submit} className="flex flex-wrap items-center gap-4 sm:gap-6">
                        <div className="flex items-center gap-2">
                            <label className="text-[10px] font-black text-slate-500 uppercase tracking-widest">Maks Users</label>
                            <input 
                                type="number" 
                                value={data.max_users} 
                                onChange={e => setData('max_users', e.target.value)}
                                className="w-20 px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 transition-all"
                                required
                            />
                        </div>
                        
                        <div className="flex items-center gap-2">
                            <label className="text-[10px] font-black text-slate-500 uppercase tracking-widest">Storage (MB)</label>
                            <input 
                                type="number" 
                                value={data.storage_limit_mb} 
                                onChange={e => setData('storage_limit_mb', e.target.value)}
                                className="w-24 px-3 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold outline-none focus:border-indigo-500 transition-all"
                                required
                            />
                        </div>

                        <div className="flex items-center">
                            <input 
                                type="checkbox" 
                                id={`is_active_${allocation.id}`}
                                checked={data.is_active}
                                onChange={e => setData('is_active', e.target.checked)}
                                className="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-4 focus:ring-indigo-500/10"
                            />
                            <label htmlFor={`is_active_${allocation.id}`} className="ml-2 text-xs font-bold text-slate-700">Aktif (Lisensi)</label>
                        </div>

                        <div className="flex items-center gap-2 ms-auto">
                            <button 
                                type="submit" 
                                disabled={processing} 
                                className="inline-flex items-center gap-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm shadow-indigo-600/10"
                            >
                                <Check className="w-3.5 h-3.5" />
                                Simpan
                            </button>
                            <button 
                                type="button" 
                                onClick={() => setIsEditing(false)} 
                                className="inline-flex items-center gap-1 px-3 py-2 bg-white hover:bg-gray-100 text-slate-600 border border-gray-200 rounded-xl text-xs font-bold transition-all"
                            >
                                <X className="w-3.5 h-3.5" />
                                Batal
                            </button>
                        </div>
                    </form>
                </td>
            </tr>
        );
    }

    return (
        <tr className="hover:bg-slate-50/50 transition-colors border-b border-gray-50 last:border-0">
            <td className="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">{allocation.tenant?.name || 'N/A'}</td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-bold text-center">{allocation.max_users} User</td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-slate-600 font-bold text-center">{formattedStorage}</td>
            <td className="px-6 py-4 whitespace-nowrap">
                <div className="flex justify-center">
                    <Badge 
                        color={allocation.is_active ? 'green' : 'red'}
                        dot={allocation.is_active ? 'green' : 'red'}
                    >
                        {allocation.is_active ? 'Aktif' : 'Nonaktif'}
                    </Badge>
                </div>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button 
                    onClick={() => setIsEditing(true)} 
                    className="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-700 text-slate-600 rounded-xl text-xs font-bold transition-all"
                >
                    <Edit3 className="w-3.5 h-3.5" />
                    Ubah Limit
                </button>
            </td>
        </tr>
    );
}

export default function Index({ allocations }) {
    const { flash } = usePage().props;

    return (
        <LandlordLayout>
            <Head title="Alokasi Resource Tenant" />

            <div className="space-y-8">
                {/* Header */}
                <PageHeader 
                    icon={HardDrive}
                    title="Alokasi Resource"
                    subtitle="Kelola dan batasi kuota pengguna serta kapasitas penyimpanan database desa."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {flash?.success && (
                    <div className="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl text-sm font-bold shadow-sm" role="alert">
                        <span className="block sm:inline">{flash.success}</span>
                    </div>
                )}

                {/* Table Card */}
                <TableCard
                    title="Daftar Alokasi Kuota Desa"
                    icon={HardDrive}
                    total={allocations.total}
                    totalLabel="Desa"
                    pagination={allocations}
                    noPadding
                >
                    <div className="overflow-x-auto">
                        <table className="w-full border-collapse">
                            <thead>
                                <tr className="bg-slate-50 border-b-2 border-slate-200/70">
                                    <th className="px-6 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-left">Nama Desa</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-center border-l-2 border-slate-200/70">Maks Users</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-center border-l-2 border-slate-200/70">Storage Limit</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-center border-l-2 border-slate-200/70">Status Sewa</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-slate-800 uppercase tracking-widest text-right border-l-2 border-slate-200/70">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="bg-white">
                                {allocations.data.map((allocation) => (
                                    <AllocationRow key={allocation.id} allocation={allocation} />
                                ))}
                                {allocations.data.length === 0 && (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-8 text-center text-gray-500 font-bold italic">
                                            Belum ada data alokasi desa.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </TableCard>
            </div>
        </LandlordLayout>
    );
}
