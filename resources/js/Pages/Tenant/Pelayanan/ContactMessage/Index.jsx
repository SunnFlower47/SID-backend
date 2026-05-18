import React from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ContactMessageStats from '@/Components/ContactMessage/ContactMessageStats';
import ContactMessageFilters from '@/Components/ContactMessage/ContactMessageFilters';
import Pagination from '@/Components/Shared/Pagination';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import { Mailbox, Eye, Trash2, MailWarning, MailCheck, MailOpen, Archive, CheckCircle } from 'lucide-react';
import Swal from 'sweetalert2';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

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
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[9px] font-black uppercase tracking-widest ${cfg.bg} ${cfg.text}`}>
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
        <AuthenticatedLayout user={auth.user} title="Pesan Masuk">
            <Head title="Manajemen Pesan Masuk" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden text-left text-white">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4 text-left">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Mailbox className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black tracking-tight uppercase italic leading-none">
                                    Pesan Masuk
                                </h1>
                                <p className="text-green-50 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">
                                    Pusat Komunikasi & Kontak Warga
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <ContactMessageStats stats={stats} />
                </Deferred>

                {/* Filters */}
                <ContactMessageFilters filters={filters} />

                {/* Data Table */}
                <Deferred data="messages" fallback={<SkeletonTable columns={5} rows={10} />}>
                    <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-left">
                        <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                            <h3 className="text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                                <Mailbox className="w-6 h-6 text-green-600" />
                                Daftar Pesan
                            </h3>
                            <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest italic">
                                Total: {messages?.total || 0}
                            </span>
                        </div>

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
                                            <td colSpan="4" className="px-6 py-12 text-center">
                                                <div className="w-48 h-48 mx-auto">
                                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                                </div>
                                                <p className="text-sm font-black text-gray-900 mt-2">Tidak Ada Pesan</p>
                                                <p className="text-xs text-gray-500 mt-1">Belum ada pesan masuk dari warga.</p>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                            <Pagination links={messages?.links} from={messages?.from} to={messages?.to} total={messages?.total} />
                        </div>
                    </div>
                </Deferred>
            </div>
        </AuthenticatedLayout>
    );
}
