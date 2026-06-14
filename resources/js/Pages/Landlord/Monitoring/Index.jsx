import React from 'react';
import { Head, usePage } from '@inertiajs/react';
import LandlordLayout from '@/Layouts/LandlordLayout';
import { PageHeader, TableCard, DataTable, Badge } from '@/Components/Shared';
import { Activity, Database, HardDrive, Users, Server, Clock, ShieldAlert } from 'lucide-react';

export default function Index({ tenants, systemInfo, logs }) {
    const { flash } = usePage().props;

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
                    <div className="flex flex-col items-center w-full max-w-[150px] mx-auto">
                        <div className="flex justify-between w-full text-xs font-black text-slate-700 mb-1">
                            <span>{row.user_count} User</span>
                            <span className="text-gray-400">/ {row.max_users} Limit</span>
                        </div>
                        <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div className={`h-full ${barColor} transition-all`} style={{ width: `${percent}%` }}></div>
                        </div>
                    </div>
                );
            }
        },
        {
            header: 'Penyimpanan (Storage)',
            className: 'text-center',
            render: (row) => {
                const percent = Math.min(100, Math.round((row.storage_used_mb / row.storage_limit_mb) * 100));
                const barColor = percent > 90 ? 'bg-red-500' : percent > 75 ? 'bg-amber-500' : 'bg-indigo-600';
                return (
                    <div className="flex flex-col items-center w-full max-w-[150px] mx-auto">
                        <div className="flex justify-between w-full text-xs font-black text-slate-700 mb-1">
                            <span>{row.storage_used_mb.toFixed(1)} MB</span>
                            <span className="text-gray-400">/ {row.storage_limit_mb} MB</span>
                        </div>
                        <div className="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div className={`h-full ${barColor} transition-all`} style={{ width: `${percent}%` }}></div>
                        </div>
                    </div>
                );
            }
        },
        {
            header: 'Status',
            className: 'text-center',
            render: (row) => (
                <div className="flex justify-center">
                    <Badge 
                        color={row.is_active ? 'green' : 'red'}
                        dot={row.is_active ? 'green' : 'red'}
                    >
                        {row.is_active ? 'Aktif' : 'Nonaktif'}
                    </Badge>
                </div>
            )
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

    return (
        <LandlordLayout>
            <Head title="Pemantauan Sistem & Resource" />

            <div className="space-y-8">
                <PageHeader 
                    icon={Activity}
                    title="Pemantauan Sistem & Resource"
                    subtitle="Monitor status kesehatan database, alokasi resource tenant, dan metrik server SaaS."
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
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PHP Version</div>
                            <div className="text-xl font-black">{systemInfo.php_version}</div>
                            <div className="text-[10px] text-slate-400 font-extrabold mt-1">Engine Versi</div>
                        </div>
                    </div>

                    <div className="bg-slate-900 border border-slate-800/60 p-6 rounded-3xl shadow-xl flex items-center gap-4 text-white">
                        <div className="p-3 bg-purple-500/10 text-purple-400 rounded-2xl">
                            <Database className="w-6 h-6" />
                        </div>
                        <div>
                            <div className="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Laravel Framework</div>
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

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {/* Tenants Health Overview */}
                    <div className="lg:col-span-12">
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
                    </div>

                    {/* Audit Logs */}
                    <div className="lg:col-span-12">
                        <TableCard
                            title="Jejak Aktivitas & Audit Logs Desa"
                            icon={Clock}
                            total={logs.total}
                            totalLabel="Log Kejadian"
                            pagination={logs}
                            noPadding
                        >
                            {logs.data.length === 0 ? (
                                <div className="p-12 text-center text-gray-400 font-bold text-sm">
                                    <ShieldAlert className="w-8 h-8 text-gray-300 mx-auto mb-2" />
                                    Belum ada catatan aktivitas yang terekam.
                                </div>
                            ) : (
                                <DataTable 
                                    columns={logColumns}
                                    data={logs.data}
                                    borderedBody={true}
                                />
                            )}
                        </TableCard>
                    </div>
                </div>
            </div>
        </LandlordLayout>
    );
}
