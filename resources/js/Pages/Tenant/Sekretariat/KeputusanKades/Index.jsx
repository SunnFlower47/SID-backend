import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons } from '@/Components/Shared';
import { Scale, Plus, Pencil, Trash2, Download, FileText } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

export default function Index({ auth, keputusan_kades, filters }) {
    const handleSearch = (e) => {
        router.get(route('sekretariat.keputusan-kades.index'), {
            search: e.target.value
        }, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        });
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
            router.delete(route('sekretariat.keputusan-kades.destroy', id));
        }
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

                <form className="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-3">
                    <div className="relative flex-1">
                        <FileText className="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                        <input
                            type="text"
                            placeholder="Cari nomor atau judul SK..."
                            defaultValue={filters.search}
                            onChange={handleSearch}
                            className="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm focus:ring-blue-500 focus:border-blue-500 transition-all"
                        />
                    </div>
                </form>

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
                            <div className="flex gap-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                                {keputusan_kades.links.map((link, i) => (
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
