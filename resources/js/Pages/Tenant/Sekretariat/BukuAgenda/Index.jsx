import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, EmptyState, Badge, ActionButtons } from '@/Components/Shared';
import { Mails, Plus, Pencil, Trash2, FileText, ArrowUpRight, ArrowDownLeft } from 'lucide-react';
import dayjs from 'dayjs';
import 'dayjs/locale/id';

dayjs.locale('id');

export default function Index({ auth, agendas, filters }) {
    const handleSearch = (e) => {
        router.get(route('sekretariat.buku-agenda.index'), {
            search: e.target.value,
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
        if (confirm('Apakah Anda yakin ingin menghapus data surat ini?')) {
            router.delete(route('sekretariat.buku-agenda.destroy', id));
        }
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

                <TableCard>
                    <div className="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
                        <div className="relative w-full sm:w-72">
                            <input
                                type="text"
                                placeholder="Cari nomor, pengirim, atau isi..."
                                defaultValue={filters.search}
                                onChange={handleSearch}
                                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                            />
                            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <FileText className="h-5 w-5 text-gray-400" />
                            </div>
                        </div>
                        <div className="w-full sm:w-48">
                            <select 
                                onChange={handleFilterJenis} 
                                value={filters.jenis || ''}
                                className="w-full border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 font-medium"
                            >
                                <option value="">Semua Surat</option>
                                <option value="Masuk">Surat Masuk</option>
                                <option value="Keluar">Surat Keluar</option>
                            </select>
                        </div>
                    </div>

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
                        <div className="flex flex-wrap justify-center gap-1 mt-6 pt-6 border-t border-gray-100">
                            {agendas.links.map((link, k) => (
                                <Link
                                    key={k}
                                    href={link.url}
                                    className={`px-4 py-2 text-sm font-medium rounded-lg transition-colors ${
                                        link.active 
                                        ? 'bg-blue-600 text-white shadow-md' 
                                        : 'bg-white text-gray-500 hover:bg-gray-100 border'
                                    } ${!link.url && 'opacity-50 cursor-not-allowed'}`}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            ))}
                        </div>
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
