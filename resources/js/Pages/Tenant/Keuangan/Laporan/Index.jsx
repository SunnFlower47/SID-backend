import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BIDANG_MAP, BIDANG_COLOR } from '@/Constants/keuangan';
import {
    FileText, Download, BarChart3, Layers, BookOpen,
    TrendingUp, ArrowLeft, ChevronDown, Printer, RefreshCw
} from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (v) => {
    const n = Number(v || 0);
    if (n >= 1_000_000_000) return `Rp ${(n / 1_000_000_000).toFixed(1)} M`;
    if (n >= 1_000_000)     return `Rp ${(n / 1_000_000).toFixed(1)} Jt`;
    return `Rp ${n.toLocaleString('id-ID')}`;
};

const JENIS_CONFIG = {
    pendapatan: { color: 'text-emerald-700', bg: 'bg-emerald-50', border: 'border-emerald-100' },
    belanja:    { color: 'text-blue-700',    bg: 'bg-blue-50',    border: 'border-blue-100'    },
    pembiayaan: { color: 'text-purple-700',  bg: 'bg-purple-50',  border: 'border-purple-100'  },
};

const REPORT_CARDS = [
    {
        id: 'realisasi',
        icon: BarChart3,
        iconColor: 'text-green-600',
        iconBg: 'bg-green-50',
        title: 'Laporan Realisasi APBDes',
        subtitle: 'Permendagri 20/2018 — Lampiran VII',
        desc: 'Rekapitulasi anggaran vs realisasi seluruh rekening APBDes, dikelompokkan per Bidang dan Jenis.',
        params: [
            { key: 'jenis', label: 'Filter Jenis', type: 'select', options: [
                { value: '', label: 'Semua Jenis' },
                { value: 'pendapatan', label: 'Pendapatan' },
                { value: 'belanja',    label: 'Belanja'    },
                { value: 'pembiayaan', label: 'Pembiayaan' },
            ]},
        ],
        routeName: 'laporan-keuangan.pdf-realisasi',
        orientation: 'Landscape · A4',
    },
    {
        id: 'buku-kas',
        icon: BookOpen,
        iconColor: 'text-blue-600',
        iconBg: 'bg-blue-50',
        title: 'Buku Kas Umum',
        subtitle: 'Rekapitulasi Pengeluaran per Rekening',
        desc: 'Daftar seluruh transaksi pengeluaran lengkap dengan nomor bukti, jenis bukti, dan status SPJ.',
        params: [],
        routeName: 'laporan-keuangan.pdf-buku-kas',
        orientation: 'Portrait · A4',
    },
    {
        id: 'proyek',
        icon: Layers,
        iconColor: 'text-purple-600',
        iconBg: 'bg-purple-50',
        title: 'Laporan Proyek Desa',
        subtitle: 'Realisasi & Progress Pembangunan',
        desc: 'Daftar seluruh proyek desa dengan progress pembangunan, anggaran, realisasi, dan penanggung jawab.',
        params: [
            { key: 'status', label: 'Filter Status', type: 'select', options: [
                { value: '',         label: 'Semua Status'  },
                { value: 'rencana',  label: 'Rencana'       },
                { value: 'berjalan', label: 'Sedang Berjalan'},
                { value: 'selesai',  label: 'Selesai'       },
                { value: 'tunda',    label: 'Ditunda'       },
            ]},
        ],
        routeName: 'laporan-keuangan.pdf-proyek',
        orientation: 'Landscape · A4',
    },
];

