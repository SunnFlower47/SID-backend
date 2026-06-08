import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, StatCard, TableCard, EmptyState, Badge } from '@/Components/Shared';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import {
    Archive, Plus, Edit2, Trash2, TrendingUp, TrendingDown,
    BookOpen, Package, ChevronDown, Search, Filter
} from 'lucide-react';
import Swal from 'sweetalert2';

const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n ?? 0);
const fmtQty = (n) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n ?? 0);

const KONDISI_MAP = {
    baik: { color: 'green', label: 'Baik' },
    rusak_ringan: { color: 'yellow', label: 'Rusak Ringan' },
    rusak_berat: { color: 'red', label: 'Rusak Berat' },
};

export default function Index({ auth, grouped, grandTotal, tahun, semester, tahunList }) {
    const totalItems = grouped.reduce((s, g) => s + g.items.length, 0);

    const [localTahun, setLocalTahun] = useState(tahun);
    const [localSemester, setLocalSemester] = useState(semester);

    const handleApplyFilter = () => {
        router.get(route('aset.inventaris.index'), { tahun: localTahun, semester: localSemester }, {
            preserveState: true,
            preserveScroll: true
        });
    };

    const handleDelete = (item) => {
        Swal.fire({
            title: 'Hapus Aset?',
            html: `<b>${item.nama_display}</b><br><small class="text-red-500">Semua riwayat mutasi akan ikut terhapus!</small>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS', cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl font-black text-xs uppercase tracking-widest', cancelButton: 'rounded-2xl font-black text-xs uppercase tracking-widest text-gray-500' },
        }).then((res) => {
            if (res.isConfirmed) router.delete(route('aset.inventaris.destroy', item.id), { preserveScroll: true });
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Inventaris Aset Desa">
            <Head title="Buku Inventaris Aset Desa" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">

                {/* PageHeader */}
                <PageHeader
                    icon={Archive}
                    title="Inventaris Aset Desa"
                    subtitle="Buku Inventaris Barang Milik Desa (BMD)"
                    actions={[
                        {
                            label: 'Master Kode Barang',
                            icon: BookOpen,
                            href: route('aset.barang.index'),
                            variant: 'ghost'
                        },
                        {
                            label: 'Tambah Aset Baru',
                            icon: Plus,
                            href: route('aset.inventaris.create', { tahun, semester }),
                            variant: 'white'
                        }
                    ]}
                />
                {/* StatCards */}
                <Deferred data={['grouped', 'grandTotal']} fallback={<SkeletonStats count={4} />}>
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <StatCard icon={BookOpen} label="Kategori" value={grouped.length} color="blue" badge="Total" />
                        <StatCard icon={Package} label="Total Item" value={totalItems} color="green" badge="Aktif" />
                        <StatCard icon={TrendingUp} label="Nilai Awal" value={fmt(grandTotal?.saldo_awal_nilai ?? 0)} color="orange" />
                        <StatCard icon={TrendingDown} label="Nilai Akhir" value={fmt(grandTotal?.saldo_akhir_nilai ?? 0)} color="emerald" />
                    </div>
                </Deferred>

                {/* Filter Tahun & Semester */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row gap-4 items-center justify-between">
                    <div className="flex flex-wrap items-center gap-4">
                        <div className="flex items-center gap-3">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun:</label>
                            <input
                                type="number"
                                value={localTahun}
                                onChange={(e) => setLocalTahun(e.target.value)}
                                onKeyDown={(e) => e.key === 'Enter' && handleApplyFilter()}
                                placeholder="Contoh: 2026"
                                className="w-24 px-4 py-2 bg-gray-50 border border-gray-200 rounded-xl text-sm font-bold focus:ring-green-500 focus:border-green-500"
                            />
                        </div>
                        <div className="flex items-center gap-2">
                            <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Semester:</label>
                            <div className="flex rounded-xl overflow-hidden border border-gray-200">
                                {[1, 2].map((s) => (
                                    <button key={s} onClick={() => setLocalSemester(s)}
                                        className={`px-5 py-2 text-xs font-black uppercase tracking-widest transition-all ${localSemester === s ? 'bg-green-600 text-white' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'}`}>
                                        SM {s}
                                    </button>
                                ))}
                            </div>
                        </div>
                        <Badge color={localSemester === 1 ? 'blue' : 'emerald'} dot={localSemester === 1 ? 'blue' : 'emerald'}>
                            {localSemester === 1 ? `Jan – Jun ${localTahun}` : `Jul – Des ${localTahun}`}
                        </Badge>
                    </div>

                    <button onClick={handleApplyFilter} className="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 active:scale-95">
                        <Search className="w-4 h-4" /> CARI DATA
                    </button>
                </div>

                {/* TableCard buku inventaris */}
                <Deferred data="grouped" fallback={<SkeletonTable columns={13} rows={6} />}>
                    <TableCard
                        icon={Archive}
                        title={`Buku Inventaris ${tahun} — Semester ${semester}`}
                        total={totalItems}
                        totalLabel="Item"
                    >
                        {grouped.length === 0 ? (
                            <EmptyState
                                title={`Belum Ada Aset Tahun ${tahun} SM ${semester}`}
                                message="Tambah aset baru untuk mulai mencatat inventaris desa."
                                action={{ label: 'Tambah Aset Baru', icon: Plus, href: route('aset.inventaris.create', { tahun, semester }) }}
                            />
                        ) : (
                            <div className="overflow-x-auto">
                                <table className="w-full text-xs min-w-[1280px]">
                                    <thead>
                                        <tr className="bg-gray-50/80 border-b border-gray-100">
                                            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest w-28">Kode</th>
                                            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest w-24">NUP</th>
                                            <th className="px-4 py-3 text-left font-black text-gray-500 uppercase tracking-widest">Nama Barang</th>
                                            <th className="px-3 py-3 text-center font-black text-gray-500 uppercase tracking-widest w-14">Sat.</th>
                                            <th colSpan={2} className="px-3 py-3 text-center font-black text-blue-500 uppercase tracking-widest border-l border-blue-100">Saldo Awal</th>
                                            <th colSpan={2} className="px-3 py-3 text-center font-black text-emerald-600 uppercase tracking-widest border-l border-emerald-100">Bertambah</th>
                                            <th colSpan={2} className="px-3 py-3 text-center font-black text-red-500 uppercase tracking-widest border-l border-red-100">Berkurang</th>
                                            <th colSpan={2} className="px-3 py-3 text-center font-black text-gray-700 uppercase tracking-widest border-l border-gray-200">Saldo Akhir</th>
                                            <th className="px-3 py-3 text-center font-black text-gray-500 uppercase tracking-widest border-l">Kondisi</th>
                                            <th className="px-3 py-3 text-center font-black text-gray-500 uppercase tracking-widest">Aksi</th>
                                        </tr>
                                        <tr className="border-b border-gray-100 bg-gray-50/40 text-[10px]">
                                            <th colSpan={4} />
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black border-l border-blue-100">Qty</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black">Nilai (Rp)</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black border-l border-emerald-100">Qty</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black">Nilai (Rp)</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black border-l border-red-100">Qty</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black">Nilai (Rp)</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black border-l border-gray-200">Qty</th>
                                            <th className="px-3 py-1.5 text-center text-gray-400 font-black">Nilai (Rp)</th>
                                            <th colSpan={2} />
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {grouped.map((grup) => (
                                            <React.Fragment key={grup.kategori.id}>
                                                {/* Kategori header row */}
                                                <tr className="bg-gray-100/70">
                                                    <td colSpan={14} className="px-4 py-2.5 font-black text-gray-700 text-xs uppercase tracking-widest">
                                                        {grup.kategori.kode}. {grup.kategori.nama}
                                                    </td>
                                                </tr>
                                                {/* Item rows */}
                                                {grup.items.map((item) => {
                                                    const kondisi = KONDISI_MAP[item.kondisi] ?? KONDISI_MAP.baik;
                                                    return (
                                                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors group">
                                                            <td className="px-4 py-2.5 font-mono font-bold text-green-700">{item.barang?.kode_barang}</td>
                                                            <td className="px-4 py-2.5 font-mono font-semibold text-gray-500">{item.nup || '-'}</td>
                                                            <td className="px-4 py-2.5 font-semibold text-gray-800">{item.nama_display}</td>
                                                            <td className="px-3 py-2.5 text-center text-gray-500">{item.satuan}</td>
                                                            <td className="px-3 py-2.5 text-right border-l border-blue-50 text-blue-700 font-semibold">{fmtQty(item.saldo_awal_kwantitas)}</td>
                                                            <td className="px-3 py-2.5 text-right text-blue-700 font-semibold">{fmt(item.saldo_awal_nilai)}</td>
                                                            <td className="px-3 py-2.5 text-right border-l border-emerald-50 text-emerald-700 font-semibold">{item.mutasi_tambah_kwantitas > 0 ? fmtQty(item.mutasi_tambah_kwantitas) : '-'}</td>
                                                            <td className="px-3 py-2.5 text-right text-emerald-700 font-semibold">{item.mutasi_tambah_nilai > 0 ? fmt(item.mutasi_tambah_nilai) : '-'}</td>
                                                            <td className="px-3 py-2.5 text-right border-l border-red-50 text-red-600 font-semibold">{item.mutasi_kurang_kwantitas > 0 ? fmtQty(item.mutasi_kurang_kwantitas) : '-'}</td>
                                                            <td className="px-3 py-2.5 text-right text-red-600 font-semibold">{item.mutasi_kurang_nilai > 0 ? fmt(item.mutasi_kurang_nilai) : '-'}</td>
                                                            <td className="px-3 py-2.5 text-right border-l border-gray-200 font-black text-gray-800">{fmtQty(item.saldo_akhir_kwantitas)}</td>
                                                            <td className="px-3 py-2.5 text-right font-black text-gray-800">{fmt(item.saldo_akhir_nilai)}</td>
                                                            <td className="px-3 py-2.5 text-center border-l">
                                                                <Badge color={kondisi.color} dot={kondisi.color}>{kondisi.label}</Badge>
                                                            </td>
                                                            <td className="px-3 py-2.5 text-center">
                                                                <div className="flex items-center justify-center gap-1">
                                                                    {/* Tambah Mutasi */}
                                                                    <Link
                                                                        href={route('aset.mutasi.create', { inventaris: item.id, tahun, semester })}
                                                                        className="w-7 h-7 flex items-center justify-center rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-all"
                                                                        title="Tambah Mutasi">
                                                                        <TrendingUp className="w-3 h-3" />
                                                                    </Link>
                                                                    {/* Edit data aset */}
                                                                    <Link href={route('aset.inventaris.edit', item.id)}
                                                                        className="w-7 h-7 flex items-center justify-center rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition-all"
                                                                        title="Edit Data Aset">
                                                                        <Edit2 className="w-3 h-3" />
                                                                    </Link>
                                                                    {/* Hapus aset */}
                                                                    <button onClick={() => handleDelete(item)}
                                                                        className="w-7 h-7 flex items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all"
                                                                        title="Hapus Aset">
                                                                        <Trash2 className="w-3 h-3" />
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    );
                                                })}
                                                {/* Subtotal kategori */}
                                                <tr className="bg-green-50/80 border-b border-green-100">
                                                    <td colSpan={4} className="px-4 py-2 font-black text-green-700 text-[10px] uppercase tracking-widest text-right">TOTAL {grup.kategori.nama}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700 border-l border-green-100">{fmtQty(grup.subtotal.saldo_awal_kwantitas)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700">{fmt(grup.subtotal.saldo_awal_nilai)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700 border-l border-green-100">{fmtQty(grup.subtotal.mutasi_tambah_kwantitas)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700">{fmt(grup.subtotal.mutasi_tambah_nilai)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700 border-l border-green-100">{fmtQty(grup.subtotal.mutasi_kurang_kwantitas)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700">{fmt(grup.subtotal.mutasi_kurang_nilai)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700 border-l border-gray-200">{fmtQty(grup.subtotal.saldo_akhir_kwantitas)}</td>
                                                    <td className="px-3 py-2 text-right font-black text-green-700">{fmt(grup.subtotal.saldo_akhir_nilai)}</td>
                                                    <td colSpan={2} />
                                                </tr>
                                            </React.Fragment>
                                        ))}
                                        {/* Grand Total */}
                                        <tr className="bg-gradient-to-r from-green-700 to-green-800">
                                            <td colSpan={4} className="px-4 py-3 font-black text-white text-[10px] uppercase tracking-widest text-right">GRAND TOTAL</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300 border-l border-white/10">{fmtQty(grandTotal?.saldo_awal_kwantitas)}</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300">{fmt(grandTotal?.saldo_awal_nilai)}</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300 border-l border-white/10">{fmtQty(grandTotal?.mutasi_tambah_kwantitas)}</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300">{fmt(grandTotal?.mutasi_tambah_nilai)}</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300 border-l border-white/10">{fmtQty(grandTotal?.mutasi_kurang_kwantitas)}</td>
                                            <td className="px-3 py-3 text-right font-black text-yellow-300">{fmt(grandTotal?.mutasi_kurang_nilai)}</td>
                                            <td className="px-3 py-3 text-right font-black text-white border-l border-white/10">{fmtQty(grandTotal?.saldo_akhir_kwantitas)}</td>
                                            <td className="px-3 py-3 text-right font-black text-white">{fmt(grandTotal?.saldo_akhir_nilai)}</td>
                                            <td colSpan={2} />
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        )}
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
