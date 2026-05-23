import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, Plus, Eye, Edit, Trash2, ArrowLeft, CheckCircle, Clock, XCircle } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, TableCard, EmptyState, Badge } from '@/Components/Shared';

const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        color: 'green',  icon: CheckCircle },
    ditangguhkan: { label: 'Ditangguhkan', color: 'yellow', icon: Clock },
    berhenti:     { label: 'Berhenti',     color: 'red',    icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_CONFIG[status] ?? { label: status, color: 'gray', icon: Clock };
    return (
        <Badge color={cfg.color} icon={cfg.icon}>
            {cfg.label}
        </Badge>
    );
}

export default function Index({ auth, bantuanSosial, penerima }) {
    const isLocked = bantuanSosial.status === 'selesai' || bantuanSosial.is_expired;

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus penerima <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('bantuan-sosial.penerima.destroy', [bantuanSosial.id, id]), {
                    preserveScroll: true,
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Daftar Penerima Bantuan">
            <Head title={`Penerima: ${bantuanSosial.nama_program}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    title="Daftar Penerima"
                    subtitle={bantuanSosial.nama_program}
                    icon={Users}
                    backHref={route('bantuan-sosial.show', bantuanSosial.id)}
                    actions={
                        !isLocked ? [
                            {
                                label: 'TAMBAH PENERIMA',
                                icon: Plus,
                                href: route('bantuan-sosial.penerima.create', bantuanSosial.id),
                                variant: 'white'
                            }
                        ] : []
                    }
                />

                {/* ── Locked Alert ── */}
                {isLocked && (
                    <div className="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-4 animate-in slide-in-from-top duration-300">
                        <div className="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                            <Clock className="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-amber-800 uppercase tracking-widest">Program Telah Selesai</p>
                            <p className="text-xs font-bold text-amber-700/80">Data penerima telah dikunci dan tidak dapat diubah lagi untuk menjaga integritas laporan.</p>
                        </div>
                    </div>
                )}

                {/* ── Table ── */}
                <TableCard 
                    title="Daftar Penerima"
                    icon={Users}
                    total={penerima?.total ?? 0}
                    pagination={penerima}
                    noPadding
                >
                    {penerima?.data?.length > 0 ? (
                        <>
                            {/* Desktop Table */}
                            <div className="hidden lg:block overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th className="px-6 py-4">Penerima</th>
                                            <th className="px-6 py-4">NIK</th>
                                            <th className="px-6 py-4">Sistem</th>
                                            <th className="px-6 py-4">Nilai Diterima</th>
                                            <th className="px-6 py-4">Tanggal</th>
                                            <th className="px-6 py-4">Status</th>
                                            <th className="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {penerima.data.map((p) => {
                                            const dataTambahan = typeof p.data_tambahan === 'string'
                                                ? JSON.parse(p.data_tambahan || '{}')
                                                : (p.data_tambahan ?? {});
                                            const isBerkala = dataTambahan?.sistem_pembayaran === 'berkala' || dataTambahan?.sistem_pembayaran === 'triwulanan';

                                            return (
                                                <tr key={p.id} className="hover:bg-green-50/20 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <p className="font-bold text-gray-900">{p.penduduk?.nama ?? '—'}</p>
                                                        <p className="text-xs text-gray-400">{p.penduduk?.alamat}</p>
                                                    </td>
                                                    <td className="px-6 py-4 font-mono text-xs">{p.penduduk?.nik}</td>
                                                    <td className="px-6 py-4">
                                                        <Badge color={isBerkala ? 'blue' : 'gray'}>
                                                            {isBerkala ? 'Berkala' : 'Sekali'}
                                                        </Badge>
                                                    </td>
                                                    <td className="px-6 py-4 font-bold text-green-700">
                                                        Rp {Number(p.nilai_diterima).toLocaleString('id-ID')}
                                                    </td>
                                                    <td className="px-6 py-4 text-xs">
                                                        {p.tanggal_penerimaan
                                                            ? new Date(p.tanggal_penerimaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                                                            : '—'}
                                                    </td>
                                                    <td className="px-6 py-4"><StatusBadge status={p.status_penerimaan} /></td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex justify-end gap-2">
                                                            <Link
                                                                href={route('bantuan-sosial.penerima.show', [bantuanSosial.id, p.id])}
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                            >
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            {!isLocked && (
                                                                <>
                                                                    <Link
                                                                        href={route('bantuan-sosial.penerima.edit', [bantuanSosial.id, p.id])}
                                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                                                                    >
                                                                        <Edit className="w-4 h-4" />
                                                                    </Link>
                                                                    <button
                                                                        onClick={() => handleDelete(p.id, p.penduduk?.nama)}
                                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                                    >
                                                                        <Trash2 className="w-4 h-4" />
                                                                    </button>
                                                                </>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile Cards */}
                            <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                {penerima.data.map((p) => {
                                    const dataTambahan = typeof p.data_tambahan === 'string'
                                        ? JSON.parse(p.data_tambahan || '{}')
                                        : (p.data_tambahan ?? {});
                                    const isBerkala = dataTambahan?.sistem_pembayaran === 'berkala' || dataTambahan?.sistem_pembayaran === 'triwulanan';

                                    return (
                                        <div key={p.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                            <div className="flex items-start gap-3 mb-3">
                                                <div className="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                                                    <Users className="w-5 h-5 text-purple-600" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h4 className="font-black text-gray-900 truncate">{p.penduduk?.nama}</h4>
                                                    <p className="text-[10px] font-mono text-gray-400 mt-0.5">{p.penduduk?.nik}</p>
                                                </div>
                                                <StatusBadge status={p.status_penerimaan} />
                                            </div>
                                            <div className="grid grid-cols-2 gap-2 mb-3">
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nilai</p>
                                                    <p className="text-xs font-bold text-green-700">Rp {Number(p.nilai_diterima).toLocaleString('id-ID')}</p>
                                                </div>
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Sistem</p>
                                                    <p className="text-xs font-bold text-gray-900">{isBerkala ? 'Berkala' : 'Sekali'}</p>
                                                </div>
                                            </div>
                                            <div className="flex gap-2">
                                                <Link href={route('bantuan-sosial.penerima.show', [bantuanSosial.id, p.id])} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    DETAIL
                                                </Link>
                                                {!isLocked && (
                                                    <>
                                                        <Link href={route('bantuan-sosial.penerima.edit', [bantuanSosial.id, p.id])} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                            EDIT
                                                        </Link>
                                                        <button onClick={() => handleDelete(p.id, p.penduduk?.nama)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </>
                    ) : (
                        <EmptyState 
                            title="Belum Ada Penerima"
                            message="Tambahkan data warga yang menerima bantuan dari program ini."
                            action={
                                !isLocked ? {
                                    label: "TAMBAH PENERIMA SEKARANG",
                                    href: route('bantuan-sosial.penerima.create', bantuanSosial.id)
                                } : null
                            }
                        />
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
