import React, { useState, useEffect } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons, Pagination } from '@/Components/Shared';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { BookOpen, Plus, Pencil, Trash2, Download, FileText, Search } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';
import Swal from 'sweetalert2';

dayjs.locale('id');

export default function Index({ auth, peraturans, filters }) {
    const [isLoading, setIsLoading] = useState(false);
    const [search, setSearch] = useState(filters?.search || '');

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
        router.get(route('sekretariat.peraturan-desa.index'), {
            search: search,
            jenis: filters.jenis,
            tahun: filters.tahun
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleFilterJenis = (e) => {
        router.get(route('sekretariat.peraturan-desa.index'), {
            search: filters.search,
            jenis: e.target.value,
            tahun: filters.tahun
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        Swal.fire({
            title: 'Hapus Peraturan?',
            text: 'Apakah Anda yakin ingin menghapus peraturan ini? Tindakan ini tidak dapat dibatalkan.',
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
                router.delete(route('sekretariat.peraturan-desa.destroy', id));
            }
        });
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'disetujui': return 'green';
            case 'ditolak': return 'red';
            case 'dibahas': return 'amber';
            case 'diajukan_bpd': return 'blue';
            default: return 'gray';
        }
    };

    const getStatusLabel = (status) => {
        switch (status) {
            case 'disetujui': return 'Disetujui';
            case 'ditolak': return 'Ditolak';
            case 'dibahas': return 'Dibahas BPD';
            case 'diajukan_bpd': return 'Diajukan ke BPD';
            default: return 'Draft';
        }
    };

    return (
        <AuthenticatedLayout user={auth?.user} title="Peraturan Desa">
            <Head title="Peraturan Desa - Sekretariat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    icon={BookOpen}
                    title="Peraturan Desa"
                    subtitle="Manajemen Dokumen Peraturan Desa, Peraturan Bersama, dan Perdes"
                    actions={[
                        { label: 'Tambah Peraturan', icon: Plus, href: route('sekretariat.peraturan-desa.create'), variant: 'white' }
                    ]}
                />

                <form onSubmit={handleSearchSubmit} className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-4 items-end mb-6">
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
                    <div className="w-full sm:w-64 space-y-2 text-left">
                        <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest ml-1">Jenis Peraturan</label>
                        <select 
                            onChange={handleFilterJenis} 
                            value={filters.jenis || ''}
                            className="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-xs font-bold focus:ring-2 focus:ring-green-500 transition-all shadow-inner"
                        >
                            <option value="">Semua Jenis Peraturan</option>
                            <option value="Peraturan Desa">Peraturan Desa</option>
                            <option value="Peraturan Kepala Desa">Peraturan Kepala Desa</option>
                            <option value="Peraturan Bersama Kepala Desa">Peraturan Bersama Kepala Desa</option>
                            <option value="APBDes">APBDes</option>
                        </select>
                    </div>
                    <button type="submit" className="flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3 bg-green-600 text-white rounded-2xl text-[10px] font-black hover:bg-green-700 active:scale-95 transition-all uppercase tracking-widest shadow-md shadow-green-200">
                        <Search className="w-3.5 h-3.5" /> CARI
                    </button>
                </form>

                {isLoading ? (
                    <SkeletonTable rows={5} columns={5} />
                ) : (
                <TableCard
                    icon={BookOpen}
                    title="Daftar Peraturan Desa"
                    total={peraturans.total}
                    totalLabel="Peraturan"
                >
                    <div className="overflow-x-auto">
                        <table className="w-full text-xs min-w-[1000px]">
                            <thead>
                                <tr className="bg-gray-50/80 border-b border-gray-100">
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest rounded-tl-xl w-48">Nomor & Jenis</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest">Judul Peraturan</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest w-40">Tahun / Tanggal</th>
                                    <th className="px-4 py-4 text-left font-black text-gray-500 uppercase tracking-widest w-32">Status</th>
                                    <th className="px-4 py-4 text-right font-black text-gray-500 uppercase tracking-widest rounded-tr-xl w-32">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {peraturans.data.length > 0 ? (
                                    peraturans.data.map((item) => (
                                        <tr key={item.id} className="hover:bg-blue-50/50 transition-colors border-b border-gray-50">
                                            <td className="px-4 py-4">
                                                <div className="font-bold text-gray-900">{item.nomor_peraturan || '-'}</div>
                                                <div className="text-[10px] text-blue-600 mt-1 font-bold tracking-wider uppercase">
                                                    {item.jenis_peraturan}
                                                </div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <div className="text-gray-900 font-medium max-w-md line-clamp-2">
                                                    {item.judul}
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
                                                <div className="font-bold text-gray-800">Tahun {item.tahun_anggaran}</div>
                                                <div className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">
                                                    {item.tanggal_ditetapkan ? dayjs(item.tanggal_ditetapkan).format('DD MMM YYYY') : '-'}
                                                </div>
                                            </td>
                                            <td className="px-4 py-4">
                                                <Badge color={getStatusColor(item.status)}>
                                                    {getStatusLabel(item.status)}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-4 text-right">
                                                <ActionButtons
                                                    editHref={route('sekretariat.peraturan-desa.edit', item.id)}
                                                    onDelete={() => handleDelete(item.id)}
                                                />
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                                            <EmptyState
                                                icon={BookOpen}
                                                title="Belum Ada Peraturan"
                                                description="Data peraturan desa akan muncul di sini."
                                            />
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {peraturans.links && peraturans.links.length > 3 && (
                        <div className="p-4 border-t border-gray-100 flex justify-center bg-gray-50/50 rounded-b-2xl">
                            <Pagination 
                                links={peraturans.links} 
                                from={peraturans.from} 
                                to={peraturans.to} 
                                total={peraturans.total} 
                            />
                        </div>
                    )}
                </TableCard>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
