import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PengaduanStats from '@/Components/Pengaduan/PengaduanStats';
import PengaduanFilters from '@/Components/Pengaduan/PengaduanFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { MessageSquare, Plus, Edit, Eye, Trash2, AlertTriangle, CheckCircle, Clock, XCircle, FileText } from 'lucide-react';
import Swal from 'sweetalert2';

const PRIORITY_COLORS = {
    rendah: 'bg-green-100 text-green-800',
    sedang: 'bg-yellow-100 text-yellow-800',
    tinggi: 'bg-orange-100 text-orange-800',
    darurat: 'bg-red-100 text-red-800 animate-pulse',
};

const STATUS_COLORS = {
    baru: { bg: 'bg-blue-100', text: 'text-blue-800', icon: AlertTriangle },
    diproses: { bg: 'bg-purple-100', text: 'text-purple-800', icon: Clock },
    selesai: { bg: 'bg-green-100', text: 'text-green-800', icon: CheckCircle },
    ditolak: { bg: 'bg-gray-100', text: 'text-gray-800', icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.baru;
    const Icon = cfg.icon;
    return (
        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${cfg.bg} ${cfg.text}`}>
            <Icon className="w-3 h-3" />
            {status}
        </span>
    );
}

function PriorityBadge({ prioritas }) {
    const color = PRIORITY_COLORS[prioritas] || PRIORITY_COLORS.rendah;
    return (
        <span className={`inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider ${color}`}>
            {prioritas}
        </span>
    );
}

export default function Index({ auth, pengaduans, stats, filters }) {
    const handleDelete = (id, judul) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus aduan <b class="text-red-600">${judul}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('pengaduan.destroy', id), {
                    preserveScroll: true
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Pengaduan Warga">
            <Head title="Pengaduan Warga" />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <MessageSquare className="w-6 h-6 sm:w-7 sm:h-7 text-green-50" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Pengaduan Warga
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Manajemen Laporan & Keluhan Masyarakat
                                </p>
                            </div>
                        </div>
                        <div>
                            <Link
                                href={route('pengaduan.create')}
                                className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                            >
                                <Plus className="w-3.5 h-3.5 mr-2" />
                                TAMBAH MANUAL
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <PengaduanStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <PengaduanFilters filters={filters} />

                {/* Data Table */}
                <Deferred data="pengaduans" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    {/* Desktop View */}
                    <div className="hidden lg:block overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-gray-50/80 border-b border-gray-100">
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Tanggal</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Aduan</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Pelapor</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center">Status</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {pengaduans.data.length > 0 ? (
                                    pengaduans.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-green-50/20 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <p className="text-xs font-bold text-gray-900">
                                                    {new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </p>
                                                <p className="text-[10px] font-medium text-gray-400 mt-0.5">
                                                    {new Date(item.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                                                </p>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex flex-col gap-1">
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-[9px] font-black uppercase tracking-wider text-green-600 bg-green-50 px-2 py-0.5 rounded">
                                                            {item.kategori}
                                                        </span>
                                                        <PriorityBadge prioritas={item.prioritas} />
                                                    </div>
                                                    <p className="font-bold text-gray-900 mt-1 line-clamp-1">{item.judul}</p>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <p className="text-sm font-bold text-gray-900">{item.nama_pelapor}</p>
                                                {item.nik_pelapor && <p className="text-[10px] font-mono text-gray-400 mt-0.5">{item.nik_pelapor}</p>}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge status={item.status} />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link
                                                        href={route('pengaduan.show', item.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                        title="Detail"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    <Link
                                                        href={route('pengaduan.edit', item.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                                                        title="Tanggapi/Edit Status"
                                                    >
                                                        <Edit className="w-4 h-4" />
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(item.id, item.judul)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center">
                                            <div className="flex flex-col items-center justify-center">
                                                <FileText className="w-12 h-12 text-gray-200 mb-3" />
                                                <p className="text-sm font-bold text-gray-400">Belum ada aduan yang masuk.</p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile View */}
                    <div className="lg:hidden p-4 space-y-4 bg-gray-50">
                        {pengaduans.data.length > 0 ? (
                            pengaduans.data.map((item) => (
                                <div key={item.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                    <div className="flex justify-between items-start mb-3">
                                        <div className="flex gap-2">
                                            <StatusBadge status={item.status} />
                                            <PriorityBadge prioritas={item.prioritas} />
                                        </div>
                                        <p className="text-[10px] font-bold text-gray-400">
                                            {new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                                        </p>
                                    </div>
                                    <h4 className="font-black text-gray-900 leading-snug mb-1">{item.judul}</h4>
                                    <p className="text-xs text-gray-500 mb-4">{item.nama_pelapor}</p>
                                    
                                    <div className="flex gap-2 border-t border-gray-50 pt-3">
                                        <Link href={route('pengaduan.show', item.id)} className="flex-1 text-center py-2 bg-blue-50 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest">Detail</Link>
                                        <Link href={route('pengaduan.edit', item.id)} className="flex-1 text-center py-2 bg-gray-100 text-gray-700 rounded-xl text-[10px] font-black uppercase tracking-widest">Tanggapi</Link>
                                        <button onClick={() => handleDelete(item.id, item.judul)} className="w-10 flex items-center justify-center bg-red-50 text-red-600 rounded-xl"><Trash2 className="w-4 h-4" /></button>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="text-center py-10 bg-white rounded-2xl">
                                <p className="text-sm font-bold text-gray-400">Belum ada aduan.</p>
                            </div>
                        )}
                    </div>

                    {/* Pagination */}
                    {pengaduans.links && pengaduans.links.length > 3 && (
                        <div className="px-6 py-4 border-t border-gray-100 bg-gray-50 flex flex-wrap justify-center gap-1">
                            {pengaduans.links.map((link, k) => (
                                <Link
                                    key={k}
                                    href={link.url || '#'}
                                    className={`px-3 py-1.5 text-xs font-bold rounded-lg transition-colors ${
                                        link.active 
                                            ? 'bg-green-600 text-white shadow-md' 
                                            : link.url 
                                                ? 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200' 
                                                : 'bg-transparent text-gray-400 cursor-not-allowed'
                                    }`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    )}
                </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
