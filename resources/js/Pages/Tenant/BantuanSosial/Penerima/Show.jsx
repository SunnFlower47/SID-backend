import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, ArrowLeft, Edit, Trash2, CheckCircle, Clock, XCircle, Calendar, DollarSign, User, Shield, MapPin, FileText } from 'lucide-react';
import Swal from 'sweetalert2';

// Shared Components
import { PageHeader, Badge, InfoRow } from '@/Components/Shared';

const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        color: 'green',  icon: CheckCircle },
    ditangguhkan: { label: 'Ditangguhkan', color: 'yellow', icon: Clock },
    berhenti:     { label: 'Berhenti',     color: 'red',    icon: XCircle },
};

export default function Show({ auth, bantuanSosial, penerima }) {
    const statusCfg = STATUS_CONFIG[penerima.status_penerimaan] ?? STATUS_CONFIG.ditangguhkan;
    const StatusIcon = statusCfg.icon;

    const dataTambahan = (() => {
        try { return typeof penerima.data_tambahan === 'string' ? JSON.parse(penerima.data_tambahan || '{}') : (penerima.data_tambahan ?? {}); }
        catch { return {}; }
    })();
    const isBerkala = dataTambahan?.sistem_pembayaran === 'berkala' || dataTambahan?.sistem_pembayaran === 'triwulanan';

    const handleDelete = () => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus penerima <b class="text-red-600">${penerima.penduduk?.nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('bantuan-sosial.penerima.destroy', [bantuanSosial.id, penerima.id]));
            }
        });
    };

    const fmt = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '—';
    const fmtRp = (v) => `Rp ${Number(v).toLocaleString('id-ID')}`;

    return (
        <AuthenticatedLayout user={auth.user} title="Detail Penerima Bantuan">
            <Head title={`Detail Penerima: ${penerima.penduduk?.nama}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* Header */}
                <PageHeader 
                    title={penerima.penduduk?.nama ?? 'Detail Penerima'}
                    subtitle={bantuanSosial.nama_program}
                    icon={Users}
                    backHref={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                    actions={[
                        {
                            label: 'EDIT',
                            icon: Edit,
                            href: route('bantuan-sosial.penerima.edit', [bantuanSosial.id, penerima.id]),
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
                    {/* Data Penerima */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* Info Warga */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Penerima</h3>
                            </div>
                            <div className="p-6">
                                <div className="mb-6">
                                    <Badge color={statusCfg.color} icon={StatusIcon}>
                                        {statusCfg.label}
                                    </Badge>
                                </div>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <InfoRow label="Nama Lengkap" value={penerima.penduduk?.nama} icon={User} color="blue" />
                                    <InfoRow label="NIK" value={penerima.penduduk?.nik} icon={Shield} color="purple" />
                                    <InfoRow label="Alamat" value={penerima.penduduk?.alamat} icon={MapPin} color="orange" />
                                    {penerima.keterangan && <InfoRow label="Keterangan" value={penerima.keterangan} icon={FileText} color="gray" />}
                                </div>
                            </div>
                        </div>

                        {/* Detail Penerimaan */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Penerimaan</h3>
                            </div>
                            <div className="p-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <InfoRow
                                        label="Sistem Pembayaran"
                                        value={isBerkala ? 'Berkala (4 Tahap per Tahun)' : 'Sekali Bayar'}
                                        icon={Calendar} color="blue"
                                    />
                                    <InfoRow
                                        label="Total Nilai Bantuan"
                                        value={fmtRp(penerima.nilai_diterima)}
                                        icon={DollarSign} color="green"
                                    />

                                    {!isBerkala && (
                                        <InfoRow label="Tanggal Penerimaan" value={fmt(penerima.tanggal_penerimaan)} icon={Calendar} color="blue" />
                                    )}
                                </div>

                                {isBerkala && (
                                    <div className="mt-6 space-y-3">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest pl-2 border-l-4 border-blue-500">Jadwal Penyaluran Berkala</p>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            {[1, 2, 3, 4].map((q) => {
                                                const tw = dataTambahan?.[`tahap_${q}`] || dataTambahan?.[`triwulan_${q}`];
                                                return tw ? (
                                                    <div key={q} className="flex items-center justify-between p-4 bg-blue-50/50 border border-blue-100 rounded-2xl hover:bg-white hover:shadow-md transition-all">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center border border-blue-200">
                                                                <span className="text-xs font-black text-blue-700">T{q}</span>
                                                            </div>
                                                            <div>
                                                                <p className="text-xs font-black text-blue-900">Tahap {q}</p>
                                                                <p className="text-[10px] font-bold text-blue-500 mt-1 uppercase tracking-widest">{fmt(tw.tanggal)}</p>
                                                            </div>
                                                        </div>
                                                        <span className="text-sm font-black text-blue-800 italic">{fmtRp(tw.jumlah)}</span>
                                                    </div>
                                                ) : null;
                                            })}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Info Program Sidebar */}
                    <div>
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Info Program</h3>
                            </div>
                            <div className="p-5 space-y-4">
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Nama Program</p>
                                    <p className="text-sm font-bold text-gray-900">{bantuanSosial.nama_program}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Jenis</p>
                                    <p className="text-sm font-bold text-gray-900">{bantuanSosial.jenis_bantuan}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Periode</p>
                                    <p className="text-sm font-bold text-gray-900">{bantuanSosial.periode}</p>
                                </div>
                                <div className="pt-4 border-t border-gray-100">
                                    <Link
                                        href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                                        className="w-full flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-600 text-green-700 hover:text-white border border-green-200 hover:border-transparent rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                                    >
                                        <Users className="w-4 h-4 mr-2" />
                                        SEMUA PENERIMA
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
