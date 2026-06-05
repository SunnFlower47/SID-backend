import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons } from '@/Components/Shared';
import { BookOpen, Plus, Pencil, Trash2, Download, FileText } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

export default function Index({ auth, peraturans, filters }) {
    const handleSearch = (e) => {
        router.get(route('sekretariat.peraturan-desa.index'), {
            search: e.target.value,
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
        if (confirm('Apakah Anda yakin ingin menghapus peraturan ini?')) {
            router.delete(route('sekretariat.peraturan-desa.destroy', id));
        }
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

                <form className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <FileText className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari nomor atau judul..."
                            defaultValue={filters.search}
                            onChange={handleSearch}
                            className="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                    <div className="w-full sm:w-64">
                        <select 
                            onChange={handleFilterJenis} 
                            value={filters.jenis || ''}
                            className="w-full h-full border border-gray-200 bg-gray-50 rounded-2xl px-4 text-sm font-bold text-gray-700 focus:ring-blue-500 focus:border-blue-500 transition-all"
                        >
                            <option value="">Semua Jenis Peraturan</option>
                            <option value="Peraturan Desa">Peraturan Desa</option>
                            <option value="Peraturan Kepala Desa">Peraturan Kepala Desa</option>
                            <option value="Peraturan Bersama Kepala Desa">Peraturan Bersama Kepala Desa</option>
                            <option value="APBDes">APBDes</option>
                        </select>
                    </div>
                </form>

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
                            <div className="flex gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                                {peraturans.links.map((link, i) => (
                                    <Link
                                        key={i}
                                        href={link.url || '#'}
                                        className={`px-3 py-2 text-xs font-bold rounded-lg transition-all ${
                                            link.active
                                                ? 'bg-blue-600 text-white shadow-md shadow-blue-200'
                                                : 'text-gray-500 hover:bg-gray-100'
                                        } ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
