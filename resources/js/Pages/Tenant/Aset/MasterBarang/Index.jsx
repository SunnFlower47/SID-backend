import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, Modal, FormField } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Search, Plus, Edit2, Trash2, Save, Package } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, barangs, kategoris, filters }) {
    const [search, setSearch]       = useState(filters.search || '');
    const [kategoriId, setKategoriId] = useState(filters.kategori_id || '');
    const [modal, setModal]         = useState(null); // null | 'create' | 'edit'
    const [editTarget, setEditTarget] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        aset_kategori_id: '',
        kode_barang:      '',
        nama_barang:      '',
        satuan_default:   '',
    });

    const openCreate = () => { reset(); setEditTarget(null); setModal('create'); };
    const openEdit   = (b) => {
        setEditTarget(b);
        setData({ aset_kategori_id: b.kategori?.id ?? '', kode_barang: b.kode_barang, nama_barang: b.nama_barang, satuan_default: b.satuan_default ?? '' });
        setModal('edit');
    };
    const closeModal = () => { setModal(null); setEditTarget(null); reset(); };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (modal === 'create') {
            post(route('aset.barang.store'), { onSuccess: closeModal });
        } else {
            router.put(route('aset.barang.update', editTarget.id), data, { onSuccess: closeModal, preserveScroll: true });
        }
    };

    const handleDelete = (b) => {
        Swal.fire({
            title: 'Hapus Kode Barang?',
            html: `<b>${b.kode_barang}</b> — ${b.nama_barang}<br><small class="text-red-500">Tidak bisa dihapus jika sudah dipakai di inventaris.</small>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS', cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl font-black text-xs uppercase tracking-widest', cancelButton: 'rounded-2xl font-black text-xs uppercase tracking-widest text-gray-500' },
        }).then((res) => {
            if (res.isConfirmed) router.delete(route('aset.barang.destroy', b.id), { preserveScroll: true });
        });
    };

    const handleFilter = () =>
        router.get(route('aset.barang.index'), { search, kategori_id: kategoriId }, { preserveScroll: true });

    return (
        <AuthenticatedLayout user={auth.user} title="Master Kode Barang">
            <Head title="Master Kode Barang Aset" />

            <div className="space-y-6 animate-in fade-in duration-500 pb-20">

                {/* PageHeader */}
                <PageHeader
                    icon={Package}
                    title="Master Kode Barang"
                    subtitle="Referensi Kode Barang Milik Desa (BMD)"
                    actions={[
                        { label: 'Tambah Kode', icon: Plus, onClick: openCreate, variant: 'white' },
                    ]}
                />

                {/* Filter */}
                <div className="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input
                            type="text" placeholder="Cari kode atau nama barang..."
                            value={search} onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleFilter()}
                            className="w-full pl-9 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-semibold focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                        />
                    </div>
                    <select value={kategoriId} onChange={(e) => setKategoriId(e.target.value)}
                        className="px-4 py-2.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-semibold focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all">
                        <option value="">Semua Golongan</option>
                        {kategoris.map((k) => <option key={k.id} value={k.id}>{k.kode} — {k.nama}</option>)}
                    </select>
                    <button onClick={handleFilter}
                        className="px-6 py-2.5 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all">
                        Cari
                    </button>
                </div>

                {/* TableCard */}
                <TableCard
                    icon={Package}
                    title="Daftar Kode Barang"
                    total={barangs.meta?.total ?? barangs.data.length}
                    totalLabel="Kode"
                    pagination={barangs}
                >
                    {barangs.data.length === 0 ? (
                        <EmptyState
                            title="Belum Ada Kode Barang"
                            message="Klik Tambah Kode untuk menambah referensi kode barang BMD."
                            action={{ label: 'Tambah Kode', icon: Plus, onClick: openCreate }}
                        />
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="bg-gray-50/80 border-b border-gray-100">
                                        <th className="text-left px-5 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Kode Barang</th>
                                        <th className="text-left px-5 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Nama Barang</th>
                                        <th className="text-left px-5 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Satuan</th>
                                        <th className="text-left px-5 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Golongan</th>
                                        <th className="text-center px-5 py-3 text-[10px] font-black text-gray-500 uppercase tracking-widest">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {barangs.data.map((b) => (
                                        <tr key={b.id} className="hover:bg-gray-50/70 transition-colors">
                                            <td className="px-5 py-3 font-mono font-bold text-green-700">{b.kode_barang}</td>
                                            <td className="px-5 py-3 font-semibold text-gray-800">{b.nama_barang}</td>
                                            <td className="px-5 py-3 text-gray-500 font-semibold">{b.satuan_default ?? '-'}</td>
                                            <td className="px-5 py-3">
                                                <Badge color="emerald">{b.kategori?.kode} — {b.kategori?.nama}</Badge>
                                            </td>
                                            <td className="px-5 py-3">
                                                <div className="flex items-center justify-center gap-2">
                                                    <button onClick={() => openEdit(b)} title="Edit"
                                                        className="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 transition-all">
                                                        <Edit2 className="w-3.5 h-3.5" />
                                                    </button>
                                                    <button onClick={() => handleDelete(b)} title="Hapus"
                                                        className="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 hover:bg-red-100 text-red-500 transition-all">
                                                        <Trash2 className="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </TableCard>
            </div>

            {/* Modal Tambah / Edit — pakai shared Modal */}
            <Modal show={!!modal} onClose={closeModal} maxWidth="md">
                <div className="bg-gradient-to-r from-green-600 to-green-700 px-6 py-5 flex items-center justify-between">
                    <h2 className="text-white font-black uppercase italic tracking-tight text-base">
                        {modal === 'create' ? 'Tambah Kode Barang' : 'Edit Kode Barang'}
                    </h2>
                </div>
                <form onSubmit={handleSubmit} className="p-6 space-y-4">
                    <FormField.Select
                        label="Golongan Aset" required
                        value={data.aset_kategori_id}
                        onChange={(e) => setData('aset_kategori_id', e.target.value)}
                        error={errors.aset_kategori_id}
                        placeholder="Pilih golongan..."
                        options={kategoris.map((k) => ({ value: k.id, label: `${k.kode} — ${k.nama}` }))}
                    />
                    <FormField.Input
                        label="Kode Barang" required
                        placeholder="Contoh: 2.01.01.01"
                        value={data.kode_barang}
                        onChange={(e) => setData('kode_barang', e.target.value)}
                        error={errors.kode_barang}
                        inputClassName="font-mono"
                    />
                    <FormField.Input
                        label="Nama Barang" required
                        placeholder="Contoh: Tanah Kas Desa"
                        value={data.nama_barang}
                        onChange={(e) => setData('nama_barang', e.target.value)}
                        error={errors.nama_barang}
                    />
                    <FormField.Input
                        label="Satuan Default"
                        placeholder="unit, m², Buah, Lusin..."
                        value={data.satuan_default}
                        onChange={(e) => setData('satuan_default', e.target.value)}
                        error={errors.satuan_default}
                    />
                    <div className="flex gap-3 pt-2">
                        <button type="button" onClick={closeModal}
                            className="flex-1 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                            Batal
                        </button>
                        <button type="submit" disabled={processing}
                            className="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 disabled:opacity-60 transition-all">
                            <Save className="w-3.5 h-3.5" />
                            {processing ? 'Menyimpan...' : 'Simpan'}
                        </button>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
