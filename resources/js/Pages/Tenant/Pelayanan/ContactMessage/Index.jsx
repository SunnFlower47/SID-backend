import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ContactMessageStats from '@/Components/ContactMessage/ContactMessageStats';
import ContactMessageFilters from '@/Components/ContactMessage/ContactMessageFilters';
import { Mailbox, Eye, Trash2, MailWarning, MailCheck, MailOpen, Archive } from 'lucide-react';
import Swal from 'sweetalert2';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

const STATUS_COLORS = {
    unread: { bg: 'bg-red-100', text: 'text-red-800', icon: MailWarning, label: 'Belum Dibaca' },
    read: { bg: 'bg-yellow-100', text: 'text-yellow-800', icon: MailOpen, label: 'Sudah Dibaca' },
    replied: { bg: 'bg-emerald-100', text: 'text-emerald-800', icon: MailCheck, label: 'Sudah Dijawab' },
    archived: { bg: 'bg-gray-100', text: 'text-gray-600', icon: Archive, label: 'Diarsipkan' },
};

function StatusBadge({ status }) {
    const cfg = STATUS_COLORS[status] || STATUS_COLORS.unread;
    const Icon = cfg.icon;
    return (
        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${cfg.bg} ${cfg.text}`}>
            <Icon className="w-3 h-3" />
            {cfg.label}
        </span>
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
        <AuthenticatedLayout user={auth.user} title="Pesan Kontak Warga">
            <Head title="Pesan Kontak Warga" />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">
                {/* Header Section */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Mailbox className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Kotak Masuk Desa
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Manajemen Pesan & Komunikasi Warga
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Statistics Cards */}
                <ContactMessageStats stats={stats} />

                {/* Search & Filter Panel */}
                <ContactMessageFilters filters={filters} />

                {/* Data Table */}
                <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden shadow-black/5">
                    {/* Desktop View */}
                    <div className="hidden lg:block overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-gray-50/80 border-b border-gray-100">
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Diterima</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Pengirim</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Subjek & Pesan</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap text-center">Status</th>
                                    <th className="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-50">
                                {messages?.data && messages.data.length > 0 ? (
                                    messages.data.map((item) => (
                                        <tr key={item.id} className={`transition-colors ${item.status === 'unread' ? 'bg-red-50/30 hover:bg-red-50/50' : 'hover:bg-gray-50'}`}>
                                            <td className="px-6 py-4 whitespace-nowrap text-left">
                                                <p className="text-xs font-bold text-gray-900">
                                                    {format(new Date(item.created_at), 'dd MMM yyyy', { locale: localeId })}
                                                </p>
                                                <p className="text-[10px] font-medium text-gray-400 mt-0.5">
                                                    {format(new Date(item.created_at), 'HH:mm')} WIB
                                                </p>
                                            </td>
                                            <td className="px-6 py-4 text-left">
                                                <div className="flex flex-col gap-0.5">
                                                    <p className="font-bold text-gray-900 uppercase">{item.nama}</p>
                                                    <p className="text-[10px] font-medium text-gray-500 tracking-wider">{item.email}</p>
                                                    <p className="text-[10px] font-medium text-gray-400 tracking-wider">{item.telepon}</p>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 max-w-sm text-left">
                                                <div className="flex flex-col gap-1">
                                                    <p className="text-xs font-black text-gray-900 line-clamp-1 uppercase tracking-tight">{item.subjek}</p>
                                                    <p className="text-xs text-gray-500 line-clamp-2 leading-relaxed">{item.pesan}</p>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <StatusBadge status={item.status} />
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <Link
                                                        href={route('contact-messages.show', item.id)}
                                                        className="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors tooltip-trigger"
                                                        title="Lihat Detail & Balas"
                                                    >
                                                        <Eye className="w-4 h-4" />
                                                    </Link>
                                                    {item.status !== 'archived' && (
                                                        <button
                                                            onClick={() => handleArchive(item.id)}
                                                            className="p-2 text-gray-600 hover:bg-gray-100 rounded-xl transition-colors tooltip-trigger"
                                                            title="Arsipkan"
                                                        >
                                                            <Archive className="w-4 h-4" />
                                                        </button>
                                                    )}
                                                    <button
                                                        onClick={() => handleDelete(id, item.subjek)}
                                                        className="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors tooltip-trigger"
                                                        title="Hapus"
                                                    >
                                                        <Trash2 className="w-4 h-4" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-12 text-center text-gray-500">
                                            <div className="flex flex-col items-center justify-center">
                                                <Mailbox className="w-12 h-12 text-gray-300 mb-3" />
                                                <p className="text-sm font-bold uppercase tracking-widest text-gray-400">Tidak ada pesan kontak</p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile View */}
                    <div className="lg:hidden divide-y divide-gray-50">
                        {messages?.data && messages.data.length > 0 ? (
                            messages.data.map((item) => (
                                <div key={item.id} className={`p-5 space-y-4 ${item.status === 'unread' ? 'bg-red-50/30' : ''}`}>
                                    <div className="flex justify-between items-start gap-4">
                                        <div>
                                            <h3 className="font-bold text-gray-900 uppercase text-sm">{item.nama}</h3>
                                            <p className="text-[10px] text-gray-500 mt-0.5">{item.email}</p>
                                        </div>
                                        <StatusBadge status={item.status} />
                                    </div>
                                    
                                    <div className="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                        <p className="text-[11px] font-black text-gray-900 uppercase tracking-wide mb-1 text-left">{item.subjek}</p>
                                        <p className="text-xs text-gray-600 line-clamp-2 text-left">{item.pesan}</p>
                                    </div>

                                    <div className="flex items-center justify-between pt-2">
                                        <span className="text-[10px] font-bold text-gray-400 uppercase">
                                            {format(new Date(item.created_at), 'dd MMM yyyy', { locale: localeId })}
                                        </span>
                                        <div className="flex gap-2">
                                            <Link
                                                href={route('contact-messages.show', item.id)}
                                                className="p-2 text-blue-600 bg-blue-50 rounded-lg"
                                            >
                                                <Eye className="w-4 h-4" />
                                            </Link>
                                            {item.status !== 'archived' && (
                                                <button
                                                    onClick={() => handleArchive(item.id)}
                                                    className="p-2 text-gray-600 bg-gray-100 rounded-lg"
                                                >
                                                    <Archive className="w-4 h-4" />
                                                </button>
                                            )}
                                            <button
                                                onClick={() => handleDelete(item.id, item.subjek)}
                                                className="p-2 text-red-600 bg-red-50 rounded-lg"
                                            >
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="p-12 text-center text-gray-500">
                                <Mailbox className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                <p className="text-sm font-bold uppercase tracking-widest text-gray-400">Tidak ada pesan kontak</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Pagination */}
                {messages?.links && messages.links.length > 3 && (
                    <div className="mt-6">
                        <div className="flex items-center justify-between">
                            <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p className="text-xs text-gray-500 font-bold uppercase tracking-widest">
                                        Menampilkan <span className="font-black text-gray-900">{messages?.from}</span> - <span className="font-black text-gray-900">{messages?.to}</span> dari <span className="font-black text-gray-900">{messages?.total}</span> pesan
                                    </p>
                                </div>
                                <div>
                                    <nav className="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination">
                                        {messages.links.map((link, i) => (
                                            <Link
                                                key={i}
                                                href={link.url || '#'}
                                                className={`relative inline-flex items-center px-4 py-2 text-xs font-black uppercase tracking-widest ${
                                                    link.active 
                                                    ? 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' 
                                                    : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50'
                                                } ${i === 0 ? 'rounded-l-xl' : ''} ${i === messages.links.length - 1 ? 'rounded-r-xl' : ''} ${!link.url ? 'opacity-50 cursor-not-allowed' : ''}`}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                                preserveScroll
                                            />
                                        ))}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
