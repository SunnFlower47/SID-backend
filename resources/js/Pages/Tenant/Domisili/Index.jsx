import React, { useState } from 'react';
import { Head, Link, router, Deferred } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, TableCard } from '@/Components/Shared';
import SkeletonStats from '@/Components/Shared/Skeleton/SkeletonStats';
import SkeletonTable from '@/Components/Shared/Skeleton/SkeletonTable';
import DomisiliStats from '@/Components/Domisili/DomisiliStats';
import DomisiliFilters from '@/Components/Domisili/DomisiliFilters';
import { MapPin, Plus, CheckCircle, Clock, AlertTriangle, XCircle, Edit, Trash2, RefreshCw, Ban, Filter, Search, UserCheck, Eye, User, Home, Info, Calendar, Briefcase, Heart, X, ClipboardList } from 'lucide-react';
import Swal from 'sweetalert2';
import { cn } from '@/lib/utils';
import Lottie from 'lottie-react';
import noDataAnimation from '@/assets/lottie/no-data-animation.json';

const LottieComponent = Lottie?.default || Lottie;

const STATUS_CONFIG = {
    aktif: { label: 'AKTIF', icon: CheckCircle, bg: 'bg-green-100', text: 'text-green-700' },
    expired: { label: 'EXPIRED', icon: Clock, bg: 'bg-orange-100', text: 'text-orange-700' },
    dicabut: { label: 'DICABUT', icon: XCircle, bg: 'bg-red-100', text: 'text-red-700' },
};

function StatusBadge({ status }) {
    const cfg = STATUS_CONFIG[status] || STATUS_CONFIG.expired;
    const Icon = cfg.icon;
    return (
        <span className={cn('inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold', cfg.bg, cfg.text)}>
            <Icon className="w-3 h-3" />{cfg.label}
        </span>
    );
}



