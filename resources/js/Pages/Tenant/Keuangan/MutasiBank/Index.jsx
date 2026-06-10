import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, FormField, ActionButtons, Modal, EmptyState, Pagination } from '@/Components/Shared';
import { Landmark, Calendar, FileText, ArrowDownRight, ArrowUpRight, Search, FileDown } from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';

export default function MutasiBankIndex({ auth, data, filters, summary }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editItem, setEditItem] = useState(null);

    const { data: formData, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        tanggal_mutasi: new Date().toISOString().split('T')[0],
        jenis_mutasi: 'masuk',
        uraian: '',
        jumlah: '',
        no_bukti: '',
    });

    const openAddModal = () => {
        reset();
        clearErrors();
        setEditItem(null);
        setIsModalOpen(true);
    };

    const openEditModal = (item) => {
        clearErrors();
        setData({
            tanggal_mutasi: item.tanggal_mutasi ? new Date(item.tanggal_mutasi).toISOString().split('T')[0] : '',
            jenis_mutasi: item.jenis_mutasi,
            uraian: item.uraian || '',
            jumlah: item.jumlah || '',
            no_bukti: item.no_bukti || '',
        });
        setEditItem(item);
        setIsModalOpen(true);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editItem) {
            put(route('keuangan.mutasi-bank.update', editItem.id), {
                onSuccess: () => { setIsModalOpen(false); reset(); }
            });
        } else {
            post(route('keuangan.mutasi-bank.store'), {
                onSuccess: () => { setIsModalOpen(false); reset(); }
            });
        }
    };

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
                        { label: 'TAMBAH TRANSAKSI', onClick: openAddModal, variant: 'primary' }
                    ]}
                />

                {/* Summary Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div className="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform" />
                        <div className="relative">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <Landmark className="w-4 h-4" />
                                </div>
                                <h3 className="text-[11px] font-black text-gray-500 tracking-widest uppercase">Total Saldo Bank</h3>
                            </div>
                            <div className="text-2xl font-black text-gray-900 tracking-tight">{formatRupiah(summary.saldo)}</div>
                        </div>
                    </div>
                    <div className="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform" />
                        <div className="relative">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-8 h-8 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                    <ArrowDownRight className="w-4 h-4" />
                                </div>
                                <h3 className="text-[11px] font-black text-gray-500 tracking-widest uppercase">Total Setoran (Masuk)</h3>
                            </div>
                            <div className="text-2xl font-black text-green-600 tracking-tight">{formatRupiah(summary.total_penerimaan)}</div>
                        </div>
                    </div>
                    <div className="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                        <div className="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full group-hover:scale-110 transition-transform" />
                        <div className="relative">
                            <div className="flex items-center gap-3 mb-2">
                                <div className="w-8 h-8 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">
                                    <ArrowUpRight className="w-4 h-4" />
                                </div>
                                <h3 className="text-[11px] font-black text-gray-500 tracking-widest uppercase">Total Tarikan (Keluar)</h3>
                            </div>
                            <div className="text-2xl font-black text-red-600 tracking-tight">{formatRupiah(summary.total_pengeluaran)}</div>
                        </div>
                    </div>
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
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-gray-50/80 border-b border-gray-100">
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Uraian / No. Bukti</th>
                                            <th className="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Setoran (Masuk)</th>
                                            <th className="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Tarikan (Keluar)</th>
                                            <th className="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {data.data.map((item) => (
                                            <tr key={item.id} className="hover:bg-gray-50/50 transition-colors">
                                                <td className="px-4 py-4 align-top">
                                                    <div className="flex items-center gap-2">
                                                        <Calendar className="w-3.5 h-3.5 text-gray-300" />
                                                        <span className="text-xs font-bold text-gray-600">
                                                            {new Date(item.tanggal_mutasi).toLocaleDateString('id-ID')}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 align-top">
                                                    <div className="text-xs font-bold text-gray-900">{item.uraian}</div>
                                                    <div className="flex items-center gap-1 text-[10px] text-gray-500 mt-1">
                                                        <FileText className="w-3 h-3" /> No. Bukti: {item.no_bukti || '-'}
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 align-top text-right">
                                                    {item.jenis_mutasi === 'masuk' ? (
                                                        <span className="text-sm font-bold text-green-600">{formatRupiah(item.jumlah)}</span>
                                                    ) : (
                                                        <span className="text-sm text-gray-400">-</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 align-top text-right">
                                                    {item.jenis_mutasi === 'keluar' ? (
                                                        <span className="text-sm font-bold text-red-600">{formatRupiah(item.jumlah)}</span>
                                                    ) : (
                                                        <span className="text-sm text-gray-400">-</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 align-top text-right">
                                                    <ActionButtons
                                                        onEdit={() => openEditModal(item)}
                                                        onDelete={() => handleDelete(item.id, item.uraian)}
                                                    />
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
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
                            onAction={openAddModal} 
                        />
                    )}
                </TableCard>
            </div>

            <Modal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} title={editItem ? 'Edit Transaksi Bank' : 'Tambah Transaksi Bank'}>
                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <FormField.Input label="Tanggal Transaksi" type="date" required error={errors.tanggal_mutasi} value={formData.tanggal_mutasi} onChange={e => setData('tanggal_mutasi', e.target.value)} />
                        <div>
                            <label className="block text-[10px] font-black text-gray-500 tracking-widest uppercase mb-2">Jenis Transaksi</label>
                            <div className="flex gap-2">
                                <label className={cn("flex-1 cursor-pointer border rounded-xl p-3 flex items-center gap-2 transition-all", formData.jenis_mutasi === 'masuk' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500 hover:border-gray-300')}>
                                    <input type="radio" className="sr-only" checked={formData.jenis_mutasi === 'masuk'} onChange={() => setData('jenis_mutasi', 'masuk')} />
                                    <ArrowDownRight className={cn("w-4 h-4", formData.jenis_mutasi === 'masuk' ? 'text-green-600' : 'text-gray-400')} />
                                    <span className="text-xs font-bold">Setor (Masuk)</span>
                                </label>
                                <label className={cn("flex-1 cursor-pointer border rounded-xl p-3 flex items-center gap-2 transition-all", formData.jenis_mutasi === 'keluar' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-500 hover:border-gray-300')}>
                                    <input type="radio" className="sr-only" checked={formData.jenis_mutasi === 'keluar'} onChange={() => setData('jenis_mutasi', 'keluar')} />
                                    <ArrowUpRight className={cn("w-4 h-4", formData.jenis_mutasi === 'keluar' ? 'text-red-600' : 'text-gray-400')} />
                                    <span className="text-xs font-bold">Tarik (Keluar)</span>
                                </label>
                            </div>
                            {errors.jenis_mutasi && <p className="text-red-500 text-xs mt-1">{errors.jenis_mutasi}</p>}
                        </div>
                    </div>
                    
                    <FormField.Input label="Uraian Transaksi" required error={errors.uraian} value={formData.uraian} onChange={e => setData('uraian', e.target.value)} placeholder="Contoh: Setoran Dana Desa Tahap I" />
                    
                    <div className="grid grid-cols-2 gap-4">
                        <FormField.Input label="Jumlah Nominal (Rp)" type="number" min="0" required error={errors.jumlah} value={formData.jumlah} onChange={e => setData('jumlah', e.target.value)} placeholder="0" />
                        <FormField.Input label="No. Bukti / Referensi" error={errors.no_bukti} value={formData.no_bukti} onChange={e => setData('no_bukti', e.target.value)} placeholder="Misal: TR-001" />
                    </div>
                    
                    <div className="flex gap-3 pt-4 border-t border-gray-100">
                        <button type="button" onClick={() => setIsModalOpen(false)} className="flex-1 py-3 rounded-xl bg-gray-50 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-100 border border-gray-200 transition-all">BATAL</button>
                        <button type="submit" disabled={processing} className="flex-1 py-3 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 disabled:opacity-50">
                            {processing ? 'MENYIMPAN...' : 'SIMPAN TRANSAKSI'}
                        </button>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
