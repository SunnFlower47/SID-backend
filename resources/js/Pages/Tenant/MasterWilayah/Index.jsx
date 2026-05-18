import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, router, Deferred, Link } from '@inertiajs/react';
import { 
    Map, 
    Users, 
    Building2, 
    MapPin, 
    Plus, 
    Search, 
    Filter, 
    ChevronLeft, 
    Edit2, 
    Trash2, 
    History,
    TrendingUp,
    AlertCircle,
    CheckCircle2,
    Undo2,
    Eye,
    Loader2
} from 'lucide-react';
import ImpactModal from '@/Components/MasterWilayah/ImpactModal';
import CrudModal from '@/Components/MasterWilayah/CrudModal';
import axios from 'axios';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonActivity from '@/Components/Shared/Skeleton/SkeletonActivity';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';

const StatBox = ({ label, value, sub, icon: Icon, color = 'green' }) => {
    const colors = {
        green: 'text-green-600 bg-green-50',
        blue: 'text-blue-600 bg-blue-50',
        purple: 'text-purple-600 bg-purple-50',
        orange: 'text-orange-600 bg-orange-50',
        indigo: 'text-indigo-600 bg-indigo-50',
        emerald: 'text-emerald-600 bg-emerald-50',
    };
    return (
        <div className="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm">
            <div className="flex items-center gap-3">
                <div className={cn('w-9 h-9 rounded-xl flex items-center justify-center shrink-0', colors[color])}>
                    <Icon className="w-4.5 h-4.5" />
                </div>
                <div>
                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{label}</p>
                    <p className="text-lg font-black text-gray-900 italic leading-none">{value || '0'}</p>
                </div>
            </div>
            {sub && <p className="text-[8px] font-bold text-gray-400 mt-2 uppercase tracking-widest leading-none">{sub}</p>}
        </div>
    );
};

