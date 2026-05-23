import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ContactMessageStats from '@/Components/ContactMessage/ContactMessageStats';
import ContactMessageFilters from '@/Components/ContactMessage/ContactMessageFilters';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Mailbox, Eye, Trash2, MailWarning, MailCheck, MailOpen, Archive } from 'lucide-react';
import Swal from 'sweetalert2';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

// Shared Components
import { PageHeader, TableCard, Badge, EmptyState } from '@/Components/Shared';

const STATUS_COLORS = {
    unread: { color: 'red', icon: MailWarning, label: 'Belum Dibaca' },
    read: { color: 'yellow', icon: MailOpen, label: 'Sudah Dibaca' },
    replied: { color: 'green', icon: MailCheck, label: 'Sudah Dijawab' },
    archived: { color: 'gray', icon: Archive, label: 'Diarsipkan' },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.unread;
    return (
        <Badge color={cfg.color} icon={cfg.icon}>
            {cfg.label}
        </Badge>
    );
}

export default function Index({ auth, messages, stats, filters }) {
    const handleDelete = (id, subjek) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus pesan <b class="text-red-600">${subjek}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('contact-messages.destroy', id), {
                    preserveScroll: true
                });
            }
        });
    };

    const handleArchive = (id) => {
        router.post(route('contact-messages.archive', id), {}, {
            preserveScroll: true
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Pesan Masuk">
            <Head title="Manajemen Pesan Masuk" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader 
                    title="Pesan Masuk"
                    subtitle="Pusat Komunikasi & Kontak Warga"
                    icon={Mailbox}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <ContactMessageStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <ContactMessageFilters filters={filters} />

                {/* Data Table */}
                <Deferred data="messages" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <TableCard 
                        title="Daftar Pesan"
                        icon={Mailbox}
                        total={messages?.total || 0}
                        pagination={messages}
                        noPadding
                    >
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                    <tr>
                                        <th className="px-6 py-4">Waktu & Pengirim</th>
                                        <th className="px-6 py-4">Subjek & Pesan</th>
                                        <th className="px-6 py-4 text-center">Status</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {messages.data.length > 0 ? messages.data.map((item) => (
                                        <tr key={item.id} className="group hover:bg-green-50/20 transition-colors">
                                            <td className="px-6 py-5">
                                                <div className="flex flex-col gap-1">
                                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 italic">
                                                        {format(new Date(item.created_at), 'dd MMM yyyy HH:mm', { locale: localeId })}
                                                    </p>
                                                    <p className="font-bold text-gray-950 leading-none">{item.nama}</p>
                                                    <p className="text-[10px] font-medium text-gray-400 mt-1">{item.email}</p>
                                                </div>
                                            </td>
                                            <td className="px-6 py-5">
                                                <div className="max-w-md">
                                                    <p className="font-black text-gray-900 text-xs uppercase italic tracking-tight line-clamp-1 mb-1">{item.subjek}</p>
                                                    <p className="text-xs text-gray-500 line-clamp-1 italic">"{item.pesan}"</p>
                                                </div>
                                            </td>
                                            <td className="px-6 py-5 text-center">
                                                <StatusBadge status={item.status} />
                                            </td>
                                            <td className="px-6 py-5 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link 
                                                        href={route('contact-messages.show', item.id)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                        title="Lihat Detail"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    {item.status !== 'archived' && (
                                                        <button 
                                                            onClick={() => handleArchive(item.id)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 hover:bg-gray-800 hover:text-white transition-colors"
                                                            title="Arsipkan"
                                                        >
                                                            <Archive className="w-4 h-4" />
                                                        </button>
                                                    )}
                                                    <button 
                                                        onClick={() => handleDelete(item.id, item.subjek)}
                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    )) : (
                                        <tr>
                                            <td colSpan="4">
                                                <EmptyState 
                                                    title="Tidak Ada Pesan"
                                                    message="Belum ada pesan masuk dari warga."
                                                />
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </TableCard>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
