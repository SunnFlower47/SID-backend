import React, { useState, useEffect } from 'react';
import { FilterContainer } from '@/Components/Shared';
import { cn } from '@/lib/utils';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons, Pagination } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Scale, Plus, Pencil, Trash2, Download, FileText, Search, Filter } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import Swal from 'sweetalert2';

dayjs.locale('id');

export default function Index({ auth, keputusan_kades, filters }) {
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        const removeStart = router.on('start', () => setIsLoading(true));
        const removeFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    const [search, setSearch] = useState(filters?.search || '');
    const hasActiveFilters = filters?.search || filters?.status || filters?.jenis;
    

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        router.get(route('sekretariat.keputusan-kades.index'), {
            search: search
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Hapus Dokumen?',
            text: 'Apakah Anda yakin ingin menghapus dokumen ini? Tindakan ini tidak dapat dibatalkan.',
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
                router.delete(route('sekretariat.keputusan-kades.destroy', id));
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth?.user} title="Keputusan Kades">
            <Head title="Keputusan Kades - Sekretariat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={Scale}
                    title="Keputusan Kepala Desa"
                    subtitle="Manajemen Dokumen Produk Hukum Internal Desa"
                    actions={[
                        { label: 'Tambah SK Baru', icon: Plus, href: route('sekretariat.keputusan-kades.create'), variant: 'white' }
                    ]}
                />

                <FilterContainer hasActiveFilters={hasActiveFilters}>
                    <form onSubmit={handleSearchSubmit} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-end ">
                        <div className="flex-1 w-full space-y-2 text-left">
                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Pencarian</label>
                            <div className="relative">
                                <FileText className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                                <input
                                    type="text"
                                    placeholder="Cari nomor atau judul..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                />
                            </div>
                        </div>
                        <button type="submit" className="flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                            <Search className="w-3.5 h-3.5" /> CARI
                        </button>
                    </form>
                </FilterContainer>

                {isLoading ? (
                    <SkeletonTable rows={5} columns={5} />
                ) : (
                <TableCard
                    icon={Scale}
                    title="Daftar Keputusan Kepala Desa"
                    total={keputusan_kades.total}
                    totalLabel="Dokumen"
                >
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs min-w-[1000px]">
                            <thead>
                                <tr className="bg-gray-50/80 border-b border-gray-100">
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest rounded-tl-xl w-48">Nomor Keputusan</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest">Judul / Tentang</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest w-40">Tgl Ditetapkan</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest w-48">Keterangan</th>
                                    <th className="px-4 py-4 text-right font-black text-gray-500 uppercase tracking-widest rounded-tr-xl w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {keputusan_kades.data.length > 0 ? (
                                    keputusan_kades.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-blue-50/50 transition-colors border-b border-gray-50">
                                            <td className="px-4 py-4">
                                                <div className="font-bold text-gray-900">{item.nomor_keputusan}</div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-gray-900 font-medium max-w-md line-clamp-2">
                                                    {item.judul_keputusan}
                                                </div>
                                                {item.file_dokumen && (
                                                    <a
                                                        href={`/storage/${item.file_dokumen}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-[10px] font-bold mt-2 bg-blue-50 px-2 py-1 rounded-md"
                                                    >
                                                        <Download className="w-3.5 h-3.5" />
                                                        Unduh Dokumen
                                                    </a>
                                                )}
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-[10px] font-bold text-gray-800 uppercase tracking-widest mt-1">
                                                    {dayjs(item.tanggal_ditetapkan).format('DD MMM YYYY')}
                                                </div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-gray-600">
                                                    {item.keterangan || '-'}
                                                </div>
                                            </td>
                                            <td className="px-4 py-4 text-right">
                                                <ActionButtons
                                                    editHref={route('sekretariat.keputusan-kades.edit', item.id)}
                                                    onDelete={() => handleDelete(item.id)}
                                                />
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                                            <EmptyState
                                                icon={FileText}
                                                title="Belum Ada Keputusan Kades"
                                                description="Data keputusan kepala desa akan muncul di sini."
                                            />
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {keputusan_kades.links && keputusan_kades.links.length > 3 && (
                        <div className="p-4 border-t border-gray-100 flex justify-center bg-gray-50/50 rounded-b-2xl">
                            <Pagination 
                                links={keputusan_kades.links} 
                                from={keputusan_kades.from} 
                                to={keputusan_kades.to} 
                                total={keputusan_kades.total} 
                            />
                        </div>
                    )}
                </TableCard>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
