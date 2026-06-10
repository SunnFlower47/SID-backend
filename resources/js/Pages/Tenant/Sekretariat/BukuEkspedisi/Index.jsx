import React, { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard, FormField, ActionButtons, Modal, EmptyState, Pagination } from '@/Components/Shared';
import { BookOpen, Calendar, FileText, Send, User, Plus } from 'lucide-react';
import Swal from 'sweetalert2';

export default function BukuEkspedisiIndex({ auth, data, filters }) {
    const { data: formData, setData, post, put, processing, errors, reset, clearErrors } = useForm({
        tanggal_pengiriman: new Date().toISOString().split('T')[0],
        tanggal_surat: new Date().toISOString().split('T')[0],
        nomor_surat: '',
        isi_singkat: '',
        tujuan: '',
        penerima: '',
        keterangan: '',
    });



    const handleDelete = (id, nomor) => {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Apakah Anda yakin ingin menghapus surat nomor <b>${nomor}</b> dari Buku Ekspedisi?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px]', cancelButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px] text-gray-500' }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('sekretariat.buku-ekspedisi.destroy', id), { preserveScroll: true });
            }
        });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('sekretariat.buku-ekspedisi.index'), { search: e.target.search.value }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Buku Ekspedisi">
            <Head title="Buku Ekspedisi - Sekretariat" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader
                    title="Buku Ekspedisi"
                    subtitle="Pencatatan pengiriman surat atau barang ke pihak luar"
                    icon={Send}
                    actions={[
                        { label: 'TAMBAH DATA', icon: Plus, href: route('sekretariat.buku-ekspedisi.create'), variant: 'white' }
                    ]}
                />

                <TableCard
                    title="Daftar Ekspedisi"
                    icon={BookOpen}
                    total={data.total}
                    onSearch={handleSearch}
                    searchDefault={filters?.search}
                >
                    {data.data.length > 0 ? (
                        <>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="bg-gray-50/80 border-b border-gray-100">
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Tgl Pengiriman</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Tgl & No Surat</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Isi Singkat</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Tujuan & Penerima</th>
                                            <th className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
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
                                                            {new Date(item.tanggal_pengiriman).toLocaleDateString('id-ID')}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 align-top">
                                                    <div className="text-xs font-bold text-gray-900">{item.nomor_surat}</div>
                                                    <div className="text-[10px] text-gray-500 mt-0.5">Tgl: {new Date(item.tanggal_surat).toLocaleDateString('id-ID')}</div>
                                                </td>
                                                <td className="px-4 py-4 align-top max-w-[200px]">
                                                    <p className="text-xs text-gray-600 line-clamp-2">{item.isi_singkat}</p>
                                                </td>
                                                <td className="px-4 py-4 align-top">
                                                    <div className="text-xs font-bold text-blue-600">{item.tujuan}</div>
                                                    {item.penerima && (
                                                        <div className="flex items-center gap-1 text-[10px] text-gray-500 mt-1">
                                                            <User className="w-3 h-3" /> Penerima: {item.penerima}
                                                        </div>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 align-top">
                                                    <span className="text-[10px] text-gray-500">{item.keterangan || '-'}</span>
                                                </td>
                                                <td className="px-4 py-4 align-top text-right">
                                                    <ActionButtons
                                                        viewHref={route('sekretariat.buku-ekspedisi.show', item.id)}
                                                        editHref={route('sekretariat.buku-ekspedisi.edit', item.id)}
                                                        onDelete={() => handleDelete(item.id, item.nomor_surat)}
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
                            icon={Send}
                            title="Belum Ada Data Ekspedisi"
                            message="Klik tombol Tambah Data untuk mencatat pengiriman surat/barang."
                            actionLabel="TAMBAH DATA"
                            actionHref={route('sekretariat.buku-ekspedisi.create')}
                        />
                    )}
                </TableCard>
            </div>


        </AuthenticatedLayout>
    );
}
