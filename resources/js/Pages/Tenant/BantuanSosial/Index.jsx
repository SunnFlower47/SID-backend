import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BansosStats from '@/Components/BantuanSosial/BansosStats';
import BansosFilters from '@/Components/BantuanSosial/BansosFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { HandHeart, Plus, Eye, Edit, Trash2, Filter, Users } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, TableCard, EmptyState, Badge } from '@/Components/Shared';

// ────────────────────────────────────────────────────────────────
// Badge helpers
// ────────────────────────────────────────────────────────────────
const STATUS_BADGE = {
    aktif:        'green',
    selesai:      'gray',
    ditangguhkan: 'yellow',
};

const JENIS_BADGE = {
    BLT:           'blue',
    PKH:           'purple',
    BPNT:          'teal',
    'Bansos Lainnya': 'orange',
};

function StatusBadge({ status, label, isExpired }) {
    const color = isExpired ? 'gray' : (STATUS_BADGE[status] ?? 'gray');
    
    return (
        <Badge color={color}>
            {isExpired && <span className="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5 animate-pulse" />}
            {label || status}
        </Badge>
    );
}

function JenisBadge({ jenis }) {
    const color = JENIS_BADGE[jenis] ?? 'gray';
    return (
        <Badge color={color}>
            {jenis}
        </Badge>
    );
}

// ────────────────────────────────────────────────────────────────
// Main Page
// ────────────────────────────────────────────────────────────────
export default function Index({ auth, bantuanSosials, stats, filters }) {

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus program <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
            },
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('bantuan-sosial.destroy', id), { preserveScroll: true });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Bantuan Sosial">
            <Head title="Bantuan Sosial" />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    title="Bantuan Sosial"
                    subtitle="Kelola program bantuan sosial desa"
                    icon={HandHeart}
                    actions={[
                        {
                            label: 'TAMBAH PROGRAM',
                            icon: Plus,
                            href: route('bantuan-sosial.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* ── Stats ── */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <BansosStats stats={stats} />
                </Deferred>

                {/* ── Filters ── */}
                <BansosFilters filters={filters} />

                {/* ── Table ── */}
                <Deferred data="bantuanSosials" fallback={<SkeletonTable columns={7} rows={10} />}>
                    <TableCard 
                        title="Daftar Program Bansos"
                        icon={HandHeart}
                        total={bantuanSosials?.total ?? 0}
                        pagination={bantuanSosials}
                        noPadding
                    >
                        {bantuanSosials?.data?.length > 0 ? (
                            <>
                                {/* ── Desktop Table ── */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                            <tr>
                                                <th className="px-6 py-4">Nama Program</th>
                                                <th className="px-6 py-4">Jenis</th>
                                                <th className="px-6 py-4">Periode</th>
                                                <th className="px-6 py-4">Nilai Bantuan</th>
                                                <th className="px-6 py-4">Penerima</th>
                                                <th className="px-6 py-4">Status</th>
                                                <th className="px-6 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {bantuanSosials.data.map((b) => (
                                                <tr key={b.id} className="hover:bg-green-50/20 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                                                <HandHeart className="w-4 h-4 text-green-600" />
                                                            </div>
                                                            <div>
                                                                <p className="font-bold text-gray-900 leading-tight">{b.nama_program}</p>
                                                                <p className="text-xs text-gray-400 truncate max-w-[200px]">{b.deskripsi}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-4"><JenisBadge jenis={b.jenis_bantuan} /></td>
                                                    <td className="px-6 py-4 font-medium">{b.periode}</td>
                                                    <td className="px-6 py-4">
                                                        {b.nilai_bantuan ? (
                                                            <span className="font-bold text-green-700">
                                                                Rp {Number(b.nilai_bantuan).toLocaleString('id-ID')}
                                                            </span>
                                                        ) : (
                                                            <span className="text-gray-400">—</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <Badge color="purple" icon={Users}>
                                                            {b.penerima_count} orang
                                                        </Badge>
                                                    </td>
                                                    <td className="px-6 py-4">
                                                        <StatusBadge 
                                                            status={b.status} 
                                                            label={b.status_label} 
                                                            isExpired={b.is_expired} 
                                                        />
                                                    </td>
                                                    <td className="px-6 py-4 text-right">
                                                        <div className="flex justify-end gap-2">
                                                            <Link
                                                                href={route('bantuan-sosial.show', b.id)}
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                                title="Lihat detail"
                                                            >
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            <Link
                                                                href={route('bantuan-sosial.edit', b.id)}
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                                                                title="Edit"
                                                            >
                                                                <Edit className="w-4 h-4" />
                                                            </Link>
                                                            <button
                                                                onClick={() => handleDelete(b.id, b.nama_program)}
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                                title="Hapus"
                                                            >
                                                                <Trash2 className="w-4 h-4" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                {/* ── Mobile Cards ── */}
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                    {bantuanSosials.data.map((b) => (
                                        <div key={b.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                            <div className="flex items-start gap-3 mb-3">
                                                <div className="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                                    <HandHeart className="w-5 h-5 text-green-600" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h4 className="font-black text-gray-900 truncate">{b.nama_program}</h4>
                                                    <div className="flex flex-wrap items-center gap-2 mt-1">
                                                        <JenisBadge jenis={b.jenis_bantuan} />
                                                        <StatusBadge 
                                                            status={b.status} 
                                                            label={b.status_label} 
                                                            isExpired={b.is_expired} 
                                                        />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="grid grid-cols-2 gap-2 mb-3">
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Periode</p>
                                                    <p className="text-xs font-bold text-gray-900">{b.periode}</p>
                                                </div>
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Penerima</p>
                                                    <p className="text-xs font-bold text-purple-700">{b.penerima_count} orang</p>
                                                </div>
                                                <div className="col-span-2 bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nilai Bantuan</p>
                                                    <p className="text-sm font-bold text-green-700">
                                                        {b.nilai_bantuan ? `Rp ${Number(b.nilai_bantuan).toLocaleString('id-ID')}` : '—'}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex gap-2">
                                                <Link href={route('bantuan-sosial.show', b.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    DETAIL
                                                </Link>
                                                <Link href={route('bantuan-sosial.edit', b.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    EDIT
                                                </Link>
                                                <button onClick={() => handleDelete(b.id, b.nama_program)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <EmptyState 
                                title="Belum Ada Program Bantuan Sosial"
                                message="Mulai tambahkan program bantuan sosial untuk mengelola penerimaan bantuan warga."
                                action={{
                                    label: "TAMBAH PROGRAM",
                                    href: route('bantuan-sosial.create')
                                }}
                            />
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
