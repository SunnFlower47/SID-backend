import React, { useState, useEffect } from 'react';
import { FilterContainer } from '@/Components/Shared';
import { cn } from '@/lib/utils';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons, Pagination } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Mails, Plus, Pencil, Trash2, FileText, ArrowUpRight, ArrowDownLeft, Search, Filter } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import Swal from 'sweetalert2';

dayjs.locale('id');

export default function Index({ auth, agendas, filters }) {
    const [isLoading, setIsLoading] = useState(false);
    const [search, setSearch] = useState(filters?.search || '');
    const hasActiveFilters = filters?.search || filters?.status || filters?.jenis;
    

    useEffect(() => {
        const removeStart = router.on('start', () => setIsLoading(true));
        const removeFinish = router.on('finish', () => setIsLoading(false));
        return () => {
            removeStart();
            removeFinish();
        };
    }, []);

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        router.get(route('sekretariat.buku-agenda.index'), {
            search: search,
            jenis: filters.jenis
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleFilterJenis = (e) => {
        router.get(route('sekretariat.buku-agenda.index'), {
            search: filters.search,
            jenis: e.target.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Hapus Data Surat?',
            text: 'Apakah Anda yakin ingin menghapus data surat ini? Tindakan ini tidak dapat dibatalkan.',
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
                router.delete(route('sekretariat.buku-agenda.destroy', id));
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth?.user}>
            <Head title="Buku Agenda Surat - Sekretariat" />

            <div className="space-y-6 pb-20">
                <PageHeader
                    icon={Mails}
                    title="Buku Agenda Surat"
                    subtitle="Manajemen dan pencatatan surat masuk dan surat keluar desa"
                    actions={[
                        { label: 'Tambah Surat Baru', icon: Plus, href: route('sekretariat.buku-agenda.create', { jenis: filters.jenis || 'Masuk' }), variant: 'white' }
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
                                    placeholder="Cari nomor, pengirim, atau isi..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                                />
                            </div>
                        </div>
                        <div className="w-full sm:w-64 space-y-2 text-left">
                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Surat</label>
                            <select 
                                onChange={handleFilterJenis} 
                                value={filters.jenis || ''}
                                className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                            >
                                <option value="">Semua Surat</option>
                                <option value="Masuk">Surat Masuk</option>
                                <option value="Keluar">Surat Keluar</option>
                            </select>
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
                    icon={Mails}
                    title="Daftar Buku Agenda Surat"
                    total={agendas.total}
                    totalLabel="Surat"
                >
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm text-left">
                            <thead className="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                                <tr>
                                    <th className="px-6 py-4 font-black">Tanggal Catat</th>
                                    <th className="px-6 py-4 font-black">Informasi Surat</th>
                                    <th className="px-6 py-4 font-black">Pengirim / Penerima</th>
                                    <th className="px-6 py-4 font-black">Isi Singkat</th>
                                    <th className="px-6 py-4 font-black text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100">
                                {agendas.data.length > 0 ? (
                                    agendas.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-blue-50/50 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="font-bold text-gray-900">{dayjs(item.tanggal).format('DD MMM YYYY')}</div>
                                                <div className="mt-1">
                                                    {item.jenis_surat === 'Masuk' ? (
                                                        <Badge color="green" icon={ArrowDownLeft}>Surat Masuk</Badge>
                                                    ) : (
                                                        <Badge color="blue" icon={ArrowUpRight}>Surat Keluar</Badge>
                                                    )}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="font-bold text-gray-900">{item.nomor_surat || '-'}</div>
                                                <div className="text-xs text-gray-500 mt-1">
                                                    Tgl: {dayjs(item.tanggal_surat).format('DD MMM YYYY')}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-gray-900 font-medium max-w-xs truncate">
                                                    {item.pengirim_penerima}
                                                </div>
                                                {item.keterangan && (
                                                    <div className="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                                        Ket: {item.keterangan}
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="text-gray-700 max-w-md line-clamp-2" title={item.isi_singkat}>
                                                    {item.isi_singkat}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <ActionButtons
                                                    editHref={route('sekretariat.buku-agenda.edit', item.id)}
                                                    onDelete={() => handleDelete(item.id)}
                                                />
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                                            <EmptyState
                                                icon={Mails}
                                                title="Belum Ada Data Surat"
                                                description="Data surat masuk dan keluar akan muncul di sini."
                                            />
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {agendas.links && agendas.links.length > 3 && (
                        <div className="p-4 border-t border-gray-100 flex justify-center bg-gray-50/50 rounded-b-2xl">
                            <Pagination 
                                links={agendas.links} 
                                from={agendas.from} 
                                to={agendas.to} 
                                total={agendas.total} 
                            />
                        </div>
                    )}
                </TableCard>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
