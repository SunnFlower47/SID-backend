import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons } from '@/Components/Shared';
import { MapPin, Plus, Search, Map, Pencil, Trash2, Eye } from 'lucide-react';
import Swal from 'sweetalert2';

export default function Index({ auth, tanahDiDesa, filters }) {
    const [search, setSearch] = useState(filters?.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('sekretariat.tanah-di-desa.index'), { search }, { preserveState: true });
    };

    const handleDelete = (item) => {
        Swal.fire({
            title: 'Hapus Data Tanah?',
            text: `Anda yakin ingin menghapus data tanah milik ${item.nama_pemilik}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl font-bold',
                cancelButton: 'rounded-xl font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('sekretariat.tanah-di-desa.destroy', item.id));
            }
        });
    };

    const formatNum = (num) => new Intl.NumberFormat('id-ID').format(num);

    return (
        <AuthenticatedLayout user={auth.user} title="Buku Tanah di Desa">
            <Head title="Buku Tanah di Desa" />
            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={MapPin}
                    title="Buku Tanah di Desa"
                    subtitle="Pengelolaan data Buku Tanah di Desa (Lampiran VII Permendagri 47/2016)"
                    actions={[
                        { label: 'Tambah Data', icon: Plus, href: route('sekretariat.tanah-di-desa.create'), variant: 'white' },
                    ]}
                />

                <form onSubmit={handleSearch} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex gap-3">
                    <div className="relative flex-1">
                        <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari NOP, nama pemilik atau lokasi tanah..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            className="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <button type="submit" className="px-6 py-3 bg-blue-600 text-white rounded-2xl text-sm font-bold tracking-wide hover:bg-blue-700 transition-colors shadow-lg shadow-blue-200">
                        Cari
                    </button>
                    {search && (
                        <button type="button" onClick={() => { setSearch(''); router.get(route('sekretariat.tanah-di-desa.index')); }} className="px-6 py-3 bg-gray-100 text-gray-600 rounded-2xl text-sm font-bold tracking-wide hover:bg-gray-200 transition-colors">
                            Reset
                        </button>
                    )}
                </form>

                <TableCard
                    icon={MapPin}
                    title="Daftar Tanah di Desa"
                    total={tanahDiDesa.total}
                    totalLabel="Data"
                >
                    {tanahDiDesa.data.length === 0 ? (
                        <EmptyState
                            icon={Map}
                            title="Data Kosong"
                            message="Belum ada data tanah di desa yang tercatat."
                            action={{ label: 'Tambah Data', href: route('sekretariat.tanah-di-desa.create') }}
                        />
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-xs min-w-[1000px]">
                                <thead>
                                    <tr className="bg-gray-50/80 border-b border-gray-100">
                                        <th className="px-4 py-4 text-center font-black text-gray-500 uppercase tracking-widest w-12 rounded-tl-xl">No</th>
                                        <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest">Pemilik & NOP</th>
                                        <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest">Lokasi</th>
                                        <th className="px-4 py-4 text-center font-black text-gray-500 uppercase tracking-widest">Status</th>
                                        <th className="px-4 py-4 text-center font-black text-gray-500 uppercase tracking-widest">Total Luas</th>
                                        <th className="px-4 py-4 text-right font-black text-gray-500 uppercase tracking-widest rounded-tr-xl">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {tanahDiDesa.data.map((item, index) => (
                                        <tr key={item.id} className="border-b border-gray-50 hover:bg-gray-50/50 transition-colors group">
                                            <td className="px-4 py-4 text-center text-gray-400 font-medium">
                                                {(tanahDiDesa.current_page - 1) * tanahDiDesa.per_page + index + 1}
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="font-bold text-gray-900">{item.nama_pemilik}</div>
                                                <div className="text-gray-500 text-[11px] mt-0.5 font-medium">NOP: {item.nop || '-'}</div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-gray-700 line-clamp-2 max-w-xs">{item.lokasi_tanah || '-'}</div>
                                            </td>
                                            <td className="px-4 py-4 text-center">
                                                <Badge variant="blue">{item.status_kepemilikan}</Badge>
                                            </td>
                                            <td className="px-4 py-4 text-center font-medium text-gray-700">
                                                {formatNum(item.total_luas)} m²
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="flex items-center justify-end gap-2 transition-opacity">
                                                    <Link 
                                                        href={route('sekretariat.tanah-di-desa.show', item.id)}
                                                        className="p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-colors tooltip"
                                                        title="Lihat Detail & Mutasi"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    <ActionButtons 
                                                        editHref={route('sekretariat.tanah-di-desa.edit', item.id)}
                                                        onDelete={() => handleDelete(item)}
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}

                    {/* Pagination here if needed */}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
