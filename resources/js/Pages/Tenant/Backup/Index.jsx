import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import Swal from 'sweetalert2';
import { PageHeader, TableCard } from '@/Components/Shared';

export default function BackupIndex({ backupFiles, diskSpace, stats }) {
    const handleCreateBackup = (type) => {
        Swal.fire({
            title: 'Buat Backup Baru?',
            text: type === 'full' 
                ? 'Backup full akan memakan waktu lebih lama karena mem-backup seluruh file sistem dan database.'
                : 'Backup database akan menyimpan seluruh data SID Anda.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Buat Backup',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    router.post(route('backup.create'), { type }, {
                        onFinish: () => resolve(),
                        preserveScroll: true
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    };

    const handleRestore = (filename) => {
        Swal.fire({
            title: 'PERINGATAN KERAS!',
            html: `Anda akan me-restore database dari file <b>${filename}</b>.<br/><br/>
                   <span class="text-red-600 font-bold">Semua data saat ini akan ditimpa (dihapus) dan diganti dengan data dari backup ini!</span><br/>
                   Tindakan ini TIDAK BISA dibatalkan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'SAYA YAKIN, RESTORE SEKARANG!',
            cancelButtonText: 'Batal Aman',
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses Restore...',
                    text: 'Mohon tunggu, jangan tutup halaman ini.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                router.post(route('backup.restore'), { filename, confirm: true }, {
                    preserveScroll: true,
                    onFinish: () => {
                        // SweetAlert will be handled by flash message from layout if configured, 
                        // or just let it close if reloading.
                        Swal.close();
                    }
                });
            }
        });
    };

    const handleDelete = (filename) => {
        Swal.fire({
            title: 'Hapus Backup?',
            text: `File backup ${filename} akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('backup.delete', filename), {
                    preserveScroll: true
                });
            }
        });
    };

    const formatBytes = (bytes, decimals = 2) => {
        if (!+bytes) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(dm))} ${sizes[i]}`;
    };

    // Calculate disk space color
    const diskSpaceColor = diskSpace.percentage > 90 ? 'bg-red-500' 
                         : diskSpace.percentage > 75 ? 'bg-amber-500' 
                         : 'bg-emerald-500';

    return (
        <AuthenticatedLayout title="Backup & Restore">
            <Head title="Backup & Restore" />

            <div className="space-y-6">
                {/* Header */}
                <PageHeader
                    title="Backup & Restore"
                    subtitle="Amankan dan Kelola Data Sistem Desa"
                    icon={Icons.Database}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column: Stats & Storage */}
                    <div className="lg:col-span-1 space-y-6">
                        {/* Disk Space Card */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
                            <div className="flex items-center justify-between mb-4">
                                <h3 className="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center">
                                    <Icons.HardDrive className="w-4 h-4 mr-2 text-blue-600" />
                                    Kapasitas Disk Server
                                </h3>
                            </div>
                            
                            <div className="mb-2 flex justify-between items-end">
                                <span className="text-3xl font-black text-gray-900 leading-none">
                                    {diskSpace.percentage}%
                                </span>
                                <span className="text-xs font-bold text-gray-500 uppercase">Digunakan</span>
                            </div>

                            <div className="w-full bg-gray-100 rounded-full h-3 mb-4 overflow-hidden">
                                <div className={`h-3 rounded-full ${diskSpaceColor} transition-all duration-1000`} style={{ width: `${diskSpace.percentage}%` }}></div>
                            </div>

                            <div className="grid grid-cols-2 gap-4 text-xs">
                                <div>
                                    <p className="text-gray-500 font-medium uppercase tracking-wider mb-0.5">Terpakai</p>
                                    <p className="font-bold text-gray-900">{formatBytes(diskSpace.used)}</p>
                                </div>
                                <div>
                                    <p className="text-gray-500 font-medium uppercase tracking-wider mb-0.5">Sisa Ruang</p>
                                    <p className="font-bold text-gray-900">{formatBytes(diskSpace.free)}</p>
                                </div>
                            </div>
                        </div>

                        {/* Quick Stats */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                            <h3 className="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center">
                                <Icons.PieChart className="w-4 h-4 mr-2 text-indigo-600" />
                                Statistik Backup
                            </h3>
                            <div className="space-y-4">
                                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                                    <div className="flex items-center">
                                        <div className="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                                            <Icons.Files className="w-4 h-4 text-blue-600" />
                                        </div>
                                        <span className="text-xs font-bold text-gray-600 uppercase">Total File</span>
                                    </div>
                                    <span className="font-black text-gray-900">{stats.total_files}</span>
                                </div>
                                <div className="flex items-center justify-between p-3 bg-gray-50 rounded-2xl">
                                    <div className="flex items-center">
                                        <div className="w-8 h-8 rounded-xl bg-indigo-100 flex items-center justify-center mr-3">
                                            <Icons.Weight className="w-4 h-4 text-indigo-600" />
                                        </div>
                                        <span className="text-xs font-bold text-gray-600 uppercase">Total Ukuran</span>
                                    </div>
                                    <span className="font-black text-gray-900">{formatBytes(stats.total_size)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Actions & List */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Actions */}
                        {/* Actions */}
                        <div className="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div className="flex items-center gap-4 text-left">
                                <div className="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shrink-0">
                                    <Icons.Database className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-black text-gray-900 uppercase italic tracking-tight text-base">Backup Database Desa</h3>
                                    <p className="text-xs text-gray-500 leading-relaxed max-w-md">Menyimpan data penduduk, pengajuan surat, laporan pengaduan, dan seluruh konfigurasi sistem desa Anda ke dalam berkas backup aman.</p>
                                </div>
                            </div>
                            <button
                                onClick={() => handleCreateBackup('database')}
                                className="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold text-xs uppercase tracking-wider transition-all active:scale-95 shadow-lg shadow-blue-600/10 shrink-0 cursor-pointer"
                            >
                                <Icons.Play className="w-4 h-4 mr-2" />
                                Buat Backup Baru
                            </button>
                        </div>

                        {/* List */}
                        <TableCard
                            title="Riwayat Backup"
                            icon={Icons.History}
                            total={backupFiles.length}
                            totalLabel="File"
                            noPadding
                        >
                            <div className="overflow-x-auto">
                                <table className="w-full text-sm text-left">
                                    <thead className="bg-gray-50/50 text-gray-500 text-xs uppercase font-bold tracking-wider">
                                        <tr>
                                            <th className="px-6 py-4 whitespace-nowrap">File Info</th>
                                            <th className="px-6 py-4">Tipe & Ukuran</th>
                                            <th className="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-100">
                                        {backupFiles.length > 0 ? (
                                            backupFiles.map((backup, index) => {
                                                const isFull = backup.name.includes('full');
                                                const isDb = backup.name.includes('database');
                                                const typeLabel = isFull ? 'Full Backup' : (isDb ? 'Database' : 'Files');
                                                const badgeColor = isFull ? 'bg-indigo-100 text-indigo-700 border-indigo-200' 
                                                                 : 'bg-emerald-100 text-emerald-700 border-emerald-200';

                                                return (
                                                    <tr key={index} className="hover:bg-gray-50/50 transition-colors">
                                                        <td className="px-6 py-4">
                                                            <div className="flex items-start">
                                                                <Icons.FileArchive className="w-5 h-5 text-gray-400 mr-3 mt-0.5 shrink-0" />
                                                                <div>
                                                                    <div className="font-bold text-gray-900 truncate max-w-[200px] sm:max-w-xs" title={backup.name}>
                                                                        {backup.name}
                                                                    </div>
                                                                    <div className="text-xs text-gray-500 mt-1 flex items-center">
                                                                        <Icons.Clock className="w-3 h-3 mr-1" />
                                                                        {backup.created_at ? new Date(backup.created_at).toLocaleString('id-ID', {
                                                                            day: 'numeric', month: 'short', year: 'numeric',
                                                                            hour: '2-digit', minute: '2-digit'
                                                                        }) : 'Unknown'}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4">
                                                            <div className="flex flex-col items-start gap-1">
                                                                <span className={`inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border ${badgeColor}`}>
                                                                    {typeLabel}
                                                                </span>
                                                                <span className="text-xs font-mono text-gray-500">
                                                                    {formatBytes(backup.size)}
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td className="px-6 py-4 text-right">
                                                            <div className="flex items-center justify-end space-x-2">
                                                                {isDb && (
                                                                    <button
                                                                        onClick={() => handleRestore(backup.name)}
                                                                        className="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl transition-colors group relative"
                                                                        title="Restore Database"
                                                                    >
                                                                        <Icons.RotateCcw className="w-4 h-4" />
                                                                    </button>
                                                                )}
                                                                <a
                                                                    href={route('backup.download', backup.name)}
                                                                    className="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-colors"
                                                                    title="Download File"
                                                                >
                                                                    <Icons.Download className="w-4 h-4" />
                                                                </a>
                                                                <button
                                                                    onClick={() => handleDelete(backup.name)}
                                                                    className="p-2 bg-gray-50 text-gray-500 hover:bg-gray-800 hover:text-white rounded-xl transition-colors"
                                                                    title="Hapus Backup"
                                                                >
                                                                    <Icons.Trash2 className="w-4 h-4" />
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                );
                                            })
                                        ) : (
                                            <tr>
                                                <td colSpan="3" className="px-6 py-12 text-center">
                                                    <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <Icons.Inbox className="w-8 h-8 text-gray-300" />
                                                    </div>
                                                    <p className="text-sm font-bold text-gray-900">Belum Ada Backup</p>
                                                    <p className="text-xs text-gray-500 mt-1">Silakan buat backup untuk mengamankan data Anda.</p>
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </TableCard>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
