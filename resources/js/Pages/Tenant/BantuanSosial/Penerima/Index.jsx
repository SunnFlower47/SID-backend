import React from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Shared/Pagination';
import { HandHeart, Users, Plus, Eye, Edit, Trash2, ArrowLeft, CheckCircle, Clock, XCircle } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

const STATUS_CONFIG = {
    aktif:        { label: 'Aktif',        cls: 'bg-green-100 text-green-800',  icon: CheckCircle },
    ditangguhkan: { label: 'Ditangguhkan', cls: 'bg-yellow-100 text-yellow-800', icon: Clock },
    berhenti:     { label: 'Berhenti',     cls: 'bg-red-100 text-red-800',      icon: XCircle },
};

function StatusBadge({ status }) {
    const cfg = STATUS_CONFIG[status] ?? { label: status, cls: 'bg-gray-100 text-gray-700', icon: Clock };
    const Icon = cfg.icon;
    return (
        <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${cfg.cls}`}>
            <Icon className="w-3 h-3" />
            {cfg.label}
        </span>
    );
}

export default function Index({ auth, bantuanSosial, penerima }) {
    const isLocked = bantuanSosial.status === 'selesai' || bantuanSosial.is_expired;

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus penerima <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('bantuan-sosial.penerima.destroy', [bantuanSosial.id, id]), {
                    preserveScroll: true,
                });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Daftar Penerima Bantuan">
            <Head title={`Penerima: ${bantuanSosial.nama_program}`} />

            <div className="space-y-5 animate-in fade-in duration-700 pb-20">

                {/* ── Header ── */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div className="flex items-center gap-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Users className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-2xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Daftar Penerima
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    {bantuanSosial.nama_program}
                                </p>
                            </div>
                        </div>
                        <div className="flex flex-wrap gap-2">
                            <Link
                                href={route('bantuan-sosial.show', bantuanSosial.id)}
                                className="flex items-center px-4 py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            >
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" />
                                KEMBALI
                            </Link>
                            {!isLocked && (
                                <Link
                                    href={route('bantuan-sosial.penerima.create', bantuanSosial.id)}
                                    className="flex items-center px-5 py-2.5 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                                >
                                    <Plus className="w-3.5 h-3.5 mr-2" />
                                    TAMBAH PENERIMA
                                </Link>
                            )}
                        </div>
                    </div>
                </div>

                {/* ── Locked Alert ── */}
                {isLocked && (
                    <div className="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center gap-4 animate-in slide-in-from-top duration-300">
                        <div className="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center shrink-0">
                            <Clock className="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <p className="text-[10px] font-black text-amber-800 uppercase tracking-widest">Program Telah Selesai</p>
                            <p className="text-xs font-bold text-amber-700/80">Data penerima telah dikunci dan tidak dapat diubah lagi untuk menjaga integritas laporan.</p>
                        </div>
                    </div>
                )}

                {/* ── Table ── */}
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 className="text-base sm:text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                            <Users className="w-5 h-5 text-green-600" />
                            Daftar Penerima
                        </h3>
                        <span className="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                            Total: {penerima?.total ?? 0}
                        </span>
                    </div>

                    {penerima?.data?.length > 0 ? (
                        <>
                            {/* Desktop Table */}
                            <div className="hidden lg:block overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th className="px-6 py-4">Penerima</th>
                                            <th className="px-6 py-4">NIK</th>
                                            <th className="px-6 py-4">Sistem</th>
                                            <th className="px-6 py-4">Nilai Diterima</th>
                                            <th className="px-6 py-4">Tanggal</th>
                                            <th className="px-6 py-4">Status</th>
                                            <th className="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {penerima.data.map((p) => {
                                            const dataTambahan = typeof p.data_tambahan === 'string'
                                                ? JSON.parse(p.data_tambahan || '{}')
                                                : (p.data_tambahan ?? {});
                                            const isBerkala = dataTambahan?.sistem_pembayaran === 'berkala' || dataTambahan?.sistem_pembayaran === 'triwulanan';

                                            return (
                                                <tr key={p.id} className="hover:bg-green-50/20 transition-colors">
                                                    <td className="px-6 py-4">
                                                        <p className="font-bold text-gray-900">{p.penduduk?.nama ?? '—'}</p>
                                                        <p className="text-xs text-gray-400">{p.penduduk?.alamat}</p>
                                                    </td>
                                                    <td className="px-6 py-4 font-mono text-xs">{p.penduduk?.nik}</td>
                                                    <td className="px-6 py-4">
                                                        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${isBerkala ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700'}`}>
                                                            {isBerkala ? 'Berkala' : 'Sekali'}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 font-bold text-green-700">
                                                        Rp {Number(p.nilai_diterima).toLocaleString('id-ID')}
                                                    </td>
                                                    <td className="px-6 py-4 text-xs">
                                                        {p.tanggal_penerimaan
                                                            ? new Date(p.tanggal_penerimaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
                                                            : '—'}
                                                    </td>
                                                    <td className="px-6 py-4"><StatusBadge status={p.status_penerimaan} /></td>
                                                    <td className="px-6 py-4">
                                                        <div className="flex justify-end gap-2">
                                                            <Link
                                                                href={route('bantuan-sosial.penerima.show', [bantuanSosial.id, p.id])}
                                                                className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                            >
                                                                <Eye className="w-4 h-4" />
                                                            </Link>
                                                            {!isLocked && (
                                                                <>
                                                                    <Link
                                                                        href={route('bantuan-sosial.penerima.edit', [bantuanSosial.id, p.id])}
                                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                                                                    >
                                                                        <Edit className="w-4 h-4" />
                                                                    </Link>
                                                                    <button
                                                                        onClick={() => handleDelete(p.id, p.penduduk?.nama)}
                                                                        className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                                    >
                                                                        <Trash2 className="w-4 h-4" />
                                                                    </button>
                                                                </>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile Cards */}
                            <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                {penerima.data.map((p) => {
                                    const dataTambahan = typeof p.data_tambahan === 'string'
                                        ? JSON.parse(p.data_tambahan || '{}')
                                        : (p.data_tambahan ?? {});
                                    const isBerkala = dataTambahan?.sistem_pembayaran === 'berkala' || dataTambahan?.sistem_pembayaran === 'triwulanan';

                                    return (
                                        <div key={p.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                            <div className="flex items-start gap-3 mb-3">
                                                <div className="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center shrink-0">
                                                    <Users className="w-5 h-5 text-purple-600" />
                                                </div>
                                                <div className="flex-1 min-w-0">
                                                    <h4 className="font-black text-gray-900 truncate">{p.penduduk?.nama}</h4>
                                                    <p className="text-[10px] font-mono text-gray-400 mt-0.5">{p.penduduk?.nik}</p>
                                                </div>
                                                <StatusBadge status={p.status_penerimaan} />
                                            </div>
                                            <div className="grid grid-cols-2 gap-2 mb-3">
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nilai</p>
                                                    <p className="text-xs font-bold text-green-700">Rp {Number(p.nilai_diterima).toLocaleString('id-ID')}</p>
                                                </div>
                                                <div className="bg-gray-50 rounded-xl p-3">
                                                    <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Sistem</p>
                                                    <p className="text-xs font-bold text-gray-900">{isBerkala ? 'Berkala' : 'Sekali'}</p>
                                                </div>
                                            </div>
                                            <div className="flex gap-2">
                                                <Link href={route('bantuan-sosial.penerima.show', [bantuanSosial.id, p.id])} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                    DETAIL
                                                </Link>
                                                {!isLocked && (
                                                    <>
                                                        <Link href={route('bantuan-sosial.penerima.edit', [bantuanSosial.id, p.id])} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                            EDIT
                                                        </Link>
                                                        <button onClick={() => handleDelete(p.id, p.penduduk?.nama)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <div className="w-56 h-56 mx-auto mb-4">
                                <LottieComponent animationData={noDataAnimation} loop />
                            </div>
                            <h3 className="text-xl font-black text-gray-900">Belum Ada Penerima</h3>
                            <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto">
                                Tambahkan data warga yang menerima bantuan dari program ini.
                            </p>
                            <Link
                                href={route('bantuan-sosial.penerima.create', bantuanSosial.id)}
                                className="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-200 hover:bg-green-700 transition-all mt-6"
                            >
                                <Plus className="w-4 h-4 mr-2" />
                                TAMBAH PENERIMA SEKARANG
                            </Link>
                        </div>
                    )}

                    <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                        <Pagination links={penerima?.links} from={penerima?.from} to={penerima?.to} total={penerima?.total} />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
