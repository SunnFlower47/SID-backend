import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm, Deferred } from '@inertiajs/react';
import { 
    AlertCircle, 
    ArrowLeft, 
    CheckCircle2, 
    XCircle, 
    RefreshCcw, 
    Eye, 
    Search,
    Filter,
    User,
    MapPin,
    AlertTriangle,
    Check,
    Loader2
} from 'lucide-react';
import ResolveModal from '@/Components/Import/ResolveModal';

export default function ImportConflicts({ auth, conflicts, rws, filters }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [processingId, setProcessingId] = useState(null);
    const [resolvingConflict, setResolvingConflict] = useState(null);

    const handleResolve = (id, action, additionalData = {}) => {
        setProcessingId(id);
        router.post(route('import-conflicts.resolve', id), {
            action,
            ...additionalData
        }, {
            onFinish: () => setProcessingId(null)
        });
    };

    const handleReprocess = (id) => {
        setProcessingId(id);
        router.post(route('import-conflicts.reprocess', id), {}, {
            onFinish: () => setProcessingId(null)
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex items-center gap-4">
                    <button 
                        onClick={() => window.history.back()}
                        className="p-2 bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all"
                    >
                        <ArrowLeft className="w-5 h-5" />
                    </button>
                    <div>
                        <h2 className="text-2xl font-black text-white tracking-tight">Resolusi Konflik Import</h2>
                        <p className="text-blue-100 text-xs font-bold uppercase tracking-widest opacity-80">Selesaikan isu sinkronisasi data wilayah & penduduk</p>
                    </div>
                </div>
            }
        >
            <Head title="Import Conflicts" />

            <div className="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-6">
                {/* Stats & Filters */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div className="md:col-span-2 bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div className="relative flex-1">
                            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input 
                                type="text" 
                                placeholder="Cari Batch ID atau Nama Penduduk..."
                                className="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-100 transition-all"
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                            />
                        </div>
                        <div className="flex items-center gap-2">
                            <button className="flex items-center gap-2 px-4 py-2 bg-gray-50 text-gray-500 rounded-xl text-xs font-bold hover:bg-gray-100 transition-all">
                                <Filter className="w-4 h-4" />
                                {filters.status || 'Semua Status'}
                            </button>
                        </div>
                    </div>
                    <div className="bg-amber-50 p-4 rounded-3xl border border-amber-100 flex items-center gap-4">
                        <div className="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                            <AlertCircle className="w-6 h-6 text-amber-600" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-amber-800 uppercase tracking-tight">Perhatian</p>
                            <p className="text-[10px] text-amber-600 font-bold leading-tight mt-0.5">
                                Segera selesaikan isu NIK/Wilayah agar data kependudukan tetap sinkron.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Conflict List */}
                <Deferred data="conflicts" fallback={
                    <div className="space-y-4">
                        {[1,2,3].map(i => <div key={i} className="h-40 bg-white rounded-3xl animate-pulse"></div>)}
                    </div>
                }>
                        <div className="space-y-4">
                            {conflicts?.data?.map((conflict) => (
                                <div key={conflict.id} className={`bg-white rounded-3xl shadow-sm border transition-all ${
                                    conflict.status === 'resolved' ? 'border-green-100 opacity-80' : 'border-gray-100 hover:border-blue-200'
                                }`}>
                                    <div className="p-6">
                                        <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                            <div className="flex gap-4">
                                                <div className={`w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ${
                                                    conflict.issue_type === 'nik_conflict' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600'
                                                }`}>
                                                    {conflict.issue_type === 'nik_conflict' ? <User className="w-6 h-6" /> : <MapPin className="w-6 h-6" />}
                                                </div>
                                                <div className="space-y-1">
                                                    <div className="flex items-center gap-2">
                                                        <h4 className="text-sm font-black text-gray-900 uppercase">{conflict.nama || 'Tanpa Nama'}</h4>
                                                        <span className="text-[9px] font-black bg-gray-100 px-2 py-0.5 rounded-full text-gray-500 tracking-widest uppercase italic">
                                                            Batch: {conflict.batch_id}
                                                        </span>
                                                    </div>
                                                    <p className="text-[10px] font-bold text-gray-400">NIK: {conflict.nik || '—'} | NKK: {conflict.nkk || '—'}</p>
                                                    <div className="flex items-center gap-2 mt-2">
                                                        <AlertTriangle className="w-3.5 h-3.5 text-amber-500" />
                                                        <p className="text-[11px] font-bold text-amber-700 leading-tight">
                                                            {conflict.issue_message}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div className="flex flex-wrap items-center gap-3 lg:justify-end">
                                                {conflict.status === 'pending' ? (
                                                    <button 
                                                        onClick={() => setResolvingConflict(conflict)}
                                                        disabled={processingId === conflict.id}
                                                        className="flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-sm shadow-blue-200"
                                                    >
                                                        <Eye className="w-3.5 h-3.5" /> Perbaiki Data
                                                    </button>
                                                ) : (
                                                    <div className="flex items-center gap-4">
                                                        <div className="text-right">
                                                            <div className="flex items-center gap-1 justify-end">
                                                                <CheckCircle2 className="w-3.5 h-3.5 text-green-500" />
                                                                <span className="text-[10px] font-black text-green-600 uppercase tracking-widest">Resolved</span>
                                                            </div>
                                                            <p className="text-[9px] font-bold text-gray-400 uppercase italic">Aksi: {conflict.resolution_action?.replace(/_/g, ' ')}</p>
                                                        </div>
                                                        {conflict.reprocess_status !== 'success' && (
                                                            <button 
                                                                onClick={() => handleReprocess(conflict.id)}
                                                                disabled={processingId === conflict.id}
                                                                className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase shadow-md shadow-blue-900/20 hover:scale-105 transition-all disabled:opacity-50"
                                                            >
                                                                {processingId === conflict.id ? <Loader2 className="w-3 h-3 animate-spin" /> : <RefreshCcw className="w-3 h-3" />}
                                                                Reprocess
                                                            </button>
                                                        )}
                                                        <button 
                                                            onClick={() => router.post(route('import-conflicts.reset', conflict.id))}
                                                            className="p-2 bg-gray-100 text-gray-400 rounded-xl hover:text-red-500 transition-all"
                                                        >
                                                            <RefreshCcw className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                    {conflict.reprocess_message && (
                                        <div className={`px-6 py-2 border-t text-[10px] font-bold italic ${
                                            conflict.reprocess_status === 'success' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-red-50 border-red-100 text-red-600'
                                        }`}>
                                            Status: {conflict.reprocess_message}
                                        </div>
                                    )}
                                </div>
                            ))}

                            {(!conflicts?.data || conflicts.data.length === 0) && (
                                <div className="py-20 bg-white rounded-3xl border border-dashed border-gray-200 text-center">
                                    <div className="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <CheckCircle2 className="w-8 h-8 text-green-500" />
                                    </div>
                                    <h4 className="text-sm font-black text-gray-900 uppercase tracking-tight">Tidak ada konflik</h4>
                                    <p className="text-[10px] text-gray-400 font-bold uppercase mt-1 tracking-widest italic">Semua data import bersih dan sinkron</p>
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
