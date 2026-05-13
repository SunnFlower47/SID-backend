import React from 'react';
import { Link, router } from '@inertiajs/react';
import { Building2, Edit2, Trash2, TrendingUp, MapPin, Calendar, User, ChevronRight, Clock, CheckCircle2, AlertCircle, XCircle, PauseCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';

const formatRupiah = (v) => {
    if (!v && v !== 0) return 'Rp 0';
    if (v >= 1_000_000_000) return `Rp ${(v / 1_000_000_000).toFixed(2)} M`;
    if (v >= 1_000_000) return `Rp ${(v / 1_000_000).toFixed(1)} Jt`;
    return `Rp ${Number(v).toLocaleString('id-ID')}`;
};

const STATUS_CONFIG = {
    perencanaan: { icon: Clock,          color: 'text-blue-700',   bg: 'bg-blue-50',   border: 'border-blue-100',   bar: 'bg-blue-400'   },
    pelaksanaan:  { icon: AlertCircle,    color: 'text-yellow-700', bg: 'bg-yellow-50', border: 'border-yellow-100', bar: 'bg-yellow-400' },
    selesai:      { icon: CheckCircle2,   color: 'text-green-700',  bg: 'bg-green-50',  border: 'border-green-100',  bar: 'bg-green-500'  },
    tertunda:     { icon: PauseCircle,    color: 'text-gray-600',   bg: 'bg-gray-50',   border: 'border-gray-100',   bar: 'bg-gray-400'   },
    dibatalkan:   { icon: XCircle,        color: 'text-red-700',    bg: 'bg-red-50',    border: 'border-red-100',    bar: 'bg-red-400'    },
};

const JENIS_CONFIG = {
    infrastruktur: { color: 'text-indigo-700', bg: 'bg-indigo-50' },
    sosial:        { color: 'text-pink-700',   bg: 'bg-pink-50'   },
    ekonomi:       { color: 'text-emerald-700',bg: 'bg-emerald-50'},
    lingkungan:    { color: 'text-teal-700',   bg: 'bg-teal-50'   },
    lainnya:       { color: 'text-gray-700',   bg: 'bg-gray-50'   },
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '-';

export default function ProyekCard({ proyek, onUpdateRealisasi }) {
    const statusCfg = STATUS_CONFIG[proyek.status] ?? STATUS_CONFIG.perencanaan;
    const jenisCfg  = JENIS_CONFIG[proyek.jenis]   ?? JENIS_CONFIG.lainnya;
    const StatusIcon = statusCfg.icon;

    const pct = proyek.anggaran > 0
        ? Math.min(100, Math.round((proyek.realisasi / proyek.anggaran) * 100))
        : 0;

    const handleDelete = () => {
        Swal.fire({
            title: 'HAPUS PROYEK?',
            html: `Hapus proyek <b class="text-red-600">${proyek.nama_proyek}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">Anggaran APBDes terkait akan dikembalikan</small>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!', cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px]',
                cancelButton:  'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px] text-gray-500',
            },
        }).then(r => {
            if (r.isConfirmed) {
                router.delete(route('transparansi-desa.proyek'), { data: { id: proyek.id }, preserveScroll: true });
            }
        });
    };

    return (
        <div className={cn(
            'bg-white rounded-2xl border shadow-sm hover:shadow-md transition-all group flex flex-col',
            statusCfg.border
        )}>
            {/* Card Header */}
            <div className={cn('p-4 rounded-t-2xl flex items-start justify-between gap-3', statusCfg.bg)}>
                <div className="flex-1 min-w-0">
                    <h3 className="text-xs font-black text-gray-900 leading-tight line-clamp-2 uppercase italic tracking-tighter">{proyek.nama_proyek}</h3>
                    <p className="text-[9px] font-bold text-gray-500 uppercase tracking-widest mt-1 flex items-center gap-1">
                        <MapPin className="w-2.5 h-2.5" /> {proyek.lokasi ?? '-'}
                    </p>
                </div>
                <div className={cn('flex items-center gap-1.5 shrink-0 px-2.5 py-1 rounded-full text-[8px] font-black uppercase tracking-widest', statusCfg.bg, statusCfg.color, `border ${statusCfg.border}`)}>
                    <StatusIcon className="w-2.5 h-2.5" />
                    {proyek.status}
                </div>
            </div>

            {/* Progress */}
            <div className="px-4 pt-4 pb-3 space-y-2">
                <div className="flex justify-between items-center">
                    <span className={cn('text-[9px] font-black uppercase tracking-widest', statusCfg.color)}>{pct}% Selesai</span>
                    <span className="text-[9px] font-bold text-gray-400">Progress</span>
                </div>
                <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div className={cn('h-full rounded-full transition-all duration-700', statusCfg.bar)} style={{ width: `${pct}%` }} />
                </div>
            </div>

            {/* Budget Info */}
            <div className="px-4 pb-3 grid grid-cols-2 gap-2">
                <div className="bg-gray-50 rounded-xl p-2.5">
                    <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Anggaran</p>
                    <p className="text-[10px] font-black text-gray-900 leading-none">{formatRupiah(proyek.anggaran)}</p>
                </div>
                <div className="bg-gray-50 rounded-xl p-2.5">
                    <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest leading-none mb-0.5">Realisasi</p>
                    <p className="text-[10px] font-black text-blue-600 leading-none">{formatRupiah(proyek.realisasi)}</p>
                </div>
            </div>

            {/* Meta */}
            <div className="px-4 pb-3 space-y-1.5">
                <div className="flex items-center gap-2">
                    <User className="w-3 h-3 text-gray-300 shrink-0" />
                    <span className="text-[9px] font-bold text-gray-500 uppercase tracking-wider truncate">{proyek.penanggung_jawab ?? '-'}</span>
                </div>
                <div className="flex items-center gap-2">
                    <Calendar className="w-3 h-3 text-gray-300 shrink-0" />
                    <span className="text-[9px] font-bold text-gray-500 uppercase tracking-wider">{formatDate(proyek.tanggal_mulai)}</span>
                    {proyek.tanggal_selesai && (
                        <>
                            <span className="text-gray-300">→</span>
                            <span className="text-[9px] font-bold text-gray-500 uppercase tracking-wider">{formatDate(proyek.tanggal_selesai)}</span>
                        </>
                    )}
                </div>
                {proyek.jenis && (
                    <span className={cn('inline-flex px-2 py-0.5 rounded-full text-[8px] font-black uppercase tracking-widest', jenisCfg.bg, jenisCfg.color)}>
                        {proyek.jenis}
                    </span>
                )}
            </div>

            {/* APBDes Link */}
            {proyek.apbdes && (
                <div className="px-4 pb-3">
                    <div className="p-2 bg-green-50 border border-green-100 rounded-xl">
                        <p className="text-[8px] font-black text-green-500 uppercase tracking-widest leading-none mb-0.5">Rekening APBDes</p>
                        <p className="text-[9px] font-black text-green-800 truncate">[{proyek.apbdes.kode_rekening}] {proyek.apbdes.nama_rekening}</p>
                    </div>
                </div>
            )}

            {/* Actions */}
            <div className="mt-auto p-3 pt-0 flex items-center gap-2">
                {proyek.status !== 'selesai' && proyek.status !== 'dibatalkan' && (
                    <button
                        onClick={() => onUpdateRealisasi(proyek)}
                        className="flex-1 flex items-center justify-center gap-2 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all hover:scale-[1.02] active:scale-[0.98] shadow-md shadow-green-200"
                    >
                        <TrendingUp className="w-3 h-3" />
                        UPDATE REALISASI
                    </button>
                )}
                {proyek.status === 'selesai' && (
                    <div className="flex-1 flex items-center justify-center gap-1.5 py-2.5 bg-green-50 border border-green-100 rounded-xl text-[9px] font-black text-green-600 uppercase tracking-widest">
                        <CheckCircle2 className="w-3 h-3" />
                        SELESAI
                    </div>
                )}
                <button onClick={handleDelete} className="p-2.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                    <Trash2 className="w-3.5 h-3.5" />
                </button>
            </div>
        </div>
    );
}
