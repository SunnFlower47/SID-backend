import React from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Mailbox, ArrowLeft, Send, MailWarning, MailCheck, MailOpen, Archive, User, Phone, Mail, Globe, CheckCircle } from 'lucide-react';
import Swal from 'sweetalert2';
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

// Shared Components
import { PageHeader, Badge, InfoRow } from '@/Components/Shared';

const STATUS_COLORS = {
    unread: { color: 'red', icon: MailWarning, label: 'Belum Dibaca' },
    read: { color: 'yellow', icon: MailOpen, label: 'Sudah Dibaca' },
    replied: { color: 'green', icon: MailCheck, label: 'Sudah Dijawab' },
    archived: { color: 'gray', icon: Archive, label: 'Diarsipkan' },
};

export default function Show({ auth, contactMessage }) {
    const { data, setData, post, processing, errors } = useForm({
        admin_reply: contactMessage.admin_reply || ''
    });

    const statusCfg = STATUS_COLORS[contactMessage.status] || STATUS_COLORS.unread;
    const StatusIcon = statusCfg.icon;

    const handleReply = (e) => {
        e.preventDefault();
        
        Swal.fire({
            title: 'KIRIM BALASAN?',
            html: `Email balasan resmi akan dikirim secara otomatis ke <b>${contactMessage.email}</b>.<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Pastikan bahasa yang digunakan sudah tepat dan profesional.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, KIRIM EMAIL!',
            cancelButtonText: 'BATAL',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-emerald-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                post(route('contact-messages.mark-replied', contactMessage.id), {
                    preserveScroll: true,
                });
            }
        });
    };

    const handleArchive = () => {
        router.post(route('contact-messages.archive', contactMessage.id), {}, {
            preserveScroll: true
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title={`Pesan: ${contactMessage.subjek}`}>
            <Head title="Detail Pesan Kontak" />

            <div className="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700 pb-20">
                
                {/* Header Section */}
                <PageHeader 
                    title="Detail Pesan Masuk"
                    subtitle={`Diterima pada: ${format(new Date(contactMessage.created_at), 'dd MMMM yyyy HH:mm', { locale: localeId })} WIB`}
                    icon={Mailbox}
                    backHref={route('contact-messages.index')}
                    actions={
                        contactMessage.status !== 'archived' ? [
                            {
                                label: 'ARSIPKAN',
                                icon: Archive,
                                onClick: handleArchive,
                                variant: 'white'
                            }
                        ] : []
                    }
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Left Column: Sender Info */}
                    <div className="lg:col-span-1 space-y-6">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Pengirim</h3>
                                <Badge color={statusCfg.color} icon={StatusIcon}>
                                    {statusCfg.label}
                               </Badge>
                            </div>
                            <div className="p-6 space-y-4">
                                <InfoRow label="Nama Lengkap" value={contactMessage.nama} icon={User} color="blue" />
                                <InfoRow label="Alamat Email" value={contactMessage.email} icon={Mail} color="purple" />
                                <InfoRow label="Nomor Telepon" value={contactMessage.telepon} icon={Phone} color="green" />
                                
                                <div className="border-t border-gray-100 pt-5 mt-2 space-y-4">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                        <Globe className="w-3.5 h-3.5" /> Data Jejak Digital
                                    </p>
                                    <div>
                                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">IP Address</p>
                                        <p className="text-xs font-mono font-medium text-gray-600 bg-gray-50 p-2 rounded-lg mt-1">{contactMessage.ip_address}</p>
                                    </div>
                                    <div>
                                        <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Browser / User Agent</p>
                                        <p className="text-[10px] font-mono font-medium text-gray-600 bg-gray-50 p-2 rounded-lg mt-1 break-words">{contactMessage.user_agent}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Message & Reply */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Original Message Box */}
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
                            <div className="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-400 to-indigo-500"></div>
                            <div className="p-6 border-b border-gray-100 bg-gray-50/50 pl-8">
                                <h2 className="text-lg sm:text-xl font-black text-gray-900 uppercase tracking-tight">{contactMessage.subjek}</h2>
                            </div>
                            <div className="p-8 pl-10 text-gray-700 leading-relaxed min-h-[150px] whitespace-pre-wrap font-medium">
                                {contactMessage.pesan}
                            </div>
                        </div>

                        {/* Reply Section */}
                        {contactMessage.status === 'replied' ? (
                            <div className="bg-emerald-50 rounded-3xl border border-emerald-100 p-8 relative overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500">
                                <div className="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                                    <CheckCircle className="w-32 h-32 text-emerald-500" />
                                </div>
                                <div className="flex items-center gap-3 mb-6 relative z-10">
                                    <div className="w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200">
                                        <Send className="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h3 className="font-black text-emerald-900 uppercase italic tracking-tighter text-sm">Balasan Telah Terkirim</h3>
                                        <p className="text-[10px] font-bold text-emerald-600/80 uppercase tracking-widest mt-0.5">
                                            {contactMessage.replied_at ? format(new Date(contactMessage.replied_at), 'dd MMM yyyy HH:mm', { locale: localeId }) : 'Waktu tidak diketahui'} WIB
                                        </p>
                                    </div>
                                </div>
                                <div className="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-emerald-100 text-emerald-900 whitespace-pre-wrap font-medium leading-relaxed relative z-10 shadow-sm">
                                    {contactMessage.admin_reply}
                                </div>
                            </div>
                        ) : (
                            <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                                <div className="p-6 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
                                    <div className="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                        <Send className="w-4 h-4" />
                                    </div>
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Balas Pesan</h3>
                                </div>
                                <div className="p-6 sm:p-8">
                                    <form onSubmit={handleReply} className="space-y-6">
                                        <div>
                                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 pl-1">
                                                Tuliskan Balasan Anda
                                            </label>
                                            <textarea
                                                value={data.admin_reply}
                                                onChange={(e) => setData('admin_reply', e.target.value)}
                                                rows={6}
                                                className={`w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm resize-y ${errors.admin_reply ? 'border-red-500 ring-4 ring-red-500/10' : ''}`}
                                                placeholder="Ketik balasan resmi di sini. Balasan akan dikirim langsung ke email warga..."
                                                required
                                            />
                                            {errors.admin_reply && (
                                                <p className="mt-2 text-[10px] font-bold text-red-600 uppercase tracking-widest pl-1">{errors.admin_reply}</p>
                                            )}
                                        </div>
                                        
                                        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-gray-100">
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                                <Mail className="w-3.5 h-3.5" />
                                                Email akan dikirim secara otomatis
                                            </p>
                                            <button
                                                type="submit"
                                                disabled={processing || !data.admin_reply.trim()}
                                                className="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-emerald-200 hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:hover:scale-100 flex items-center justify-center gap-2"
                                            >
                                                {processing ? (
                                                    <>
                                                        <div className="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full" />
                                                        MENGIRIM...
                                                    </>
                                                ) : (
                                                    <>
                                                        <Send className="w-4 h-4" />
                                                        KIRIM BALASAN EMAIL
                                                    </>
                                                )}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
