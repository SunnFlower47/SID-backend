import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader } from '@/Components/Shared';
import { 
    ChevronLeft, Calendar, User, Globe, Database, 
    Terminal, FileText, GitCompare, HardDrive, Info
} from 'lucide-react';
import { cn } from '@/lib/utils';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

export default function Show({ auth, activity }) {
    const properties = activity.properties || {};
    const isUpdated = activity.event === 'updated';
    const oldValues = properties.old || {};
    const newValues = properties.attributes || {};
    
    // Get fields that actually changed
    const changedFields = isUpdated 
        ? Object.keys(newValues).filter(field => {
            const oldVal = oldValues[field];
            const newVal = newValues[field];
            // Compare stringified versions if they are objects, otherwise direct comparison
            if (typeof oldVal === 'object' && typeof newVal === 'object') {
                return JSON.stringify(oldVal) !== JSON.stringify(newVal);
            }
            return String(oldVal) !== String(newVal);
        })
        : [];

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
                return 'bg-blue-100 text-blue-800';
            case 'POST':
                return 'bg-green-100 text-green-800';
            case 'PUT':
            case 'PATCH':
                return 'bg-yellow-100 text-yellow-800';
            case 'DELETE':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const getModelName = (classPath) => {
        if (!classPath) return '-';
        return classPath.split('\\').pop();
    };

    const formatValue = (val) => {
        if (val === null || val === undefined) return '-';
        if (typeof val === 'object') return <pre className="text-xs font-mono whitespace-pre-wrap">{JSON.stringify(val, null, 2)}</pre>;
        return String(val);
    };

    // Filter out standard keys ('old', 'attributes') to find other custom properties
    const customProperties = Object.keys(properties).filter(
        key => key !== 'old' && key !== 'attributes'
    );

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Audit Log">
            <Head title={`Detail Audit Log #${activity.id}`} />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20 text-left">
                {/* Header */}
                <PageHeader
                    title="Detail Audit Log"
                    subtitle={`Log ID #${activity.id} • ${activity.event}`}
                    icon={Terminal}
                    titleSize="sm"
                    backHref={route('audit-log.index')}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column - Main Details & Changes */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Summary Card */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6">
                            <div>
                                <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <Info className="w-4 h-4 text-green-600" />
                                    Deskripsi Aktivitas
                                </h3>
                                <p className="text-base font-bold text-gray-900 leading-relaxed bg-gray-50 p-4 rounded-2xl border border-gray-100 italic">
                                    "{activity.description || 'Tidak ada deskripsi aktivitas.'}"
                                </p>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Model Terkait (Subject)</label>
                                    <div className="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-50">
                                        <Database className="w-4 h-4 text-gray-400" />
                                        <span className="text-xs font-bold text-gray-800">{getModelName(activity.subject_type)}</span>
                                        <span className="text-[10px] font-bold text-gray-400 bg-gray-200/60 px-2 py-0.5 rounded">ID {activity.subject_id || '-'}</span>
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Waktu Aktivitas</label>
                                    <div className="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-50">
                                        <Calendar className="w-4 h-4 text-gray-400" />
                                        <span className="text-xs font-bold text-gray-800">
                                            {dayjs(activity.created_at).format('DD MMMM YYYY • HH:mm:ss')}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Changes Card (only for update event) */}
                        {isUpdated && changedFields.length > 0 && (
                            <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                                <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <GitCompare className="w-4 h-4 text-yellow-600" />
                                    Perubahan Data
                                </h3>

                                <div className="space-y-4">
                                    {changedFields.map(field => (
                                        <div key={field} className="border border-gray-100 rounded-2xl p-4 bg-gray-50/30">
                                            <h4 className="text-xs font-black text-gray-700 uppercase tracking-widest mb-3 pb-2 border-b border-gray-100">
                                                {field.replace(/_/g, ' ')}
                                            </h4>
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div className="space-y-1">
                                                    <label className="text-[9px] font-black text-red-500 uppercase tracking-widest">Nilai Lama</label>
                                                    <div className="bg-red-50/50 text-red-800 p-3 rounded-xl border border-red-100 text-xs font-medium min-h-[40px] whitespace-pre-wrap">
                                                        {formatValue(oldValues[field])}
                                                    </div>
                                                </div>
                                                <div className="space-y-1">
                                                    <label className="text-[9px] font-black text-green-600 uppercase tracking-widest">Nilai Baru</label>
                                                    <div className="bg-green-50/50 text-green-800 p-3 rounded-xl border border-green-100 text-xs font-bold min-h-[40px] whitespace-pre-wrap">
                                                        {formatValue(newValues[field])}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Custom / Non-standard Properties */}
                        {customProperties.length > 0 && (
                            <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                                <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                                    <HardDrive className="w-4 h-4 text-purple-600" />
                                    Informasi Tambahan (Metadata)
                                </h3>
                                <div className="space-y-4">
                                    {customProperties.map(key => (
                                        <div key={key} className="space-y-1">
                                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                                                {key.replace(/_/g, ' ')}
                                            </label>
                                            <div className="bg-gray-50 p-4 rounded-xl border border-gray-100 text-xs font-mono whitespace-pre-wrap">
                                                {formatValue(properties[key])}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* Raw JSON View */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                            <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <FileText className="w-4 h-4 text-gray-500" />
                                Raw Properties JSON
                            </h3>
                            <div className="bg-gray-950 text-gray-100 p-4 rounded-2xl overflow-x-auto text-xs font-mono max-h-[300px]">
                                <pre>{JSON.stringify(properties, null, 2)}</pre>
                            </div>
                        </div>
                    </div>

                    {/* Right Column - User & Network Details */}
                    <div className="space-y-6">
                        {/* User Card */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                            <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Aktor / User</h3>
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center shrink-0 border border-green-100 shadow-inner">
                                    <User className="w-6 h-6" />
                                </div>
                                <div className="space-y-1">
                                    <p className="text-sm font-black text-gray-900 leading-none">
                                        {activity.causer?.name ?? 'System / Engine'}
                                    </p>
                                    {activity.causer?.email ? (
                                        <p className="text-[10px] text-gray-400 font-bold tracking-tight">
                                            {activity.causer.email}
                                        </p>
                                    ) : (
                                        <p className="text-[10px] text-gray-400 font-bold tracking-tight uppercase">
                                            Aktivitas Sistem Otomatis
                                        </p>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Network Card */}
                        <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">
                            <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest">Informasi Jaringan</h3>
                            
                            <div className="space-y-4">
                                <div className="flex items-center justify-between py-2 border-b border-gray-50">
                                    <div className="flex items-center gap-2">
                                        <Globe className="w-4 h-4 text-gray-400" />
                                        <span className="text-xs font-bold text-gray-600">IP Address</span>
                                    </div>
                                    <span className="text-xs font-mono font-bold text-gray-800">{activity.ip_address || '-'}</span>
                                </div>

                                <div className="flex items-center justify-between py-2 border-b border-gray-50">
                                    <div className="flex items-center gap-2">
                                        <Terminal className="w-4 h-4 text-gray-400" />
                                        <span className="text-xs font-bold text-gray-600">Method</span>
                                    </div>
                                    {activity.method ? (
                                        <span className={cn("text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded", getMethodBadge(activity.method))}>
                                            {activity.method}
                                        </span>
                                    ) : '-'}
                                </div>

                                <div className="flex items-center justify-between py-2 border-b border-gray-50">
                                    <div className="flex items-center gap-2">
                                        <Terminal className="w-4 h-4 text-gray-400" />
                                        <span className="text-xs font-bold text-gray-600">Aksi Event</span>
                                    </div>
                                    <span className={cn("text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded", getEventBadge(activity.event))}>
                                        {activity.event}
                                    </span>
                                </div>

                                <div className="space-y-1">
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Request URL</span>
                                    <div className="p-3 bg-gray-50 rounded-xl border border-gray-50 font-mono text-[10px] text-gray-600 break-all leading-relaxed">
                                        {activity.url || '-'}
                                    </div>
                                </div>

                                {activity.user_agent && (
                                    <div className="space-y-1">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest block">User Agent</span>
                                        <div className="p-3 bg-gray-50 rounded-xl border border-gray-50 text-[10px] text-gray-500 break-all leading-relaxed">
                                            {activity.user_agent}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