export default function Index({ auth, domisilis, stats, filters, rtList, rwList, dusunList }) {
    const [showCabutModal, setShowCabutModal] = useState(false);
    const [cabutTarget, setCabutTarget] = useState(null);
    const [alasan, setAlasan] = useState('');

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'HAPUS DATA DOMISILI',
            html: `Data domisili atas nama <b class="text-red-600">${nama}</b> akan dihapus secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS DATA!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-red-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-red-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then(res => {
            if (res.isConfirmed) router.delete(route('domisili.destroy', id), { 
                onSuccess: () => { /* Global alert handles this */ } 
            });
        });
    };

    const handlePerpanjang = (id, nama) => {
        Swal.fire({
            title: 'PERPANJANG DOMISILI',
            html: `Domisili atas nama <b class="text-emerald-600">${nama}</b> akan diperpanjang melalui pembuatan surat baru agar memiliki history yang jelas.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, PERPANJANG!',
            cancelButtonText: 'BATALKAN',
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-none shadow-2xl',
                title: 'font-black tracking-tighter uppercase italic text-emerald-600',
                confirmButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-200',
                cancelButton: 'rounded-2xl px-6 py-3 font-black uppercase tracking-widest text-[10px] text-gray-500'
            }
        }).then(res => {
            if (res.isConfirmed) {
                // Hit api perpanjang langsung tanpa membuka form lagi
                router.post(route('domisili.perpanjang', id), {}, {
                    onSuccess: () => {
                        // Tidak perlu alert lagi karena controller sudah mengembalikan flash 'success'
                    }
                });
            }
        });
    };

    const openCabut = (item) => { setCabutTarget(item); setAlasan(''); setShowCabutModal(true); };
    const submitCabut = () => {
        if (alasan.length < 10) { 
            Swal.fire({
                icon: 'warning',
                title: 'PERHATIAN!',
                text: 'Alasan minimal 10 karakter.',
                customClass: { popup: 'rounded-3xl' }
            }); 
            return; 
        }
        router.post(route('domisili.cabut', cabutTarget.id), { alasan }, {
            onSuccess: () => { 
                setShowCabutModal(false); 
                /* Global alert handles this */ 
            },
            onError: (e) => Swal.fire({
                icon: 'error',
                title: 'GAGAL!',
                text: Object.values(e)[0],
                customClass: { popup: 'rounded-3xl' }
            }),
        });
    };



    return (
        <AuthenticatedLayout user={auth.user} title="Penduduk Domisili">
            <Head title="Penduduk Domisili" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                <PageHeader 
                    title="Penduduk Domisili"
                    subtitle="Warga Pendatang Sementara"
                    icon={MapPin}
                    actions={[
                        {
                            label: 'TAMBAH',
                            icon: Plus,
                            href: route('domisili.create'),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Stats */}
                <Deferred data="stats" fallback={<SkeletonStats />}>
                    <DomisiliStats stats={stats} />
                </Deferred>

                {/* Filter Bar */}
                <DomisiliFilters filters={filters} rtList={rtList} rwList={rwList} />

                {/* Table */}
                <Deferred data="domisilis" fallback={<SkeletonTable columns={7} rows={10} />}>
                    <TableCard 
                        title="Daftar Penduduk Domisili"
                        icon={MapPin}
                        total={domisilis?.total || 0}
                        noPadding
                        pagination={{
                            links: domisilis?.links,
                            from: domisilis?.from,
                            to: domisilis?.to,
                            total: domisilis?.total
                        }}
                    >
                        {domisilis?.data?.length > 0 ? (
                            <>
                                {/* Desktop Table */}
                                <div className="hidden lg:block overflow-x-auto">
                                    <table className="w-full text-left text-sm text-gray-600">
                                        <thead className="bg-gray-50/50 text-gray-900 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                                            <tr>
                                                <th className="px-5 py-4">Identitas</th>
                                                <th className="px-5 py-4">Asal Daerah</th>
                                                <th className="px-5 py-4">Tgl Masuk</th>
                                                <th className="px-5 py-4">Lokasi Tinggal</th>
                                                <th className="px-5 py-4">Berlaku S/D</th>
                                                <th className="px-5 py-4">Status</th>
                                                <th className="px-5 py-4 text-right">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-50">
                                            {domisilis.data.map(d => (
                                                <tr key={d.id} className="hover:bg-green-50/30 transition-colors">
                                                    <td className="px-5 py-4 text-left">
                                                        <p className="font-black text-gray-900 uppercase text-xs tracking-tight">{d.nama}</p>
                                                        <p className="font-mono text-[10px] text-gray-400">{d.nik}</p>
                                                    </td>
                                                    <td className="px-5 py-4 text-xs font-bold text-gray-600 text-left">
                                                        {d.asal_daerah || (d.alamat_asal ? (d.alamat_asal.length > 30 ? d.alamat_asal.substring(0, 30) + '...' : d.alamat_asal) : '-')}
                                                    </td>
                                                    <td className="px-5 py-4 text-xs font-bold text-gray-800 italic text-left">{d.tanggal_masuk ? new Date(d.tanggal_masuk).toLocaleDateString('id-ID') : '-'}</td>
                                                    <td className="px-5 py-4">
                                                        <p className="text-xs font-bold text-gray-700 uppercase italic text-left">Dsn. {d.dusun_label}</p>
                                                        <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-left">RT {d.rt_label} / RW {d.rw_label}</p>
                                                    </td>
                                                    <td className="px-5 py-4 text-left">
                                                        <p className="text-xs font-bold text-gray-800">
                                                            {d.tanggal_berlaku ? new Date(d.tanggal_berlaku).toLocaleDateString('id-ID') : '-'}
                                                        </p>
                                                        {d.sisa_hari_berlaku > 0
                                                            ? <p className={cn('text-[10px] font-bold', d.sisa_hari_berlaku <= 30 ? 'text-orange-500' : 'text-gray-400')}>{d.sisa_hari_berlaku} hari lagi</p>
                                                            : (d.status !== 'dicabut' && <p className="text-[10px] font-bold text-red-500">Sudah lewat</p>)
                                                        }
                                                    </td>
                                                    <td className="px-5 py-4 text-left"><StatusBadge status={d.status} /></td>
                                                    <td className="px-5 py-4">
                                                        <div className="flex justify-end gap-2">
                                                            <Link href={route('domisili.show', d.id)} className="w-8 h-8 flex items-center justify-center rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all" title="Detail">
                                                                <Eye className="w-3.5 h-3.5" />
                                                            </Link>
                                                            <Link href={route('domisili.edit', d.id)} className="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 text-gray-600 hover:bg-gray-800 hover:text-white transition-all" title="Edit">
                                                                <Edit className="w-3.5 h-3.5" />
                                                            </Link>
                                                            {d.status !== 'dicabut' && (
                                                                <button onClick={() => handlePerpanjang(d.id, d.nama)} className="w-8 h-8 flex items-center justify-center rounded-xl bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-all" title="Perpanjang">
                                                                    <RefreshCw className="w-3.5 h-3.5" />
                                                                </button>
                                                            )}
                                                            {d.status === 'aktif' && (
                                                                <button onClick={() => openCabut(d)} className="w-8 h-8 flex items-center justify-center rounded-xl bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white transition-all" title="Cabut">
                                                                    <Ban className="w-3.5 h-3.5" />
                                                                </button>
                                                            )}
                                                            <button onClick={() => handleDelete(d.id, d.nama)} className="w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all" title="Hapus">
                                                                <Trash2 className="w-3.5 h-3.5" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>

                                {/* Mobile Cards */}
                                <div className="lg:hidden p-4 space-y-4 bg-gray-50/50">
                                    {domisilis.data.map(d => (
                                        <div key={d.id} className="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-left">
                                            <div className="flex justify-between items-start mb-3">
                                                <div>
                                                    <h4 className="font-black text-gray-900 uppercase italic text-sm">{d.nama}</h4>
                                                    <p className="font-mono text-[10px] text-gray-400">{d.nik}</p>
                                                </div>
                                                <StatusBadge status={d.status} />
                                            </div>
                                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Dsn. {d.dusun_label} | RT {d.rt_label}/RW {d.rw_label}</p>
                                            <p className="text-xs text-gray-600 mb-3">
                                                Masuk: {d.tanggal_masuk ? new Date(d.tanggal_masuk).toLocaleDateString('id-ID') : '-'} · 
                                                Berlaku: {d.tanggal_berlaku ? new Date(d.tanggal_berlaku).toLocaleDateString('id-ID') : '-'}
                                             </p>
                                            <div className="flex gap-2">
                                                <Link href={route('domisili.show', d.id)} className="px-3 py-2.5 bg-blue-50 text-blue-600 rounded-xl"><Eye className="w-4 h-4" /></Link>
                                                <Link href={route('domisili.edit', d.id)} className="flex-1 py-2.5 bg-gray-50 text-gray-700 rounded-xl text-[10px] font-black text-center uppercase tracking-widest">EDIT</Link>
                                                {d.status !== 'dicabut' && <button onClick={() => handlePerpanjang(d.id, d.nama)} className="flex-1 py-2.5 bg-green-50 text-green-700 rounded-xl text-[10px] font-black text-center uppercase tracking-widest">PERPANJANG</button>}
                                                <button onClick={() => handleDelete(d.id, d.nama)} className="px-3 py-2.5 bg-red-50 text-red-600 rounded-xl"><Trash2 className="w-4 h-4" /></button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </>
                        ) : (
                            <div className="p-16 text-center">
                                <div className="w-56 h-56 mx-auto mb-4 opacity-80">
                                    <LottieComponent animationData={noDataAnimation} loop={true} />
                                </div>
                                <h3 className="text-xl font-black text-gray-900 uppercase italic">Data Domisili Kosong</h3>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-2">Gunakan tombol Daftar Pendatang untuk mulai mencatat.</p>
                            </div>
                        )}
                    </TableCard>
                </Deferred>
            </div>

            {/* Cabut Modal */}
            {showCabutModal && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl p-8 shadow-2xl w-full max-w-md mx-4 animate-in zoom-in-95 duration-300">
                        <div className="flex items-center gap-3 mb-6">
                            <div className="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center"><Ban className="w-6 h-6 text-red-600" /></div>
                            <div className="text-left">
                                <h3 className="text-lg font-black text-gray-900 uppercase italic">Cabut Domisili</h3>
                                <p className="text-xs text-gray-500 font-bold uppercase tracking-widest">{cabutTarget?.nama}</p>
                            </div>
                        </div>
                        <label className="block text-xs font-black text-gray-700 uppercase tracking-widest mb-2 text-left">Alasan Pencabutan *</label>
                        <textarea rows={4} value={alasan} onChange={e => setAlasan(e.target.value)} placeholder="Minimal 10 karakter..."
                            className="w-full border border-gray-200 rounded-2xl p-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none text-left" />
                        <p className="text-[10px] text-gray-400 mt-1 text-left">{alasan.length}/500 karakter</p>
                        <div className="flex gap-3 mt-6">
                            <button onClick={() => setShowCabutModal(false)} className="flex-1 py-3 rounded-2xl border border-gray-200 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all">Batal</button>
                            <button onClick={submitCabut} className="flex-1 py-3 rounded-2xl bg-red-600 text-white text-xs font-black uppercase tracking-widest hover:bg-red-700 transition-all">Cabut Sekarang</button>
                        </div>
                    </div>
                </div>
            )}

        </AuthenticatedLayout>
    );
}

function DetailItem({ label, value, color = 'text-gray-900' }) {
    return (
        <div className="flex justify-between items-center text-[11px] gap-4">
            <span className="font-bold text-gray-400 uppercase tracking-widest shrink-0">{label}</span>
            <span className={cn('font-black uppercase tracking-tight text-right truncate', color)} title={value}>{value || '-'}</span>
        </div>
    );
}