export default function LaporanIndex({ auth, tahunList = [], tahun, summary = [], totalAnggaran, totalRealisasi, persen }) {
    const [selectedTahun, setSelectedTahun] = useState(tahun);
    const [params, setParams] = useState({});

    const handleTahunChange = (t) => {
        setSelectedTahun(Number(t));
        router.get(route('laporan-keuangan.index'), { tahun: t }, { preserveState: true });
    };

    const handleDownload = (card) => {
        const qParams = new URLSearchParams({ tahun: selectedTahun });
        card.params.forEach(p => { if (params[card.id + '_' + p.key]) qParams.set(p.key, params[card.id + '_' + p.key]); });
        window.open(route(card.routeName) + '?' + qParams.toString(), '_blank');
    };

    const updateParam = (cardId, key, val) => setParams(prev => ({ ...prev, [cardId + '_' + key]: val }));

    // Group summary per bidang
    const summaryByBidang = summary.reduce((acc, s) => {
        const b = s.bidang ?? 0;
        if (!acc[b]) acc[b] = { anggaran: 0, realisasi: 0, sisa: 0, items: [] };
        acc[b].anggaran  += Number(s.total_anggaran);
        acc[b].realisasi += Number(s.total_realisasi);
        acc[b].sisa      += Number(s.total_sisa);
        acc[b].items.push(s);
        return acc;
    }, {});

    return (
        <AuthenticatedLayout user={auth.user} title="Laporan Keuangan Desa">
            <Head title="Laporan Keuangan Desa" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <div className="bg-gradient-to-r from-green-600 via-green-700 to-green-800 rounded-3xl shadow-xl p-6 sm:p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 -mt-4 -mr-4 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl pointer-events-none" />
                    <div className="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 bg-white opacity-5 rounded-full blur-3xl pointer-events-none" />
                    <div className="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div className="flex items-center space-x-4">
                            <div className="w-12 h-12 sm:w-14 sm:h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-inner shrink-0">
                                <Printer className="w-6 h-6 sm:w-7 sm:h-7 text-yellow-300" />
                            </div>
                            <div>
                                <h1 className="text-xl sm:text-3xl font-black text-white tracking-tight uppercase italic leading-none">Laporan Keuangan Desa</h1>
                                <p className="text-green-100 font-bold text-[10px] sm:text-xs uppercase tracking-widest mt-1 opacity-80 italic">Sesuai Permendagri No. 20 Tahun 2018</p>
                            </div>
                        </div>
                        <div className="flex flex-wrap items-center gap-3">
                            {/* Tahun Selector */}
                            <div className="relative">
                                <select
                                    value={selectedTahun}
                                    onChange={e => handleTahunChange(e.target.value)}
                                    className="appearance-none pl-4 pr-8 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10 cursor-pointer focus:outline-none focus:ring-2 focus:ring-white/30"
                                >
                                    {(tahunList.length ? tahunList : [tahun]).map(t => (
                                        <option key={t} value={t} className="text-gray-900 bg-white">{t}</option>
                                    ))}
                                </select>
                                <ChevronDown className="absolute right-2.5 top-1/2 -translate-y-1/2 w-3 h-3 text-white pointer-events-none" />
                            </div>
                            <Link href={route('transparansi-desa.index')} className="flex items-center px-4 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] sm:text-xs font-black transition-all uppercase tracking-widest backdrop-blur-md border border-white/10">
                                <ArrowLeft className="w-3.5 h-3.5 mr-2" /> KEMBALI
                            </Link>
                        </div>
                    </div>

                    {/* Summary Pills */}
                    <div className="relative z-10 mt-6 grid grid-cols-3 gap-3">
                        {[
                            { label: 'Total Anggaran', value: formatRupiah(totalAnggaran), sub: `Tahun ${selectedTahun}` },
                            { label: 'Total Realisasi', value: formatRupiah(totalRealisasi), sub: `${persen}% Serapan` },
                            { label: 'Sisa Anggaran', value: formatRupiah(Number(totalAnggaran) - Number(totalRealisasi)), sub: `${100 - persen}% Belum terserap` },
                        ].map(s => (
                            <div key={s.label} className="bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 p-4 text-center">
                                <p className="text-[8px] font-black text-white/60 uppercase tracking-widest mb-1">{s.label}</p>
                                <p className="text-sm sm:text-base font-black text-white">{s.value}</p>
                                <p className="text-[8px] font-bold text-white/50 mt-0.5">{s.sub}</p>
                            </div>
                        ))}
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* ── Cetak Laporan ────────────────────── */}
                    <div className="lg:col-span-2 space-y-4">
                        <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-2">
                            <FileText className="w-4 h-4 text-green-600" /> Cetak Laporan PDF
                        </h2>
                        {REPORT_CARDS.map(card => {
                            const Icon = card.icon;
                            return (
                                <div key={card.id} className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6 transition-all hover:shadow-md hover:border-green-100 group">
                                    <div className="flex items-start gap-4">
                                        <div className={cn('w-10 h-10 rounded-xl flex items-center justify-center shrink-0', card.iconBg)}>
                                            <Icon className={cn('w-5 h-5', card.iconColor)} />
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-start justify-between gap-3 flex-wrap">
                                                <div>
                                                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">{card.title}</h3>
                                                    <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{card.subtitle}</p>
                                                </div>
                                                <span className="px-2.5 py-1 bg-gray-50 border border-gray-100 rounded-lg text-[8px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">{card.orientation}</span>
                                            </div>
                                            <p className="text-[10px] font-bold text-gray-500 mt-2 leading-relaxed">{card.desc}</p>

                                            {/* Extra params */}
                                            {card.params.length > 0 && (
                                                <div className="mt-3 flex flex-wrap gap-3">
                                                    {card.params.map(p => (
                                                        <div key={p.key} className="flex-1 min-w-[140px]">
                                                            <label className="text-[9px] font-black text-gray-400 uppercase tracking-widest block mb-1">{p.label}</label>
                                                            <select
                                                                value={params[card.id + '_' + p.key] ?? ''}
                                                                onChange={e => updateParam(card.id, p.key, e.target.value)}
                                                                className="w-full border border-gray-200 rounded-xl px-3 py-2 text-xs font-bold focus:ring-2 focus:ring-green-500 appearance-none cursor-pointer"
                                                            >
                                                                {p.options.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                                                            </select>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                    <div className="mt-4 flex justify-end">
                                        <button
                                            onClick={() => handleDownload(card)}
                                            className="flex items-center gap-2.5 px-6 py-3 bg-green-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-md shadow-green-200 hover:scale-105 active:scale-95"
                                        >
                                            <Download className="w-3.5 h-3.5" />
                                            UNDUH PDF — {selectedTahun}
                                        </button>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {/* ── Summary per Bidang ─────────────── */}
                    <div className="space-y-4">
                        <h2 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-2">
                            <TrendingUp className="w-4 h-4 text-green-600" /> Ringkasan {selectedTahun}
                        </h2>

                        {Object.keys(summaryByBidang).length > 0 ? (
                            Object.entries(summaryByBidang)
                                .sort(([a], [b]) => (Number(a) || 99) - (Number(b) || 99))
                                .map(([bidang, data]) => {
                                    const bNum = Number(bidang);
                                    const cfg  = BIDANG_COLOR[bNum] ?? { bg: 'bg-gray-50', text: 'text-gray-700', border: 'border-gray-100' };
                                    const pct  = data.anggaran > 0 ? Math.round((data.realisasi / data.anggaran) * 100) : 0;
                                    return (
                                        <div key={bidang} className={cn('rounded-2xl border p-4 space-y-3', cfg.border, cfg.bg)}>
                                            <div className="flex items-start justify-between gap-2">
                                                <div>
                                                    <p className={cn('text-[8px] font-black uppercase tracking-widest', cfg.text)}>Bidang {bNum || '—'}</p>
                                                    <p className="text-[10px] font-black text-gray-900 leading-tight mt-0.5">{BIDANG_MAP[bNum] ?? 'Belum Terklasifikasi'}</p>
                                                </div>
                                                <span className={cn('text-base font-black', cfg.text)}>{pct}%</span>
                                            </div>
                                            <div className="w-full h-1.5 bg-white/50 rounded-full overflow-hidden">
                                                <div className="h-full rounded-full bg-current transition-all" style={{ width: `${Math.min(pct, 100)}%` }} />
                                            </div>
                                            <div className="flex justify-between text-[8px] font-black text-gray-500 uppercase tracking-widest">
                                                <span>Anggaran: {formatRupiah(data.anggaran)}</span>
                                                <span>Real: {formatRupiah(data.realisasi)}</span>
                                            </div>
                                        </div>
                                    );
                                })
                        ) : (
                            <div className="bg-white rounded-2xl border border-gray-100 p-8 text-center">
                                <BarChart3 className="w-10 h-10 text-gray-200 mx-auto mb-3" />
                                <p className="text-xs font-black text-gray-400 uppercase italic tracking-tighter">Belum ada data APBDes</p>
                                <p className="text-[9px] text-gray-300 font-bold uppercase tracking-widest mt-1">untuk tahun {selectedTahun}</p>
                            </div>
                        )}

                        {/* Quick nav */}
                        <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 space-y-2">
                            <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Navigasi Cepat</p>
                            {[
                                { href: route('transparansi-desa.apbdes', { tahun: selectedTahun }), label: 'APBDes · Tabel Rekening', icon: BarChart3 },
                                { href: route('transparansi-desa.proyek', { tahun: selectedTahun }), label: 'Proyek Desa', icon: Layers },
                            ].map(nav => {
                                const NavIcon = nav.icon;
                                return (
                                    <Link key={nav.href} href={nav.href}
                                        className="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-50 border border-transparent hover:border-green-100 transition-all group">
                                        <NavIcon className="w-4 h-4 text-gray-300 group-hover:text-green-600 transition-colors" />
                                        <span className="text-[10px] font-black text-gray-500 uppercase tracking-widest group-hover:text-green-700">{nav.label}</span>
                                    </Link>
                                );
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
