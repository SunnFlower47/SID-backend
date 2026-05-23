import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PengaduanStats from '@/Components/Pengaduan/PengaduanStats';
import PengaduanFilters from '@/Components/Pengaduan/PengaduanFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { MessageSquare, Plus, Edit, Eye, Trash2, AlertTriangle, CheckCircle, Clock, XCircle, FileText } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

// Shared Components
import { PageHeader, TableCard, Badge } from '@/Components/Shared';

const LottieComponent = Lottie?.default || Lottie;

const PRIORITY_COLORS = {
    rendah: { color: 'green', pulse: false },
    sedang: { color: 'yellow', pulse: false },
    tinggi: { color: 'orange', pulse: false },
    darurat: { color: 'red', pulse: true },
};

const STATUS_COLORS = {
    baru: { color: 'blue', icon: AlertTriangle },
    diproses: { color: 'purple', icon: Clock },
    selesai: { color: 'green', icon: CheckCircle },
    ditolak: { color: 'gray', icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.baru;
    return <Badge color={cfg.color} icon={cfg.icon}>{status}</Badge>;
}

function PriorityBadge({ prioritas }) {
    const cfg = PRIORITY_COLORS[prioritas] || PRIORITY_COLORS.rendah;
    return <Badge color={cfg.color} size="sm" pulse={cfg.pulse}>{prioritas}</Badge>;
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
                <PageHeader 
                    title="Pengaduan Warga"
                    subtitle="Manajemen Laporan & Keluhan Masyarakat"
                    icon={MessageSquare}
                    actions={[
                        {
                            label: 'TAMBAH MANUAL',
                            icon: Plus,
                            href: route('pengaduan.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <PengaduanStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <PengaduanFilters filters={filters} />

                {/* Data Table */}
                <Deferred data="pengaduans" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <TableCard 
                        title="Daftar Pengaduan" 
                        icon={FileText} 
                        total={pengaduans?.total || 0}
                        pagination={pengaduans}
                        noPadding
                    >
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
                                                <div className="w-48 h-48 mx-auto">
                                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                                </div>
                                                <p className="text-sm font-black text-gray-900 mt-2">Belum Ada Aduan</p>
                                                <p className="text-xs text-gray-500 mt-1">Belum ada pengaduan warga yang masuk.</p>
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
                                <div className="text-center py-8 bg-white rounded-2xl">
                                    <div className="w-40 h-40 mx-auto">
                                        <LottieComponent animationData={noDataAnimation} loop={true} />
                                    </div>
                                    <p className="text-sm font-black text-gray-900">Belum Ada Aduan</p>
                                </div>
                            )}
                        </div>
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
