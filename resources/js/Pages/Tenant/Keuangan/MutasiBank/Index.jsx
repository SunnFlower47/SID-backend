import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, FormField, ActionButtons, Modal, EmptyState, Pagination, StatCard } from '@/Components/Shared';
import { Landmark, Calendar, FileText, ArrowDownRight, ArrowUpRight, Search, FileDown, Trash2, Plus } from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function MutasiBankIndex({ auth, data, filters, summary }) {
    const handleDelete = (id, uraian) => {
        Swal.fire({
            title: 'Hapus Transaksi?',
            html: `Apakah Anda yakin ingin menghapus transaksi <b>${uraian}</b>? Ini akan mempengaruhi saldo bank desa.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px]', cancelButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px] text-gray-500' }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('keuangan.mutasi-bank.destroy', id), { preserveScroll: true });
            }
        });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('keuangan.mutasi-bank.index'), { 
            search: e.target.search.value,
            jenis_mutasi: filters.jenis_mutasi 
        }, { preserveState: true });
    };

    const handleFilterJenis = (jenis) => {
        router.get(route('keuangan.mutasi-bank.index'), { 
            search: filters.search,
            jenis_mutasi: jenis 
        }, { preserveState: true });
    };

    const formatRupiah = (number) => {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Mutasi Bank Desa">
            <Head title="Mutasi Bank Desa - Keuangan" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    title="Mutasi Bank Desa"
                    subtitle="Pencatatan setor dan tarik dana di rekening kas desa"
                    icon={Landmark}
                    actions={[
                        { label: 'TAMBAH TRANSAKSI', href: route('keuangan.mutasi-bank.create'), icon: Plus, variant: 'white' }
                    ]}
                />

                {/* Summary Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <StatCard 
                        icon={Landmark}
                        label="Total Saldo Bank"
                        value={formatRupiah(summary.saldo)}
                        color="blue"
                    />
                    <StatCard 
                        icon={ArrowDownRight}
                        label="Total Setoran (Masuk)"
                        value={formatRupiah(summary.total_penerimaan)}
                        color="green"
                    />
                    <StatCard 
                        icon={ArrowUpRight}
                        label="Total Tarikan (Keluar)"
                        value={formatRupiah(summary.total_pengeluaran)}
                        color="rose"
                    />
                </div>

                <TableCard
                    title="Riwayat Mutasi Bank"
                    icon={FileText}
                    total={data.total}
                    onSearch={handleSearch}
                    searchDefault={filters?.search}
                    actions={
                        <div className="flex bg-gray-100 p-1 rounded-xl">
                            <button onClick={() => handleFilterJenis('')} className={cn("px-4 py-2 rounded-lg text-xs font-black tracking-widest uppercase transition-all", !filters?.jenis_mutasi ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700')}>Semua</button>
                            <button onClick={() => handleFilterJenis('masuk')} className={cn("px-4 py-2 rounded-lg text-xs font-black tracking-widest uppercase transition-all", filters?.jenis_mutasi === 'masuk' ? 'bg-white text-green-600 shadow-sm' : 'text-gray-500 hover:text-gray-700')}>Masuk</button>
                            <button onClick={() => handleFilterJenis('keluar')} className={cn("px-4 py-2 rounded-lg text-xs font-black tracking-widest uppercase transition-all", filters?.jenis_mutasi === 'keluar' ? 'bg-white text-red-600 shadow-sm' : 'text-gray-500 hover:text-gray-700')}>Keluar</button>
                        </div>
                    }
                >
                    {data.data.length > 0 ? (
                        <>
                            {/* Desktop Table */}
                            <div className="hidden lg:block overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead className="bg-gray-100 text-gray-900 font-bold uppercase text-[10px] tracking-wider border-b border-gray-200 whitespace-nowrap">
                                        <tr>
                                            <th className="px-4 py-3 text-center border-r border-gray-200 w-16">NO</th>
                                            <th className="px-4 py-3 text-center border-r border-gray-200 w-24">AKSI</th>
                                            <th className="px-4 py-3 border-r border-gray-200">TANGGAL</th>
                                            <th className="px-4 py-3 border-r border-gray-200">URAIAN / NO. BUKTI</th>
                                            <th className="px-4 py-3 text-right border-r border-gray-200">SETORAN (MASUK)</th>
                                            <th className="px-4 py-3 text-right">TARIKAN (KELUAR)</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50 whitespace-nowrap">
                                        {data.data.map((item, index) => {
                                            const nomorUrut = data.from ? data.from + index : index + 1;
                                            return (
                                                <tr key={item.id} className="hover:bg-blue-50/30 transition-colors">
                                                    <td className="px-4 py-3 text-center font-mono text-xs text-gray-500">{nomorUrut}</td>
                                                    <td className="px-4 py-3 text-center border-r border-gray-50">
                                                        <ActionButtons
                                                            editHref={route('keuangan.mutasi-bank.edit', item.id)}
                                                            onDelete={() => handleDelete(item.id, item.uraian)}
                                                        />
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <div className="flex items-center gap-2">
                                                            <Calendar className="w-3.5 h-3.5 text-gray-400" />
                                                            <span className="font-bold text-gray-900">
                                                                {new Date(item.tanggal_mutasi).toLocaleDateString('id-ID')}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3">
                                                        <div className="font-bold text-gray-900 truncate max-w-xs" title={item.uraian}>{item.uraian}</div>
                                                        <div className="flex items-center gap-1 text-[10px] text-gray-500 mt-1 font-mono">
                                                            <FileText className="w-3 h-3" /> NO. BUKTI: {item.no_bukti || '-'}
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3 text-right">
                                                        {item.jenis_mutasi === 'masuk' ? (
                                                            <span className="font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">{formatRupiah(item.jumlah)}</span>
                                                        ) : (
                                                            <span className="text-gray-300">-</span>
                                                        )}
                                                    </td>
                                                    <td className="px-4 py-3 text-right">
                                                        {item.jenis_mutasi === 'keluar' ? (
                                                            <span className="font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg">{formatRupiah(item.jumlah)}</span>
                                                        ) : (
                                                            <span className="text-gray-300">-</span>
                                                        )}
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile List View */}
                            <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                {data.data.map((item, index) => (
                                    <div key={item.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                        <div className="flex items-start gap-4 mb-4">
                                            <div className={cn(
                                                "w-12 h-12 rounded-full flex items-center justify-center shrink-0",
                                                item.jenis_mutasi === 'masuk' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'
                                            )}>
                                                {item.jenis_mutasi === 'masuk' ? <ArrowDownRight className="w-6 h-6" /> : <ArrowUpRight className="w-6 h-6" />}
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <h4 className="font-black text-gray-900 leading-snug line-clamp-2" title={item.uraian}>{item.uraian}</h4>
                                                <div className="flex items-center gap-2 mt-2">
                                                    <span className={cn(
                                                        "text-[10px] font-black tracking-widest uppercase px-2 py-1 rounded-md",
                                                        item.jenis_mutasi === 'masuk' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                                    )}>
                                                        {item.jenis_mutasi === 'masuk' ? 'SETORAN' : 'PENARIKAN'}
                                                    </span>
                                                    <span className="text-xs font-medium text-gray-500">
                                                        {new Date(item.tanggal_mutasi).toLocaleDateString('id-ID')}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div className="grid grid-cols-2 gap-3 mb-4">
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NO BUKTI</p>
                                                <p className="text-xs font-mono font-bold text-gray-900 truncate">{item.no_bukti || '-'}</p>
                                            </div>
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">NOMINAL</p>
                                                <p className={cn(
                                                    "text-sm font-black truncate",
                                                    item.jenis_mutasi === 'masuk' ? 'text-green-600' : 'text-red-600'
                                                )}>
                                                    {formatRupiah(item.jumlah)}
                                                </p>
                                            </div>
                                        </div>

                                        <div className="flex gap-2">
                                            <Link href={route('keuangan.mutasi-bank.edit', item.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                EDIT
                                            </Link>
                                            <button onClick={() => handleDelete(item.id, item.uraian)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                            
                            {data.last_page > 1 && (
                                <div className="p-4 border-t border-gray-100">
                                    <Pagination links={data.links} />
                                </div>
                            )}
                        </>
                    ) : (
                        <EmptyState 
                            icon={Landmark} 
                            title="Belum Ada Riwayat Mutasi" 
                            message="Riwayat setor dan tarik tunai bank desa masih kosong." 
                            actionLabel="TAMBAH TRANSAKSI" 
                            onAction={() => router.get(route('keuangan.mutasi-bank.create'))} 
                        />
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
