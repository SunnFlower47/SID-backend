import React, { useState } from 'react';
import { Head, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { 
    AlertCircle, 
    CheckCircle2, 
    RefreshCcw, 
    Eye, 
    MapPin,
    AlertTriangle,
    User,
    Loader2,
    Filter,
    FileWarning
} from 'lucide-react';
import ResolveModal from '@/Components/Import/ResolveModal';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';

const LottieComponent = Lottie?.default || Lottie;

// ── Issue Type Config ──────────────────────────────────────────────
const ISSUE_TYPES = {
    invalid_nik:      { label: 'NIK Tidak Valid',      badgeClass: 'bg-amber-100 text-amber-700',  icon: User    },
    invalid_nkk:      { label: 'NKK Tidak Valid',      badgeClass: 'bg-amber-100 text-amber-700',  icon: User    },
    wilayah_conflict: { label: 'Wilayah Tidak Dikenal', badgeClass: 'bg-orange-100 text-orange-700', icon: MapPin },
    nik_conflict:     { label: 'NIK Sudah Terdaftar',  badgeClass: 'bg-rose-100 text-rose-700',    icon: User    },
};

const REPROCESS_STATUS = {
    success: { label: 'Berhasil Diimport',      class: 'bg-green-50 border-green-200 text-green-700' },
    failed:  { label: 'Gagal Diimport',          class: 'bg-red-50 border-red-200 text-red-700'     },
    skipped: { label: 'Dilewati',                class: 'bg-gray-50 border-gray-200 text-gray-500'  },
    pending: { label: 'Menunggu Konfirmasi',     class: 'bg-blue-50 border-blue-200 text-blue-700'  },
};

const AUTO_REPROCESS_TYPES = ['invalid_nik', 'invalid_nkk', 'wilayah_conflict', 'fix_fields'];

export default function ImportConflictsIndex({ auth, conflicts, rws, filters, stats }) {
    const [processingId, setProcessingId] = useState(null);
    const [resolvingConflict, setResolvingConflict] = useState(null);

    const applyFilter = (newFilters) => {
        router.get(route('import-conflicts.index'), { ...filters, ...newFilters }, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const handleReprocess = (id) => {
        setProcessingId(id);
        router.post(route('import-conflicts.reprocess', id), {}, {
            onFinish: () => setProcessingId(null)
        });
    };

    const handleReset = (id) => {
        router.post(route('import-conflicts.reset', id), {}, { preserveScroll: true });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Import Conflicts">
            <Head title="Import Conflicts" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* ── Header — sama persis struktur & ukuran dengan Penduduk ── */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <FileWarning className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Import Conflicts</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">Selesaikan isu sinkronisasi data kependudukan</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2 sm:gap-3">
                            <div className="flex items-center px-4 py-3 bg-green-500/30 backdrop-blur-md border border-green-400/30 text-white rounded-xl text-[10px] font-black uppercase tracking-widest gap-2">
                                <AlertCircle className="w-3.5 h-3.5 text-yellow-300" />
                                {stats?.pending ?? 0} Menunggu
                            </div>
                        </div>
                    </div>
                </div>

                {/* ── Stat Cards — sama pola dengan ResidentStats ── */}
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    {[
                        { label: 'Total Konflik',     value: stats?.total   ?? 0, bg: 'bg-gray-50',   text: 'text-gray-700',   dot: 'bg-gray-400'  },
                        { label: 'Menunggu',           value: stats?.pending ?? 0, bg: 'bg-amber-50',  text: 'text-amber-700',  dot: 'bg-amber-400' },
                        { label: 'Sudah Ditangani',   value: (stats?.resolved ?? 0) - (stats?.success ?? 0), bg: 'bg-blue-50', text: 'text-blue-700', dot: 'bg-blue-400' },
                        { label: 'Berhasil Diimport', value: stats?.success ?? 0, bg: 'bg-green-50',  text: 'text-green-700',  dot: 'bg-green-500' },
                    ].map((s, i) => (
                        <div key={i} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div className="flex items-center gap-3">
                                <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center shrink-0', s.bg)}>
                                    <span className={cn('w-3 h-3 rounded-full', s.dot)} />
                                </div>
                                <div>
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{s.label}</p>
                                    <p className={cn('text-2xl font-black italic leading-none', s.text)}>{s.value}</p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                {/* ── Filters ── */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-wrap gap-3 items-center">
                    <Filter className="w-4 h-4 text-gray-400 shrink-0" />
                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Filter:</span>

                    <div className="flex gap-1.5 flex-wrap">
                        {[['all', 'Semua'], ['pending', 'Pending'], ['resolved', 'Selesai']].map(([val, label]) => (
                            <button key={val} onClick={() => applyFilter({ status: val })}
                                className={cn('px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all',
                                    (filters?.status || 'all') === val ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200')}>
                                {label}
                            </button>
                        ))}
                    </div>

                    <div className="h-4 w-px bg-gray-200 hidden sm:block" />

                    <div className="flex gap-1.5 flex-wrap">
                        <button onClick={() => applyFilter({ issue_type: 'all' })}
                            className={cn('px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all',
                                (filters?.issue_type || 'all') === 'all' ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200')}>
                            Semua Tipe
                        </button>
                        {Object.entries(ISSUE_TYPES).map(([key, cfg]) => (
                            <button key={key} onClick={() => applyFilter({ issue_type: key })}
                                className={cn('px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all',
                                    filters?.issue_type === key ? cfg.badgeClass : 'bg-gray-100 text-gray-500 hover:bg-gray-200')}>
                                {cfg.label}
                            </button>
                        ))}
                    </div>
                </div>

                {/* ── Conflict List ── */}
                <Deferred data="conflicts" fallback={<SkeletonTable columns={4} rows={6} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                <FileWarning className="w-6 h-6 text-orange-500" />
                                Daftar Konflik
                            </h3>
                            <span className="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                                Total: {conflicts?.total || 0}
                            </span>
                        </div>

                        {conflicts?.data?.length > 0 ? (
                            <div className="divide-y divide-gray-50">
                                {conflicts.data.map((conflict) => {
                                    const typeCfg = ISSUE_TYPES[conflict.issue_type] || { label: conflict.issue_type, badgeClass: 'bg-gray-100 text-gray-600', icon: AlertTriangle };
                                    const TypeIcon = typeCfg.icon;
                                    const isAutoType = AUTO_REPROCESS_TYPES.includes(conflict.issue_type);
                                    const reprocessCfg = REPROCESS_STATUS[conflict.reprocess_status] || null;
                                    const isSuccess = conflict.reprocess_status === 'success' || conflict.reprocess_status === 'skipped';

                                    return (
                                        <div key={conflict.id} className={cn('transition-all', isSuccess ? 'opacity-70 bg-gray-50/30' : 'hover:bg-orange-50/20')}>
                                            <div className="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                                {/* Info */}
                                                <div className="flex gap-4">
                                                    <div className={cn('w-10 h-10 rounded-full flex items-center justify-center shrink-0 shadow-sm', typeCfg.badgeClass)}>
                                                        <TypeIcon className="w-5 h-5" />
                                                    </div>
                                                    <div className="space-y-1 min-w-0">
                                                        <div className="flex flex-wrap items-center gap-2">
                                                            <p className="font-bold text-gray-900 leading-tight">{conflict.nama || 'Tanpa Nama'}</p>
                                                            <span className={cn('inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[10px] font-bold tracking-wider shrink-0', typeCfg.badgeClass)}>
                                                                {typeCfg.label}
                                                            </span>
                                                            {isSuccess && (
                                                                <span className="inline-flex items-center px-2 py-0.5 mt-0.5 rounded text-[10px] font-bold tracking-wider bg-green-100 text-green-800 shrink-0">
                                                                    ✓ Selesai
                                                                </span>
                                                            )}
                                                        </div>
                                                        <p className="font-mono text-xs text-gray-500">NIK: {conflict.nik || '—'} &nbsp;|&nbsp; NKK: {conflict.nkk || '—'}</p>
                                                        <div className="flex items-start gap-1.5">
                                                            <AlertTriangle className="w-3 h-3 text-amber-500 shrink-0 mt-0.5" />
                                                            <p className="text-xs font-medium text-amber-700 leading-snug">{conflict.reason}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Actions */}
                                                <div className="flex items-center gap-2 shrink-0 justify-end">
                                                    {conflict.status === 'pending' ? (
                                                        <button
                                                            onClick={() => setResolvingConflict(conflict)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600 hover:bg-orange-500 hover:text-white transition-colors"
                                                            title="Perbaiki">
                                                            <Eye className="w-4 h-4" />
                                                        </button>
                                                    ) : (
                                                        <>
                                                            {!isAutoType && !isSuccess && (
                                                                <button
                                                                    onClick={() => handleReprocess(conflict.id)}
                                                                    disabled={processingId === conflict.id}
                                                                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-colors disabled:opacity-50"
                                                                    title="Konfirmasi Import">
                                                                    {processingId === conflict.id ? <Loader2 className="w-4 h-4 animate-spin" /> : <CheckCircle2 className="w-4 h-4" />}
                                                                </button>
                                                            )}
                                                            {conflict.reprocess_status === 'failed' && (
                                                                <button onClick={() => setResolvingConflict(conflict)}
                                                                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white transition-colors"
                                                                    title="Edit Ulang">
                                                                    <Eye className="w-4 h-4" />
                                                                </button>
                                                            )}
                                                            {!isSuccess && (
                                                                <button onClick={() => handleReset(conflict.id)}
                                                                    className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                                    title="Reset ke Pending">
                                                                    <RefreshCcw className="w-4 h-4" />
                                                                </button>
                                                            )}
                                                        </>
                                                    )}
                                                </div>
                                            </div>

                                            {/* Status bar */}
                                            {conflict.reprocess_message && reprocessCfg && (
                                                <div className={cn('px-6 py-2 border-t text-xs font-bold', reprocessCfg.class)}>
                                                    {reprocessCfg.label}: {conflict.reprocess_message}
                                                </div>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <div className="p-12 text-center">
                                <div className="w-64 h-64 mx-auto mb-4">
                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                </div>
                                <h3 className="text-xl font-black text-gray-900">Tidak Ada Konflik</h3>
                                <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto">
                                    Semua data import sudah bersih dan sinkron. Tidak ada isu yang perlu diselesaikan.
                                </p>
                            </div>
                        )}
                    </div>
                </Deferred>
            </div>

            <ResolveModal
                isOpen={!!resolvingConflict}
                onClose={() => setResolvingConflict(null)}
                conflict={resolvingConflict}
                rws={rws}
            />
        </AuthenticatedLayout>
    );
}
