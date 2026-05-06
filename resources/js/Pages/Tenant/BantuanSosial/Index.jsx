import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BansosStats from '@/Components/BantuanSosial/BansosStats';
import BansosFilters from '@/Components/BantuanSosial/BansosFilters';
import Pagination from '@/Components/Shared/Pagination';
import { HandHeart, Plus, Eye, Edit, Trash2, Filter, Users } from 'lucide-react';
import Swal from 'sweetalert2';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

// ────────────────────────────────────────────────────────────────
// Badge helpers
// ────────────────────────────────────────────────────────────────
const STATUS_BADGE = {
    aktif:        'bg-green-100 text-green-800',
    selesai:      'bg-gray-100 text-gray-700',
    ditangguhkan: 'bg-yellow-100 text-yellow-800',
};

const JENIS_BADGE = {
    BLT:           'bg-blue-100 text-blue-800',
    PKH:           'bg-purple-100 text-purple-800',
    BPNT:          'bg-teal-100 text-teal-800',
    'Bansos Lainnya': 'bg-orange-100 text-orange-800',
};

function StatusBadge({ status }) {
    const label = { aktif: 'Aktif', selesai: 'Selesai', ditangguhkan: 'Ditangguhkan' }[status] ?? status;
    return (
        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${STATUS_BADGE[status] ?? 'bg-gray-100 text-gray-700'}`}>
            {label}
        </span>
    );
}

function JenisBadge({ jenis }) {
    return (
        <span className={`inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${JENIS_BADGE[jenis] ?? 'bg-gray-100 text-gray-700'}`}>
            {jenis}
        </span>
    );
}

// ────────────────────────────────────────────────────────────────
// Main Page
// ────────────────────────────────────────────────────────────────
export default function Index({ auth, bantuanSosials, stats, filters }) {
    const [showFilters, setShowFilters] = useState(
        !!(filters?.search || filters?.status || filters?.jenis_bantuan || filters?.tahun)
    );

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'KONFIRMASI HAPUS',
            html: `Hapus program <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Tindakan ini tidak dapat dibatalkan</small>`,
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
                router.delete(route('bantuan-sosial.destroy', id), { preserveScroll: true });
            }
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Bantuan Sosial">
            <Head title="Bantuan Sosial" />

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
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">
                                    Bantuan Sosial
                                </h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80">
                                    Kelola program bantuan sosial desa
                                </p>
                            </div>
                        </div>
                        <Link
                            href={route('bantuan-sosial.create')}
                            className="flex items-center px-6 py-3 bg-white text-green-700 hover:bg-green-50 rounded-xl text-[10px] sm:text-xs font-black shadow-lg shadow-black/10 transition-all hover:scale-105 uppercase tracking-widest"
                        >
                            <Plus className="w-3.5 h-3.5 mr-2" />
                            TAMBAH PROGRAM
                        </Link>
                    </div>
                </div>

                {/* ── Stats ── */}
                <BansosStats stats={stats} />

                {/* ── Filter Toggle ── */}
                <div className="flex justify-between items-center bg-white p-3 sm:p-5 rounded-2xl sm:rounded-3xl border border-gray-100 shadow-sm">
                    <div className="flex items-center gap-2 sm:gap-4">
                        <div className="w-8 h-8 sm:w-12 sm:h-12 bg-green-50 rounded-xl flex items-center justify-center">
                            <HandHeart className="w-4 h-4 sm:w-6 sm:h-6 text-green-600" />
                        </div>
                        <div>
                            <h3 className="text-[10px] sm:text-sm font-black text-gray-950 uppercase italic tracking-tighter">
                                Konfigurasi Data
                            </h3>
                            <p className="hidden sm:block text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Pencarian &amp; Filter Program
                            </p>
                        </div>
                    </div>
                    <button
                        onClick={() => setShowFilters(!showFilters)}
                        className={`flex items-center px-4 py-2 sm:px-6 sm:py-3 rounded-xl text-[9px] sm:text-xs font-black transition-all border shadow-sm active:scale-95 ${
                            showFilters
                                ? 'bg-yellow-400 text-yellow-900 border-yellow-500 shadow-yellow-400/20'
                                : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'
                        }`}
                    >
                        <Filter className="w-3 h-3 sm:w-4 sm:h-4 mr-2" />
                        {showFilters ? 'TUTUP PANEL' : 'BUKA FILTER'}
                    </button>
                </div>

                {/* ── Filters ── */}
                {showFilters && (
                    <div className="animate-in slide-in-from-top duration-300">
                        <BansosFilters filters={filters} />
                    </div>
                )}

                {/* ── Table ── */}
                <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div className="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
                        <h3 className="text-base sm:text-lg font-black text-gray-900 flex items-center gap-3 uppercase italic tracking-tighter">
                            <HandHeart className="w-5 h-5 text-green-600" />
                            Daftar Program Bansos
                        </h3>
                        <span className="px-3 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                            Total: {bantuanSosials?.total ?? 0}
                        </span>
                    </div>

                    {bantuanSosials?.data?.length > 0 ? (
                        <>
                            {/* ── Desktop Table ── */}
                            <div className="hidden lg:block overflow-x-auto">
                                <table className="w-full text-left text-sm text-gray-600">
                                    <thead className="bg-gray-50/50 text-gray-900 font-bold uppercase text-xs tracking-wider border-b border-gray-100">
                                        <tr>
                                            <th className="px-6 py-4">Nama Program</th>
                                            <th className="px-6 py-4">Jenis</th>
                                            <th className="px-6 py-4">Periode</th>
                                            <th className="px-6 py-4">Nilai Bantuan</th>
                                            <th className="px-6 py-4">Penerima</th>
                                            <th className="px-6 py-4">Status</th>
                                            <th className="px-6 py-4 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-50">
                                        {bantuanSosials.data.map((b) => (
                                            <tr key={b.id} className="hover:bg-green-50/20 transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                                            <HandHeart className="w-4 h-4 text-green-600" />
                                                        </div>
                                                        <div>
                                                            <p className="font-bold text-gray-900 leading-tight">{b.nama_program}</p>
                                                            <p className="text-xs text-gray-400 truncate max-w-[200px]">{b.deskripsi}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4"><JenisBadge jenis={b.jenis_bantuan} /></td>
                                                <td className="px-6 py-4 font-medium">{b.periode}</td>
                                                <td className="px-6 py-4">
                                                    {b.nilai_bantuan ? (
                                                        <span className="font-bold text-green-700">
                                                            Rp {Number(b.nilai_bantuan).toLocaleString('id-ID')}
                                                        </span>
                                                    ) : (
                                                        <span className="text-gray-400">—</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-[10px] font-black uppercase tracking-widest">
                                                        <Users className="w-3 h-3" />
                                                        {b.penerima_count} orang
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4"><StatusBadge status={b.status} /></td>
                                                <td className="px-6 py-4">
                                                    <div className="flex justify-end gap-2">
                                                        <Link
                                                            href={route('bantuan-sosial.show', b.id)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors"
                                                            title="Lihat detail"
                                                        >
                                                            <Eye className="w-4 h-4" />
                                                        </Link>
                                                        <Link
                                                            href={route('bantuan-sosial.edit', b.id)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-colors"
                                                            title="Edit"
                                                        >
                                                            <Edit className="w-4 h-4" />
                                                        </Link>
                                                        <button
                                                            onClick={() => handleDelete(b.id, b.nama_program)}
                                                            className="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-colors"
                                                            title="Hapus"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* ── Mobile Cards ── */}
                            <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                {bantuanSosials.data.map((b) => (
                                    <div key={b.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                                        <div className="flex items-start gap-3 mb-3">
                                            <div className="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                                                <HandHeart className="w-5 h-5 text-green-600" />
                                            </div>
                                            <div className="flex-1 min-w-0">
                                                <h4 className="font-black text-gray-900 truncate">{b.nama_program}</h4>
                                                <div className="flex flex-wrap items-center gap-2 mt-1">
                                                    <JenisBadge jenis={b.jenis_bantuan} />
                                                    <StatusBadge status={b.status} />
                                                </div>
                                            </div>
                                        </div>
                                        <div className="grid grid-cols-2 gap-2 mb-3">
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Periode</p>
                                                <p className="text-xs font-bold text-gray-900">{b.periode}</p>
                                            </div>
                                            <div className="bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Penerima</p>
                                                <p className="text-xs font-bold text-purple-700">{b.penerima_count} orang</p>
                                            </div>
                                            <div className="col-span-2 bg-gray-50 rounded-xl p-3">
                                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nilai Bantuan</p>
                                                <p className="text-sm font-bold text-green-700">
                                                    {b.nilai_bantuan ? `Rp ${Number(b.nilai_bantuan).toLocaleString('id-ID')}` : '—'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <Link href={route('bantuan-sosial.show', b.id)} className="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                DETAIL
                                            </Link>
                                            <Link href={route('bantuan-sosial.edit', b.id)} className="flex-1 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-bold text-center transition-colors">
                                                EDIT
                                            </Link>
                                            <button onClick={() => handleDelete(b.id, b.nama_program)} className="px-4 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-colors">
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    ) : (
                        <div className="p-12 text-center">
                            <div className="w-64 h-64 mx-auto mb-4">
                                <LottieComponent animationData={noDataAnimation} loop />
                            </div>
                            <h3 className="text-xl font-black text-gray-900">Belum Ada Program Bantuan Sosial</h3>
                            <p className="text-sm text-gray-500 mt-2 max-w-xs mx-auto">
                                Mulai tambahkan program bantuan sosial untuk mengelola penerimaan bantuan warga.
                            </p>
                            <Link
                                href={route('bantuan-sosial.create')}
                                className="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-green-200 hover:bg-green-700 transition-all mt-6"
                            >
                                <Plus className="w-4 h-4 mr-2" />
                                TAMBAH PROGRAM SEKARANG
                            </Link>
                        </div>
                    )}

                    <div className="p-4 border-t border-gray-100 bg-gray-50/50">
                        <Pagination
                            links={bantuanSosials?.links}
                            from={bantuanSosials?.from}
                            to={bantuanSosials?.to}
                            total={bantuanSosials?.total}
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
