import React from 'react';
import { Head, usePage, router } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, DataTable, Badge } from '@/Components/Shared';
import { Activity, Database, HardDrive, Users, Server, Clock, ShieldAlert, CheckCircle2, AlertOctagon, Terminal, Trash2, ChevronDown, ChevronUp } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ tenants, systemInfo, logs, landlordLogs, laravelLogs }) {
    const { flash } = usePage().props;
    const [selectedLevel, setSelectedLevel] = React.useState('ALL');
    const [expandedIndex, setExpandedIndex] = React.useState(null);
    const [activeTab, setActiveTab] = React.useState('landlord_audit');

    const tenantColumns = [
        {
            header: 'Desa',
            accessor: 'name',
            className: 'text-left min-w-[150px]',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-black text-slate-800 text-sm">{row.name}</span>
                    <span className="text-gray-400 text-xs font-bold uppercase tracking-wider">ID: {row.id}</span>
                </div>
            )
        },
        {
            header: 'Koneksi DB',
            className: 'text-center',
            render: (row) => (
                <div className="flex justify-center">
                    <Badge 
                        color={row.db_healthy ? 'green' : 'red'}
                        dot={row.db_healthy ? 'green' : 'red'}
                        className="text-[10px]"
                    >
                        {row.db_healthy ? 'Sehat' : 'Error'}
                    </Badge>
                </div>
            )
        },
        {
            header: 'Database Info',
            accessor: 'db_name',
            className: 'text-center',
            render: (row) => (
                <div className="flex flex-col items-center">
                    <span className="font-mono text-slate-600 text-xs bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">{row.db_name}</span>
                    <span className="text-xs text-slate-400 font-bold mt-1 flex items-center gap-1">
                        <Database className="w-3 h-3 text-indigo-400" />
                        {row.db_size_mb} MB
                    </span>
                </div>
            )
        },
        {
            header: 'Pengguna (Aktif / Kuota)',
            className: 'text-center',
            render: (row) => {
                const percent = Math.min(100, Math.round((row.user_count / row.max_users) * 100));
                const barColor = percent > 90 ? 'bg-red-500' : percent > 75 ? 'bg-amber-500' : 'bg-indigo-600';
                return (
                    <div className="flex flex-col items-center max-w-[140px] mx-auto">
                        <div className="flex justify-between w-full text-xs font-bold mb-1">
                            <span className="text-slate-700">{row.user_count} Users</span>
                            <span className="text-slate-400">{row.max_users}</span>
                        </div>
                        <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                            <div className={`h-full ${barColor} rounded-full transition-all duration-500`} style={{ width: `${percent}%` }}></div>
                        </div>
                    </div>
                );
            }
        },
        {
            header: 'Penyimpanan (S3 / MinIO)',
            className: 'text-center',
            render: (row) => {
                const percent = Math.min(100, Math.round((row.storage_used_mb / row.storage_limit_mb) * 100));
                const barColor = percent > 90 ? 'bg-red-500' : percent > 75 ? 'bg-amber-500' : 'bg-indigo-600';
                return (
                    <div className="flex flex-col items-center max-w-[140px] mx-auto">
                        <div className="flex justify-between w-full text-xs font-bold mb-1">
                            <span className="text-slate-700">{row.storage_used_mb} MB</span>
                            <span className="text-slate-400">{row.storage_limit_mb} MB</span>
                        </div>
                        <div className="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/60">
                            <div className={`h-full ${barColor} rounded-full transition-all duration-500`} style={{ width: `${percent}%` }}></div>
                        </div>
                    </div>
                );
            }
        },
        {
            header: 'Status Tenant',
            className: 'text-center',
            render: (row) => (
                <div className="flex justify-center">
                    <Badge 
                        color={row.is_active ? 'green' : 'red'}
                        className="text-[10px]"
                    >
                        {row.is_active ? 'Aktif' : 'Nonaktif'}
                    </Badge>
                </div>
            )
        }
    ];

    const landlordLogColumns = [
        {
            header: 'Aktor (Email / IP)',
            className: 'text-left min-w-[160px]',
            render: (row) => (
                <div className="flex flex-col">
                    <span className="font-bold text-slate-800 text-xs">{row.actor_email || 'System / Auto'}</span>
                    <span className="text-[10px] text-slate-400 font-mono mt-0.5">{row.ip_address || '-'}</span>
                </div>
            )
        },
        {
            header: 'Kejadian (Event)',
            accessor: 'event',
            className: 'text-center w-[160px]',
            render: (row) => {
                const colors = {
                    login_success: 'green',
                    login_failed: 'red',
                    logout: 'slate',
                    account_locked: 'red',
                    two_factor_enabled: 'blue',
                    two_factor_verified: 'emerald',
                    two_factor_disabled: 'amber',
                    tenant_hard_deleted: 'red',
                };
                return (
                    <div className="flex justify-center">
                        <Badge 
                            color={colors[row.event] || 'indigo'}
                            dot={colors[row.event] || 'indigo'}
                            className="text-[10px]"
                        >
                            {row.event ? row.event.replace(/_/g, ' ').toUpperCase() : 'UNKNOWN'}
                        </Badge>
                    </div>
                );
            }
        },
        {
            header: 'Deskripsi Log Audit',
            accessor: 'description',
            className: 'text-left text-slate-700 text-xs font-semibold min-w-[200px]'
        },
        {
            header: 'User Agent / Browser',
            className: 'text-left text-[11px] text-slate-400 max-w-[200px] truncate',
            render: (row) => <span title={row.user_agent}>{row.user_agent || '-'}</span>
        },
        {
            header: 'Waktu Kejadian',
            className: 'text-center text-xs font-bold text-gray-500 w-[150px]',
            render: (row) => new Date(row.created_at).toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            })
        }
    ];

    const logColumns = [
        {
            header: 'Desa',
            className: 'text-left font-bold text-slate-800 text-xs w-[120px]',
            render: (row) => row.tenant?.name || `Desa ${row.tenant_id.toUpperCase()}`
        },
        {
            header: 'Aktivitas',
            accessor: 'action',
            className: 'text-center w-[120px]',
            render: (row) => {
                const colors = {
                    created: 'blue',
                    user_created: 'green',
                    user_deleted: 'red',
                    file_uploaded: 'purple',
                };
                return (
                    <div className="flex justify-center">
                        <Badge 
                            color={colors[row.action] || 'slate'}
                            dot={colors[row.action] || 'slate'}
                            className="text-[10px]"
                        >
                            {row.action.replace('_', ' ').toUpperCase()}
                        </Badge>
                    </div>
                );
            }
        },
        {
            header: 'Detail Deskripsi',
            accessor: 'description',
            className: 'text-left text-slate-600 text-xs font-medium'
        },
        {
            header: 'Waktu Kejadian',
            className: 'text-center text-xs font-bold text-gray-500 w-[160px]',
            render: (row) => new Date(row.created_at).toLocaleString('id-ID', {
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }
    ];

    const handleClearLogs = () => {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: 'Berkas log utama laravel.log di server akan dikosongkan secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Bersihkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('landlord.monitoring.clear-logs'), {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Log error berhasil dikosongkan.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                });
            }
        });
    };

    const levels = ['ALL', 'ERROR', 'WARNING', 'INFO', 'DEBUG'];
    const filteredLaravelLogs = selectedLevel === 'ALL' 
        ? laravelLogs 
        : laravelLogs.filter(log => log.level === selectedLevel);

    const getLevelBadgeColor = (level) => {
        switch (level) {
            case 'ERROR': return 'bg-red-500/10 text-red-400 border-red-500/20';
            case 'WARNING': return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
            case 'INFO': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
            case 'DEBUG': return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
            default: return 'bg-gray-500/10 text-gray-400 border-gray-500/20';
        }
    };

    return (
        <LandlordLayout>
            <Head title="Pemantauan Sistem & Resource" />

            <div className="space-y-8">
                <PageHeader 
                    icon={Activity}
                    title="Pemantauan Sistem & Resource"
                    subtitle="Monitor status kesehatan database, alokasi resource tenant, log audit keamanan, dan metrik server SaaS."
                    gradient="from-indigo-600 via-indigo-700 to-indigo-800"
                />

                {/* Server Status Stats Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div className="bg-slate-900 border border-slate-800/60 p-6 rounded-3xl shadow-xl flex items-center gap-4 text-white">
                        <div className="p-3 bg-indigo-500/10 text-indigo-400 rounded-2xl">
                            <HardDrive className="w-6 h-6" />
                        </div>
                        <div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Kapasitas Disk</div>
                            <div className="text-xl font-black">{systemInfo.disk_used_gb} GB <span className="text-sm font-bold text-slate-400">/ {systemInfo.disk_total_gb} GB</span></div>
                            <div className="text-[10px] text-indigo-400 font-extrabold mt-1">{systemInfo.disk_usage_percent}% Terpakai</div>
                        </div>
                    </div>

                    <div className="bg-slate-900 border border-slate-800/60 p-6 rounded-3xl shadow-xl flex items-center gap-4 text-white">
                        <div className="p-3 bg-blue-500/10 text-blue-400 rounded-2xl">
                            <Server className="w-6 h-6" />
                        </div>
                        <div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Lingkungan Server</div>
                            <div className="text-xl font-black">PHP {systemInfo.php_version}</div>
                            <div className="text-[10px] text-blue-400 font-extrabold mt-1">Laravel Architecture</div>
                        </div>
                    </div>

                    <div className="bg-slate-900 border border-slate-800/60 p-6 rounded-3xl shadow-xl flex items-center gap-4 text-white">
                        <div className="p-3 bg-purple-500/10 text-purple-400 rounded-2xl">
                            <Activity className="w-6 h-6" />
                        </div>
                        <div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Versi Aplikasi</div>
                            <div className="text-xl font-black">v{systemInfo.laravel_version}</div>
                            <div className="text-[10px] text-slate-400 font-extrabold mt-1">Framework Versi</div>
                        </div>
                    </div>

                    <div className="bg-slate-900 border border-slate-800/60 p-6 rounded-3xl shadow-xl flex items-center gap-4 text-white">
                        <div className="p-3 bg-emerald-500/10 text-emerald-400 rounded-2xl">
                            <Users className="w-6 h-6" />
                        </div>
                        <div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sistem Operasi</div>
                            <div className="text-xl font-black truncate max-w-[180px]">{systemInfo.os}</div>
                            <div className="text-[10px] text-emerald-400 font-extrabold mt-1">Status Sehat</div>
                        </div>
                    </div>
                </div>

                {/* Tabs Navigation Bar */}
                <div className="flex flex-wrap items-center justify-between gap-4 bg-white/80 backdrop-blur border border-slate-200/80 p-2.5 rounded-3xl shadow-sm">
                    <div className="flex flex-wrap gap-2 w-full sm:w-auto">
                        {[
                            { id: 'landlord_audit', label: 'Log Audit Keamanan Landlord', icon: ShieldAlert, count: landlordLogs?.total || 0, badgeClass: 'bg-red-500 text-white' },
                            { id: 'tenants', label: 'Kesehatan & Resource Desa', icon: Database, count: tenants.length, badgeClass: 'bg-indigo-100 text-indigo-700' },
                            { id: 'tenant_audit', label: 'Log Aktivitas Tenant Desa', icon: Clock, count: logs?.total || 0, badgeClass: 'bg-slate-100 text-slate-600' },
                            { id: 'laravel_logs', label: 'System Debugger (laravel.log)', icon: Terminal },
                        ].map((tab) => {
                            const Icon = tab.icon;
                            const isActive = activeTab === tab.id;
                            return (
                                <button
                                    key={tab.id}
                                    onClick={() => setActiveTab(tab.id)}
                                    className={`flex items-center gap-2.5 px-4 py-2.5 rounded-2xl font-bold text-xs transition-all ${
                                        isActive 
                                            ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20 scale-[1.02]' 
                                            : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80'
                                    }`}
                                >
                                    <Icon className={`w-4 h-4 ${isActive ? 'text-indigo-400' : 'text-slate-400'}`} />
                                    <span>{tab.label}</span>
                                    {tab.count !== undefined && (
                                        <span className={`px-2 py-0.5 rounded-full text-[10px] font-black ${
                                            isActive ? 'bg-white/20 text-white' : tab.badgeClass
                                        }`}>
                                            {tab.count}
                                        </span>
                                    )}
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* Tab Content Section */}
                <div className="space-y-8">
                    {/* Tab 1: Landlord Audit Logs */}
                    {activeTab === 'landlord_audit' && (
                        <TableCard
                            title="Log Audit Keamanan (Admin Panel Central)"
                            icon={ShieldAlert}
                            total={landlordLogs?.total || 0}
                            totalLabel="Kejadian Keamanan"
                            pagination={landlordLogs}
                            noPadding
                        >
                            {!landlordLogs || landlordLogs.data.length === 0 ? (
                                <div className="p-16 text-center text-gray-400 font-bold text-sm space-y-2">
                                    <ShieldAlert className="w-10 h-10 text-gray-300 mx-auto" />
                                    <p>Belum ada insiden atau aktivitas keamanan Landlord yang terekam.</p>
                                </div>
                            ) : (
                                <DataTable 
                                    columns={landlordLogColumns}
                                    data={landlordLogs.data}
                                    borderedBody={true}
                                />
                            )}
                        </TableCard>
                    )}

                    {/* Tab 2: Tenants Health Overview */}
                    {activeTab === 'tenants' && (
                        <TableCard
                            title="Kesehatan Database & Resource Desa"
                            icon={Database}
                            total={tenants.length}
                            totalLabel="Desa"
                            noPadding
                        >
                            <DataTable 
                                columns={tenantColumns}
                                data={tenants}
                                borderedBody={true}
                            />
                        </TableCard>
                    )}

                    {/* Tab 3: Tenant Activity Audit Logs */}
                    {activeTab === 'tenant_audit' && (
                        <TableCard
                            title="Jejak Aktivitas & Audit Logs Desa"
                            icon={Clock}
                            total={logs?.total || 0}
                            totalLabel="Log Kejadian"
                            pagination={logs}
                            noPadding
                        >
                            {!logs || logs.data.length === 0 ? (
                                <div className="p-16 text-center text-gray-400 font-bold text-sm space-y-2">
                                    <Clock className="w-10 h-10 text-gray-300 mx-auto" />
                                    <p>Belum ada catatan aktivitas tenant desa yang terekam.</p>
                                </div>
                            ) : (
                                <DataTable 
                                    columns={logColumns}
                                    data={logs.data}
                                    borderedBody={true}
                                />
                            )}
                        </TableCard>
                    )}

                    {/* Tab 4: Laravel Debug Log Terminal */}
                    {activeTab === 'laravel_logs' && (
                        <div className="bg-slate-900 border border-slate-800 rounded-3xl shadow-xl overflow-hidden flex flex-col">
                            {/* Terminal Header */}
                            <div className="bg-slate-950 px-6 py-4 border-b border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div className="flex items-center gap-3">
                                    <div className="flex gap-1.5">
                                        <span className="w-3 h-3 rounded-full bg-red-500/80 inline-block"></span>
                                        <span className="w-3 h-3 rounded-full bg-yellow-500/80 inline-block"></span>
                                        <span className="w-3 h-3 rounded-full bg-green-500/80 inline-block"></span>
                                    </div>
                                    <div className="h-4 w-px bg-slate-800 hidden sm:block"></div>
                                    <div className="flex items-center gap-2 text-slate-300 font-mono text-sm font-bold">
                                        <Terminal className="w-4 h-4 text-indigo-400" />
                                        laravel.log ~ System Debugger
                                    </div>
                                </div>
                                
                                <div className="flex items-center gap-3 w-full sm:w-auto justify-end">
                                    {/* Level Filters */}
                                    <div className="flex bg-slate-900 border border-slate-800 p-0.5 rounded-xl text-xs font-mono">
                                        {levels.map(level => (
                                            <button
                                                key={level}
                                                onClick={() => {
                                                    setSelectedLevel(level);
                                                    setExpandedIndex(null);
                                                }}
                                                className={`px-2.5 py-1 rounded-lg font-bold transition-all ${
                                                    selectedLevel === level 
                                                        ? 'bg-slate-850 text-indigo-400 border border-slate-800 shadow-md' 
                                                        : 'text-slate-500 hover:text-slate-300'
                                                }`}
                                            >
                                                {level}
                                            </button>
                                        ))}
                                    </div>

                                    {/* Clear log button */}
                                    <button
                                        onClick={handleClearLogs}
                                        className="p-2 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 rounded-xl transition-all flex items-center justify-center gap-1.5 text-xs font-bold font-mono"
                                        title="Kosongkan Berkas Log"
                                    >
                                        <Trash2 className="w-4 h-4" />
                                        <span className="hidden md:inline">Clear</span>
                                    </button>
                                </div>
                            </div>

                            {/* Terminal Console View */}
                            <div className="p-6 bg-slate-950/80 max-h-[500px] overflow-y-auto font-mono text-xs text-slate-300 space-y-2.5 min-h-[250px] scrollbar-thin scrollbar-thumb-slate-800 scrollbar-track-transparent">
                                {filteredLaravelLogs.length === 0 ? (
                                    <div className="py-16 text-center text-slate-500 font-bold space-y-2">
                                        <CheckCircle2 className="w-8 h-8 text-emerald-500/40 mx-auto" />
                                        <p>Log Bersih / Tidak ada pesan error yang terdeteksi.</p>
                                    </div>
                                ) : (
                                    filteredLaravelLogs.map((log, idx) => {
                                        const isExpanded = expandedIndex === idx;
                                        return (
                                            <div 
                                                key={idx}
                                                className={`border border-slate-900 rounded-xl overflow-hidden transition-all ${
                                                    isExpanded ? 'bg-slate-900/60 border-slate-800' : 'bg-slate-950/50 hover:bg-slate-900/30'
                                                }`}
                                            >
                                                {/* Header Row */}
                                                <div 
                                                    onClick={() => setExpandedIndex(isExpanded ? null : idx)}
                                                    className="p-3.5 flex items-start gap-3 cursor-pointer justify-between"
                                                >
                                                    <div className="flex items-start gap-3 min-w-0">
                                                        <span className="text-slate-500 shrink-0 font-bold mt-0.5">[{log.timestamp}]</span>
                                                        <span className={`px-2 py-0.5 rounded text-[10px] font-black tracking-wider border shrink-0 ${getLevelBadgeColor(log.level)}`}>
                                                            {log.level}
                                                        </span>
                                                        <p className="text-slate-200 truncate font-semibold">{log.message}</p>
                                                    </div>
                                                    <div className="text-slate-500 flex items-center shrink-0 ml-4">
                                                        {isExpanded ? <ChevronUp className="w-4 h-4" /> : <ChevronDown className="w-4 h-4" />}
                                                    </div>
                                                </div>

                                                {/* Expanded Body (Full Trace) */}
                                                {isExpanded && (
                                                    <div className="px-4 pb-4 pt-1 border-t border-slate-900/80 bg-slate-950/40">
                                                        <pre className="text-slate-400 whitespace-pre-wrap leading-relaxed overflow-x-auto text-[11px] p-3.5 bg-slate-950 rounded-xl border border-slate-900 mt-2 selection:bg-indigo-500 selection:text-white">
                                                            {log.message}
                                                        </pre>
                                                    </div>
                                                )}
                                            </div>
                                        );
                                    })
                                )}
                            </div>

                            {/* Terminal Footer */}
                            <div className="bg-slate-950 px-6 py-3 border-t border-slate-800 text-[10px] text-slate-500 font-mono flex justify-between items-center">
                                <span>Loaded: Last 100 Entries (Max 150KB)</span>
                                <span className="flex items-center gap-1.5">
                                    <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                    System Online
                                </span>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </LandlordLayout>
    );
}
