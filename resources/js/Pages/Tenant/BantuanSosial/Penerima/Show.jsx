import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Users, ArrowLeft, Edit, Trash2, CheckCircle, Clock, XCircle, Calendar, DollarSign } from 'lucide-react';
import Swal from 'sweetalert2';

const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        cls: 'bg-green-100 text-green-800',  icon: CheckCircle },
    ditangguhkan: { label: 'Ditangguhkan', cls: 'bg-yellow-100 text-yellow-800', icon: Clock },
    berhenti:     { label: 'Berhenti',     cls: 'bg-red-100 text-red-800',      icon: XCircle },
};

function InfoRow({ label, value, highlight }) {
    return (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-50 last:border-b-0">
            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-0">{label}</span>
            <span className={`text-sm font-bold ${highlight ?? 'text-gray-900'}`}>{value ?? '—'}</span>
        </div>
    );
}

export default function Show({ auth, bantuanSosial, penerima }) {
    const statusCfg = STATUS_CONFIG[penerima.status_penerimaan] ?? STATUS_CONFIG.ditangguhkan;
    const StatusIcon = statusCfg.icon;

    const dataTambahan = (() => {
        try { return typeof penerima.data_tambahan === 'string' ? JSON.parse(penerima.data_tambahan || '{}') : (penerima.data_tambahan ?? {}); }
        catch { return {}; }
    })();
    const isTriwulanan = dataTambahan?.sistem_pembayaran === 'triwulanan';

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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Users className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    {penerima.penduduk?.nama ?? 'Detail Penerima'}
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    {bantuanSosial.nama_program}
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                                className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                            <Link
                                href={route('bantuan-sosial.penerima.edit', [bantuanSosial.id, penerima.id])}
                                className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <Edit className="w-3.5 h-3.5 mr-2" />
                                EDIT
                            </Link>
                            <button
                                onClick={handleDelete}
                                className="flex items-center px-4 py-2.5 bg-red-500/30 hover:bg-red-500/50 backdrop-blur-md border border-red-400/30 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <Trash2 className="w-3.5 h-3.5 mr-2" />
                                HAPUS
                            </button>
                        </div>
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    {/* Data Penerima */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* Info Warga */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Data Penerima</h3>
                            </div>
                            <div className="p-6">
                                <div className="mb-4">
                                    <span className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${statusCfg.cls}`}>
                                        <StatusIcon className="w-3 h-3" />
                                        {statusCfg.label}
                                    </span>
                                </div>
                                <InfoRow label="Nama Lengkap" value={penerima.penduduk?.nama} />
                                <InfoRow label="NIK" value={penerima.penduduk?.nik} />
                                <InfoRow label="Alamat" value={penerima.penduduk?.alamat} />
                                {penerima.keterangan && <InfoRow label="Keterangan" value={penerima.keterangan} />}
                            </div>
                        </div>

                        {/* Detail Penerimaan */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Penerimaan</h3>
                            </div>
                            <div className="p-6">
                                <InfoRow
                                    label="Sistem Pembayaran"
                                    value={isBerkala ? 'Berkala (4 Tahap per Tahun)' : 'Sekali Bayar'}
                                />
                                <InfoRow
                                    label="Total Nilai Bantuan"
                                    value={fmtRp(penerima.nilai_diterima)}
                                    highlight="text-green-700"
                                />

                                {!isBerkala && (
                                    <InfoRow label="Tanggal Penerimaan" value={fmt(penerima.tanggal_penerimaan)} />
                                )}

                                {isBerkala && (
                                    <div className="mt-4 space-y-3">
                                        <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jadwal Penyaluran Berkala</p>
                                        {[1, 2, 3, 4].map((q) => {
                                            const tw = dataTambahan?.[`tahap_${q}`] || dataTambahan?.[`triwulan_${q}`];
                                            return tw ? (
                                                <div key={q} className="flex items-center justify-between p-3 bg-blue-50 border border-blue-200 rounded-xl">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                                            <span className="text-[10px] font-black text-blue-700">T{q}</span>
                                                        </div>
                                                        <div>
                                                            <p className="text-xs font-bold text-blue-900">Tahap {q}</p>
                                                            <p className="text-[10px] font-bold text-blue-500">{fmt(tw.tanggal)}</p>
                                                        </div>
                                                    </div>
                                                    <span className="text-sm font-black text-blue-800">{fmtRp(tw.jumlah)}</span>
                                                </div>
                                            ) : null;
                                        })}
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
                            <div className="p-5 space-y-3">
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nama Program</p>
                                    <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.nama_program}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis</p>
                                    <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.jenis_bantuan}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Periode</p>
                                    <p className="text-sm font-bold text-gray-900 mt-1">{bantuanSosial.periode}</p>
                                </div>
                                <div className="pt-3 border-t border-gray-100">
                                    <Link
                                        href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                                        className="w-full flex items-center justify-center px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl text-xs font-black uppercase tracking-widest transition-all"
                                    >
                                        <Users className="w-3.5 h-3.5 mr-2" />
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
