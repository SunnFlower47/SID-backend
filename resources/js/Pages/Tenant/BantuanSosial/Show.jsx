import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { HandHeart, Edit, Trash2, Users, ArrowLeft, Calendar, DollarSign, Building2, CheckCircle, Clock, XCircle } from 'lucide-react';
import Swal from 'sweetalert2';

// ── Helpers ────────────────────────────────────────────────────
const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        cls: 'bg-green-100 text-green-800', icon: CheckCircle },
    selesai:      { label: 'Selesai',      cls: 'bg-gray-100 text-gray-700',   icon: Clock },
    ditangguhkan: { label: 'Ditangguhkan', cls: 'bg-yellow-100 text-yellow-800', icon: XCircle },
};

const JENIS_CONFIG = {
    BLT:           'bg-blue-100 text-blue-800',
    PKH:           'bg-purple-100 text-purple-800',
    BPNT:          'bg-teal-100 text-teal-800',
    'Bansos Lainnya': 'bg-orange-100 text-orange-800',
};

function InfoRow({ label, value, highlight }) {
    return (
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 border-b border-gray-50 last:border-b-0">
            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 sm:mb-0">{label}</span>
            <span className={`text-sm font-bold ${highlight ?? 'text-gray-900'}`}>{value ?? '—'}</span>
        </div>
    );
}

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
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <HandHeart className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    {bantuanSosial.nama_program}
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Detail Program Bantuan Sosial
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('bantuan-sosial.index')}
                                className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                            <Link
                                href={route('bantuan-sosial.edit', bantuanSosial.id)}
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
                    {/* ── Left: Detail Info ── */}
                    <div className="lg:col-span-2 space-y-5">
                        {/* Info Utama */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Informasi Program</h3>
                            </div>
                            <div className="p-6">
                                <div className="mb-4 flex flex-wrap gap-2">
                                    <span className={`inline-flex items-center px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${JENIS_CONFIG[bantuanSosial.jenis_bantuan] ?? 'bg-gray-100 text-gray-700'}`}>
                                        {bantuanSosial.jenis_bantuan}
                                    </span>
                                    <span className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest ${statusCfg.cls}`}>
                                        <StatusIcon className="w-3 h-3" />
                                        {statusCfg.label}
                                    </span>
                                </div>

                                <p className="text-sm text-gray-600 leading-relaxed mb-5">{bantuanSosial.deskripsi}</p>

                                <InfoRow label="Periode" value={bantuanSosial.periode} />
                                <InfoRow label="Sumber Dana" value={bantuanSosial.sumber_dana} />
                                <InfoRow
                                    label="Tanggal Mulai"
                                    value={bantuanSosial.tanggal_mulai
                                        ? new Date(bantuanSosial.tanggal_mulai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                        : null}
                                />
                                <InfoRow
                                    label="Tanggal Selesai"
                                    value={bantuanSosial.tanggal_selesai
                                        ? new Date(bantuanSosial.tanggal_selesai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
                                        : null}
                                />
                                <InfoRow
                                    label="Nilai Bantuan"
                                    value={bantuanSosial.nilai_bantuan
                                        ? `Rp ${Number(bantuanSosial.nilai_bantuan).toLocaleString('id-ID')}`
                                        : null}
                                    highlight="text-green-700"
                                />
                                <InfoRow
                                    label="Kuota Penerima"
                                    value={bantuanSosial.kuota_penerima
                                        ? `${Number(bantuanSosial.kuota_penerima).toLocaleString('id-ID')} orang`
                                        : 'Tidak terbatas'}
                                />
                            </div>
                        </div>

                        {/* Kriteria Penerima */}
                        {kriteria.length > 0 && (
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                                <div className="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Kriteria Penerima</h3>
                                </div>
                                <div className="p-6">
                                    <ul className="space-y-2">
                                        {kriteria.map((k, i) => (
                                            <li key={i} className="flex items-start gap-3 text-sm text-gray-700">
                                                <span className="w-5 h-5 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-[10px] font-black shrink-0 mt-0.5">
                                                    {i + 1}
                                                </span>
                                                {k}
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
                                <div className="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                                    <Users className="w-8 h-8 text-purple-600" />
                                </div>
                                <p className="text-4xl font-black text-gray-900">{bantuanSosial.penerima_count ?? 0}</p>
                                <p className="text-xs font-black text-gray-400 uppercase tracking-widest mt-1">Total Penerima</p>
                                {bantuanSosial.kuota_penerima && (
                                    <div className="mt-4">
                                        <div className="w-full bg-gray-100 rounded-full h-2">
                                            <div
                                                className="bg-green-500 h-2 rounded-full transition-all"
                                                style={{ width: `${Math.min((bantuanSosial.penerima_count / bantuanSosial.kuota_penerima) * 100, 100)}%` }}
                                            />
                                        </div>
                                        <p className="text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-1.5">
                                            {bantuanSosial.penerima_count} / {bantuanSosial.kuota_penerima} kuota
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* CTA Kelola Penerima */}
                        <Link
                            href={route('bantuan-sosial.penerima.index', bantuanSosial.id)}
                            className="flex flex-col items-center justify-center gap-3 p-6 bg-gradient-to-br from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-2xl shadow-lg shadow-green-200 transition-all hover:scale-[1.02] text-center"
                        >
                            <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <Users className="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <p className="font-black text-sm uppercase tracking-widest">KELOLA PENERIMA</p>
                                <p className="text-green-100 text-[10px] font-bold uppercase tracking-wider mt-0.5">Tambah &amp; kelola data penerima</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