export default function Index({ auth, mapping, summary, recentChangeLogs }) {
    const [activeTab, setActiveTab] = useState('rt');
    const [searchTerm, setSearchTerm] = useState('');
    const [isImpactModalOpen, setIsImpactModalOpen] = useState(false);
    const [impactData, setImpactData] = useState(null);
    const [processing, setProcessing] = useState(false);
    const [crudState, setCrudState] = useState({ isOpen: false, type: null, mode: null, data: null });
    const [rollingBackId, setRollingBackId] = useState(null);

    const openCrudModal = (type, mode, data = null) => {
        setCrudState({ isOpen: true, type, mode, data });
    };

    const handleDelete = (type, id) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Apakah Anda yakin ingin menghapus <b>${type.toUpperCase()}</b> ini secara permanen?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Data yang berkaitan harus dipindahkan terlebih dahulu</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS DATA!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route(`settings.wilayah.${type}.destroy`, id));
            }
        });
    };

    const handleRollback = (logId) => {
        Swal.fire({
            title: 'KONFIRMASI ROLLBACK',
            html: `Apakah Anda yakin ingin membatalkan (rollback) perubahan ini?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Semua data terkait (termasuk NIK dan KK) akan dikembalikan ke posisi sebelum perubahan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, ROLLBACK!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-blue-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-blue-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                setRollingBackId(logId);
                router.post(route('settings.wilayah.change-log.rollback', logId), {}, {
                    onSuccess: () => {
                        router.reload({ only: ['recentChangeLogs', 'summary', 'mapping'] });
                    },
                    onFinish: () => setRollingBackId(null)
                });
            }
        });
    };

    const { dusuns, rws, rts } = mapping || {};

    const handlePreview = async (type, item, newData) => {
        setProcessing(true);
        try {
            const routeName = type === 'rt' ? 'settings.wilayah.rt.preview-impact' : 
                            type === 'rw' ? 'settings.wilayah.rw.preview-impact' : 
                            'settings.wilayah.dusun.preview-impact';
            
            const response = await axios.post(route(routeName, item.id), newData);
            setImpactData(response.data);
            setIsImpactModalOpen(true);
        } catch (error) {
            console.error("Preview failed", error);
        } finally {
            setProcessing(false);
        }
    };

    const handleConfirmUpdate = () => {
        if (!impactData) return;
        setProcessing(true);

        const routeName = impactData.entity === 'rt' ? 'settings.wilayah.rt.apply-update' : 
                         impactData.entity === 'rw' ? 'settings.wilayah.rw.update' : 
                         'settings.wilayah.dusun.update';

        router.post(route(routeName, impactData.id), {
            ...impactData.apply_payload,
            preview_token: impactData.preview_token
        }, {
            onSuccess: () => {
                router.reload({ only: ['recentChangeLogs', 'summary', 'mapping'] });
                setIsImpactModalOpen(false);
            },
            onFinish: () => {
                setProcessing(false);
            }
        });
    };

    const filteredData = () => {
        if (activeTab === 'dusun') return (dusuns ?? []).filter(d => d.nama.toLowerCase().includes(searchTerm.toLowerCase()));
        if (activeTab === 'rw') return (rws ?? []).filter(r => r.kode.includes(searchTerm));
        if (activeTab === 'rt') return (rts ?? []).filter(r => r.kode.includes(searchTerm) || r.rw?.kode.includes(searchTerm));
        return [];
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Master Wilayah">
            <Head title="Master Wilayah - Admin Panel" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                
                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Map className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Master Wilayah</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Data Administratif Desa Cibatu</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <button 
                                onClick={() => router.get(route('import-conflicts.index'))}
                                className="flex items-center px-4 py-3 bg-green-500/30 hover:bg-green-500/50 backdrop-blur-md border border-green-400/30 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest"
                            >
                                <History className="w-3.5 h-3.5 mr-2" /> Conflicts
                            </button>
                            <button 
                                onClick={() => openCrudModal(activeTab, 'create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" /> TAMBAH DATA
                            </button>
                        </div>
                    </div>
                </div>

                {/* ── Summary Stats ── */}
                <Deferred data="summary" fallback={<SkeletonStats count={4} />}>
                    <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <StatBox label="Total Dusun" value={summary?.dusun} icon={Map} color="blue" sub="Wilayah Dusun" />
                        <StatBox label="Total RW" value={summary?.rw} icon={Building2} color="indigo" sub="Rukun Warga" />
                        <StatBox label="Total RT" value={summary?.rt} icon={MapPin} color="purple" sub="Rukun Tetangga" />
                        <StatBox label="Penduduk" value={summary?.penduduk_terpetakan?.toLocaleString('id-ID')} icon={Users} color="emerald" sub="Jiwa Terpetakan" />
                    </div>
                </Deferred>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    {/* Main Content */}
                    <div className="lg:col-span-3 space-y-4">
                        <Deferred data="mapping" fallback={<SkeletonTable rows={5} columns={4} />}>
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                {/* Tab Switcher */}
                                <div className="flex p-1.5 bg-gray-50/50 border-b border-gray-50 gap-1">
                                {[
                                    { id: 'rt', label: 'RT', icon: MapPin },
                                    { id: 'rw', label: 'RW', icon: Building2 },
                                    { id: 'dusun', label: 'Dusun', icon: Map }
                                ].map(tab => (
                                    <button
                                        key={tab.id}
                                        onClick={() => setActiveTab(tab.id)}
                                        className={cn(
                                            "flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all",
                                            activeTab === tab.id 
                                            ? "bg-white text-blue-600 shadow-sm border border-gray-100" 
                                            : "text-gray-400 hover:text-gray-600 hover:bg-gray-100"
                                        )}
                                    >
                                        <tab.icon className="w-3.5 h-3.5" />
                                        {tab.label}
                                    </button>
                                ))}
                            </div>

                            {/* Filters */}
                            <div className="p-4 border-b border-gray-50 bg-white">
                                <div className="relative max-w-md">
                                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-300" />
                                    <input 
                                        type="text" 
                                        placeholder={`Cari Kode ${activeTab.toUpperCase()}...`}
                                        className="w-full pl-9 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-xs font-bold text-gray-700 focus:ring-2 focus:ring-blue-500/20"
                                        value={searchTerm}
                                        onChange={(e) => setSearchTerm(e.target.value)}
                                    />
                                </div>
                            </div>

                            {/* Table */}
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Informasi</th>
                                            {activeTab === 'rt' && <th className="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Kaitan</th>}
                                            <th className="px-6 py-3 text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Status</th>
                                            <th className="px-6 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest italic">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {filteredData().length > 0 ? filteredData().map((item) => (
                                            <tr key={item.id} className="hover:bg-gray-50/50 transition-all group">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-4">
                                                        <div className={cn(
                                                            "w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs border border-white shadow-sm",
                                                            activeTab === 'rt' ? 'bg-blue-50 text-blue-600' :
                                                            activeTab === 'rw' ? 'bg-indigo-50 text-indigo-600' :
                                                            'bg-purple-50 text-purple-600'
                                                        )}>
                                                            {item.kode || item.nama.substring(0, 2).toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <p className="text-xs font-black text-gray-900 uppercase italic tracking-tight">{activeTab.toUpperCase()} {item.kode || item.nama}</p>
                                                            <p className="text-[9px] font-bold text-gray-400 uppercase italic tracking-tighter mt-0.5">{item.nama || 'Unit Administratif'}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                {activeTab === 'rt' && (
                                                    <td className="px-6 py-4">
                                                        <p className="text-[10px] font-black uppercase italic text-gray-500 tracking-tighter leading-none">RW {item.rw?.kode ?? '—'}</p>
                                                        <p className="text-[8px] font-bold uppercase italic text-gray-400 tracking-widest mt-1">{item.dusun?.nama ?? '—'}</p>
                                                    </td>
                                                )}
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-2">
                                                        <div className={cn("w-1.5 h-1.5 rounded-full", item.is_active ? 'bg-green-500' : 'bg-gray-300')} />
                                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest italic">{item.is_active ? 'Aktif' : 'Off'}</span>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                    <div className="flex items-center justify-end gap-2">
                                                        {activeTab === 'rt' && (
                                                            <button
                                                                onClick={() => router.get(route('settings.wilayah.detail-rt', item.id))}
                                                                title="Lihat Detail Penduduk"
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors">
                                                                <Eye className="w-4 h-4" />
                                                            </button>
                                                        )}
                                                        <button
                                                            title="Edit"
                                                            onClick={() => openCrudModal(activeTab, 'edit', item)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors">
                                                            <Edit2 className="w-4 h-4" />
                                                        </button>
                                                        <button
                                                            title="Hapus"
                                                            onClick={() => handleDelete(activeTab, item.id)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr><td colSpan={5} className="px-6 py-12 text-center text-[9px] font-black text-gray-300 uppercase tracking-widest italic">Data wilayah tidak ditemukan</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        </Deferred>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-4">
                        <Deferred data="recentChangeLogs" fallback={<SkeletonActivity count={3} />}>
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                <div className="px-5 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <History className="w-3.5 h-3.5 text-blue-600" />
                                        <h3 className="text-[10px] font-black text-gray-900 uppercase tracking-widest italic">Histori RT</h3>
                                    </div>
                                </div>
                                <div className="p-5">
                                    <div className="space-y-4 relative before:absolute before:left-1.5 before:top-2 before:bottom-2 before:w-px before:bg-gray-100">
                                        {Array.isArray(recentChangeLogs) && recentChangeLogs.map((log) => (
                                            <div key={log.id} className="relative pl-6 group">
                                                <div className={cn("absolute left-0 top-1.5 w-3 h-3 rounded-full border-2 border-white shadow-sm z-10", log.status === 'applied' ? 'bg-blue-500' : 'bg-red-500')} />
                                                <div className="space-y-0.5">
                                                    <p className="text-[10px] font-black text-gray-900 uppercase italic tracking-tighter leading-none">RT {log.after_payload?.rt ?? '—'}</p>
                                                    <p className="text-[8px] font-bold text-gray-400 uppercase tracking-tighter leading-tight italic">{log.action === 'update_with_backup' ? 'Snapshot' : 'Change'}</p>
                                                    {log.status === 'applied' && (
                                                        <button 
                                                            onClick={() => handleRollback(log.id)} 
                                                            disabled={rollingBackId === log.id}
                                                            className="flex items-center gap-1 text-[8px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest mt-1 transition-all disabled:opacity-50"
                                                        >
                                                            {rollingBackId === log.id ? (
                                                                <><Loader2 className="w-3 h-3 animate-spin" /> Memproses...</>
                                                            ) : 'Rollback'}
                                                        </button>
                                                    )}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </Deferred>
                    </div>
                </div>

                <ImpactModal 
                    isOpen={isImpactModalOpen}
                    onClose={() => setIsImpactModalOpen(false)}
                    data={impactData}
                    onConfirm={handleConfirmUpdate}
                    processing={processing}
                />
            </div>
            
            <CrudModal 
                isOpen={crudState.isOpen}
                onClose={() => setCrudState({ ...crudState, isOpen: false })}
                type={crudState.type}
                mode={crudState.mode}
                data={crudState.data}
                dusuns={dusuns || []}
                rws={rws || []}
                onPreviewRequest={handlePreview}
            />
        </AuthenticatedLayout>
    );
}
