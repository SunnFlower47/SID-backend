import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { HandHeart, Edit, Trash2, Users, ArrowLeft, Calendar, DollarSign, Building2, CheckCircle, Clock, XCircle } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, Badge, InfoRow } from '@/Components/Shared';

// ── Helpers ────────────────────────────────────────────────────
const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        color: 'green',  icon: CheckCircle },
    selesai:      { label: 'Selesai',      color: 'gray',   icon: Clock },
    ditangguhkan: { label: 'Ditangguhkan', color: 'yellow', icon: XCircle },
};

const JENIS_CONFIG = {
    BLT:           'blue',
    PKH:           'purple',
    BPNT:          'teal',
    'Bansos Lainnya': 'orange',
};

// ────────────────────────────────────────────────────────────────
export default function Show({ auth, bantuanSosial }) {
    const statusCfg = STATUS_CONFIG[bantuanSosial.status] ?? STATUS_CONFIG.ditangguhkan;
    const StatusIcon = statusCfg.icon;

    const handleDelete = () => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus program <b class="text-red-600">${bantuanSosial.nama_program}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
            },
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(route('bantuan-sosial.destroy', bantuanSosial.id));
            }
        });
    };

    const kriteria = Array.isArray(bantuanSosial.kriteria_penerima)
        ? bantuanSosial.kriteria_penerima
        : [];

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Program Bantuan Sosial">
            <Head title={bantuanSosial.nama_program} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <PageHeader 
                    title={bantuanSosial.nama_program}
                    subtitle="Detail Program Bantuan Sosial"
                    icon={HandHeart}
                    backHref={route('bantuan-sosial.index')}
                    actions={[
                        {
                            label: 'EDIT',
                            icon: Edit,
                            href: route('bantuan-sosial.edit', bantuanSosial.id),
                            variant: 'white'
                        },
                        {
                            label: 'HAPUS',
                            icon: Trash2,
                            onClick: handleDelete,
                            variant: 'danger'
                        }
                    ]}
                />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    {/* ── Left: Detail Info ── */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* Info Utama */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Program</h3>
                            </div>
                            <div className="p-6">
                                <div className="mb-6 flex flex-wrap gap-2">
                                    <Badge color={JENIS_CONFIG[bantuanSosial.jenis_bantuan] ?? 'gray'}>
                                        {bantuanSosial.jenis_bantuan}
                                    </Badge>
                                    <Badge color={statusCfg.color} icon={StatusIcon}>
                                        {statusCfg.label}
                                    </Badge>
                                </div>

                                <p className="text-sm text-gray-600 leading-relaxed mb-6 italic">{bantuanSosial.deskripsi}</p>

                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <InfoRow label="Periode" value={bantuanSosial.periode} icon={Calendar} color="blue" />
                                    <InfoRow label="Sumber Dana" value={bantuanSosial.sumber_dana} icon={Building2} color="purple" />
                                    <InfoRow
                                        label="Tanggal Mulai"
                                        value={bantuanSosial.tanggal_mulai
                                            ? new Date(bantuanSosial.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                            : null}
                                        icon={Calendar} 
                                        color="blue"
                                    />
                                    <InfoRow
                                        label="Tanggal Selesai"
                                        value={bantuanSosial.tanggal_selesai
                                            ? new Date(bantuanSosial.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                            : null}
                                        icon={Calendar} 
                                        color="red"
                                    />
                                    <InfoRow
                                        label="Nilai Bantuan"
                                        value={bantuanSosial.nilai_bantuan
                                            ? `Rp ${Number(bantuanSosial.nilai_bantuan).toLocaleString('id-ID')}`
                                            : null}
                                        icon={DollarSign} 
                                        color="green"
                                    />
                                    <InfoRow
                                        label="Kuota Penerima"
                                        value={bantuanSosial.kuota_penerima
                                            ? `${Number(bantuanSosial.kuota_penerima).toLocaleString('id-ID')} orang`
                                            : 'Tidak terbatas'}
                                        icon={Users} 
                                        color="orange"
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Kriteria Penerima */}
                        {kriteria.length > 0 && (
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Kriteria Penerima</h3>
                                </div>
                                <div className="p-6">
                                    <ul className="space-y-3">
                                        {kriteria.map((k, i) => (
                                            <li key={i} className="flex items-start gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                                <span className="w-6 h-6 bg-green-100 text-green-700 rounded-lg flex items-center justify-center text-xs font-black shrink-0 mt-0.5 shadow-sm border border-green-200">
                                                    {i + 1}
                                                </span>
                                                <span className="text-sm text-gray-700 italic">{k}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* ── Right: Quick Stats + Penerima CTA ── */}
                    <div className="space-y-5">
                        {/* Stats Penerima */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Statistik Penerima</h3>
                            </div>
                            <div className="p-6 text-center">
                                <div className="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner border border-purple-200">
                                    <Users className="w-8 h-8 text-purple-600" />
                                </div>
                                <p className="text-4xl font-black text-gray-900 italic tracking-tighter">{bantuanSosial.penerima_count ?? 0}</p>
                                <p className="text-xs font-black text-gray-400 uppercase tracking-widest mt-1">Total Penerima</p>
                                {bantuanSosial.kuota_penerima && (
                                    <div className="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div className="w-full bg-gray-200 rounded-full h-2 mb-2 overflow-hidden shadow-inner">
                                            <div
                                                className="bg-green-500 h-2 rounded-full transition-all duration-1000"
                                                style={{ width: `${Math.min((bantuanSosial.penerima_count / bantuanSosial.kuota_penerima) * 100, 100)}%` }}
                                            />
                                        </div>
                                        <p className="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                            {bantuanSosial.penerima_count} / {bantuanSosial.kuota_penerima} kuota terisi
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* CTA Kelola Penerima */}
                        <Link
                            href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                            className="flex flex-col items-center justify-center gap-3 p-6 bg-gradient-to-br from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl shadow-xl shadow-green-200 transition-all hover:scale-[1.02] text-center border border-green-500"
                        >
                            <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/30 shadow-inner">
                                <Users className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <p className="font-black text-sm uppercase tracking-widest italic">KELOLA PENERIMA</p>
                                <p className="text-green-100 text-[10px] font-bold uppercase tracking-wider mt-1 opacity-90">Tambah &amp; kelola data penerima</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
