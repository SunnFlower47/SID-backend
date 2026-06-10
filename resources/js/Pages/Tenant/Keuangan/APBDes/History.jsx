import React, { useState } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import { History, ArrowLeft, Plus, Edit2, Trash2, Save, X, Calendar, FileText, Upload, Eye, Download, CheckCircle, Clock, XCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import { PageHeader, TableCard, FormField, Badge } from '@/Components/Shared';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;
const formatDate   = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }) : '-';

const JENIS_CONFIG = {
    pendapatan: 'emerald',
    belanja:    'blue',
    pembiayaan: 'purple',
};

const SPJ_CONFIG = {
    belum: { icon: Clock,         color: 'yellow', label: 'Belum SPJ' },
    sudah: { icon: CheckCircle,   color: 'green',  label: 'Sudah SPJ' },
};

export default function HistoryPage({ auth, apbdes, jenisBuktiOptions = {} }) {
    const [previewFile, setPreviewFile] = useState(null);
    const [detailItem, setDetailItem] = useState(null);
    const sisaAnggaran = Number(apbdes.anggaran) - Number(apbdes.realisasi);

    const handleDelete = (id, nama) => {
        Swal.fire({
            title: 'HAPUS PENGELUARAN?',
            html: `Hapus <b class="text-red-600">${nama}</b>?<br><small class="text-gray-400 font-bold uppercase tracking-widest text-[9px]">File bukti akan ikut dihapus</small>`,
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#ef4444', cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS!', cancelButtonText: 'BATAL',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px]', cancelButton: 'rounded-2xl px-5 py-2.5 font-black uppercase tracking-widest text-[10px] text-gray-500' },
        }).then(r => { if (r.isConfirmed) router.delete(route('anggaran.delete-pengeluaran', id), { preserveScroll: true }); });
    };

    const jenisColor = JENIS_CONFIG[apbdes.jenis] ?? 'blue';

    return (
        <AuthenticatedLayout user={auth.user} title="Histori Pengeluaran">
            <Head title="Histori Pengeluaran APBDes" />

            {/* Detail Modal */}
            {detailItem && (() => {
                const dSpj = SPJ_CONFIG[detailItem.spj_status] ?? SPJ_CONFIG.belum;
                return (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" onClick={() => setDetailItem(null)}>
                    <div className="bg-white rounded-3xl shadow-2xl max-w-xl w-full max-h-[90vh] flex flex-col" onClick={e => e.stopPropagation()}>
                        {/* Header */}
                        <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                            <div>
                                <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter">Detail Pengeluaran</h3>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{detailItem.no_bukti ?? 'Tanpa Nomor Bukti'}</p>
                            </div>
                            <button onClick={() => setDetailItem(null)} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                                <X className="w-5 h-5" />
                            </button>
                        </div>

                        {/* Body */}
                        <div className="p-6 overflow-auto space-y-4">
                            {/* Nama */}
                            <p className="text-base font-black text-gray-900">{detailItem.nama_pengeluaran}</p>

                            {/* Grid info utama */}
                            <div className="grid grid-cols-2 gap-3">
                                {[
                                    { label: 'Tanggal', value: formatDate(detailItem.tanggal_pengeluaran) },
                                    { label: 'Penerima', value: detailItem.nama_penerima || '—' },
                                    { label: 'Jenis Transaksi', value: (detailItem.jenis_transaksi || 'belanja').replace(/_/g, ' ') },
                                    { label: 'Jenis Bukti', value: detailItem.jenis_bukti_label || detailItem.jenis_bukti || '—' },
                                ].map(f => (
                                    <div key={f.label} className="bg-gray-50 rounded-xl p-3 border border-gray-100">
                                        <span className="text-[9px] font-black text-gray-400 uppercase tracking-widest">{f.label}</span>
                                        <p className="text-xs font-bold text-gray-800 mt-0.5 capitalize">{f.value}</p>
                                    </div>
                                ))}
                            </div>

                            {/* Total & Pajak */}
                            <div className="rounded-2xl border border-blue-100 bg-blue-50/50 p-4 space-y-3">
                                <div className="flex justify-between items-center">
                                    <span className="text-[10px] font-black text-blue-400 uppercase tracking-widest">Total Belanja</span>
                                    <span className="text-base font-black text-blue-600">{formatRupiah(detailItem.jumlah)}</span>
                                </div>
                                {(['ppn','pph21','pph22','pph23'].some(t => Number(detailItem[`pajak_${t}`]) > 0)) && (
                                    <div className="border-t border-blue-100 pt-3 space-y-1.5">
                                        <span className="text-[9px] font-black text-blue-300 uppercase tracking-widest block">Rincian Pajak</span>
                                        {[
                                            { key: 'ppn',   label: 'PPN'    },
                                            { key: 'pph21', label: 'PPh 21' },
                                            { key: 'pph22', label: 'PPh 22' },
                                            { key: 'pph23', label: 'PPh 23' },
                                        ].filter(t => Number(detailItem[`pajak_${t.key}`]) > 0).map(t => (
                                            <div key={t.key} className="flex justify-between text-xs">
                                                <span className="font-bold text-blue-500">{t.label}</span>
                                                <span className="font-black text-blue-700">{formatRupiah(detailItem[`pajak_${t.key}`])}</span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>

                            {/* Status SPJ */}
                            <div className="flex items-center justify-between rounded-xl border border-gray-100 bg-gray-50 px-4 py-3">
                                <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status SPJ</span>
                                <Link
                                    href={route('anggaran.toggle-spj', detailItem.id)}
                                    method="patch"
                                    preserveScroll
                                    as="button"
                                    className="inline-flex items-center gap-1.5 transition-transform hover:scale-105 active:scale-95"
                                    title="Klik untuk toggle status SPJ"
                                    onClick={() => setDetailItem(null)}
                                >
                                    <Badge color={dSpj.color} icon={dSpj.icon}>{dSpj.label}</Badge>
                                </Link>
                            </div>

                            {/* Keterangan */}
                            {detailItem.keterangan && (
                                <div>
                                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Keterangan</span>
                                    <p className="text-xs font-medium text-gray-600 italic mt-1 bg-gray-50 p-3 rounded-xl border border-gray-100">{detailItem.keterangan}</p>
                                </div>
                            )}
                        </div>

                        {/* Footer aksi */}
                        <div className="flex flex-wrap items-center gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-3xl">
                            {detailItem.file_bukti_url && (
                                <button
                                    onClick={() => { setDetailItem(null); setPreviewFile({ url: detailItem.file_bukti_url, nama: detailItem.nama_file_bukti, noBukti: detailItem.no_bukti }); }}
                                    className="flex items-center gap-1.5 px-3 py-2 bg-blue-50 border border-blue-100 rounded-xl text-[10px] font-black text-blue-600 uppercase tracking-widest hover:bg-blue-100 transition-all"
                                >
                                    <Eye className="w-3.5 h-3.5" /> Lihat Dokumen
                                </button>
                            )}
                            <a href={route('laporan-keuangan.pdf-spp', detailItem.id)} target="_blank" rel="noreferrer"
                                className="flex items-center gap-1.5 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-xl text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:bg-indigo-100 transition-all">
                                <FileText className="w-3.5 h-3.5" /> Cetak SPP
                            </a>
                            <a href={route('laporan-keuangan.pdf-kwitansi', detailItem.id)} target="_blank" rel="noreferrer"
                                className="flex items-center gap-1.5 px-3 py-2 bg-teal-50 border border-teal-100 rounded-xl text-[10px] font-black text-teal-600 uppercase tracking-widest hover:bg-teal-100 transition-all">
                                <FileText className="w-3.5 h-3.5" /> Cetak Kwitansi
                            </a>
                            <Link href={route('anggaran.edit-pengeluaran', detailItem.id)}
                                className="ml-auto flex items-center gap-1.5 px-3 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-gray-600 uppercase tracking-widest hover:bg-gray-100 transition-all">
                                <Edit2 className="w-3.5 h-3.5" /> Edit
                            </Link>
                        </div>
                    </div>
                </div>
                );
            })()}

            {/* Preview File Modal */}
            {previewFile && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" onClick={() => setPreviewFile(null)}>
                    <div className="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onClick={e => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-5 border-b border-gray-100">
                            <div>
                                <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{previewFile.nama}</p>
                                <p className="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">{previewFile.noBukti}</p>
                            </div>
                            <div className="flex gap-2">
                                <a href={previewFile.url} download target="_blank" rel="noopener noreferrer"
                                    className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-700 transition-all">
                                    <Download className="w-3 h-3" /> Download
                                </a>
                                <button onClick={() => setPreviewFile(null)} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                                    <X className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div className="h-[75vh] overflow-auto">
                            {previewFile.url.endsWith('.pdf')
                                ? <iframe src={previewFile.url} className="w-full h-full" title="Preview Bukti" />
                                : <img src={previewFile.url} alt="Preview Bukti" className="w-full h-full object-contain bg-gray-50 p-4" />
                            }
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title={apbdes.nama_rekening}
                    subtitle={`Histori Pengeluaran · ${apbdes.kode_rekening} · ${apbdes.tahun}`}
                    icon={History}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('transparansi-desa.apbdes'),
                            variant: 'ghost'
                        },
                        {
                            label: 'TAMBAH PENGELUARAN',
                            icon: Plus,
                            href: route('anggaran.create-pengeluaran', { apbdes_id: apbdes.id }),
                            variant: 'white'
                        }
                    ]}
                />

                {/* Status Card */}
                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div className="flex items-center justify-between">
                        <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Status Rekening</h3>
                        <Badge color={jenisColor}>{apbdes.jenis}</Badge>
                    </div>
                    <div className="space-y-2">
                        {[
                            { label: 'Anggaran', value: formatRupiah(apbdes.anggaran), color: 'text-gray-900' },
                            { label: 'Realisasi', value: formatRupiah(apbdes.realisasi), color: 'text-blue-600' },
                            { label: 'Sisa Anggaran', value: formatRupiah(sisaAnggaran), color: sisaAnggaran >= 0 ? 'text-green-600' : 'text-red-600' },
                        ].map(r => (
                            <div key={r.label} className="flex justify-between py-2 border-b border-gray-50 last:border-0">
                                <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">{r.label}</span>
                                <span className={cn('text-xs font-black', r.color)}>{r.value}</span>
                            </div>
                        ))}
                    </div>
                    <AnggaranProgressBar anggaran={Number(apbdes.anggaran)} realisasi={Number(apbdes.realisasi)} height="h-2" showLabels={false} />
                    <p className="text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">
                        {apbdes.anggaran > 0 ? Math.min(100, Math.round((apbdes.realisasi / apbdes.anggaran) * 100)) : 0}% Terserap
                    </p>
                </div>

                {/* History Table */}
                <TableCard
                    title="Histori Pengeluaran"
                    icon={History}
                    total={apbdes.histori_pengeluarans?.length ?? 0}
                    totalLabel="Transaksi"
                    noPadding={true}
                >
                    {apbdes.histori_pengeluarans?.length > 0 ? (
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="bg-gray-50/80 border-b border-gray-100">
                                        {['No. Bukti', 'Tanggal', 'Pengeluaran', 'Rincian Nilai', 'Jenis Bukti', 'Dokumen', 'SPJ', 'Aksi'].map(h => (
                                            <th key={h} className="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">{h}</th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {apbdes.histori_pengeluarans.map((item) => {
                                        const spjCfg = SPJ_CONFIG[item.spj_status] ?? SPJ_CONFIG.belum;
                                        return (
                                            <tr key={item.id} className="hover:bg-gray-50/50 transition-all group">
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className="text-[9px] font-black text-gray-500 font-mono uppercase">{item.no_bukti ?? '—'}</span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-2">
                                                        <Calendar className="w-3.5 h-3.5 text-gray-300" />
                                                        <span className="text-xs font-bold text-gray-600">{formatDate(item.tanggal_pengeluaran)}</span>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 max-w-[220px]">
                                                    <p className="text-xs font-black text-gray-900 truncate">{item.nama_pengeluaran}</p>
                                                    {item.nama_penerima && <p className="text-[10px] text-gray-500 font-bold mt-0.5 truncate flex items-center gap-1"><span className="w-1 h-1 bg-gray-300 rounded-full"></span>{item.nama_penerima}</p>}
                                                    {item.keterangan && <p className="text-[9px] text-gray-400 font-medium italic mt-0.5 truncate">{item.keterangan}</p>}
                                                    {item.jenis_transaksi && item.jenis_transaksi !== 'belanja' && (
                                                        <span className="inline-block mt-1.5 px-1.5 py-0.5 bg-orange-50 text-orange-600 border border-orange-100 rounded text-[8px] font-black uppercase tracking-widest">
                                                            {item.jenis_transaksi.replace('_', ' ')}
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <div className="flex flex-col gap-1">
                                                        <span className="text-sm font-black text-blue-600">{formatRupiah(item.jumlah)}</span>
                                                        <div className="flex flex-wrap gap-1">
                                                            {item.pajak_ppn > 0 && <span className="text-[8px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">PPN: {formatRupiah(item.pajak_ppn)}</span>}
                                                            {item.pajak_pph21 > 0 && <span className="text-[8px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">PPh 21: {formatRupiah(item.pajak_pph21)}</span>}
                                                            {item.pajak_pph22 > 0 && <span className="text-[8px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">PPh 22: {formatRupiah(item.pajak_pph22)}</span>}
                                                            {item.pajak_pph23 > 0 && <span className="text-[8px] font-black text-green-600 bg-green-50 px-1.5 py-0.5 rounded border border-green-100">PPh 23: {formatRupiah(item.pajak_pph23)}</span>}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <span className="px-2 py-0.5 bg-gray-50 border border-gray-100 rounded-lg text-[9px] font-black text-gray-600 uppercase tracking-widest">
                                                        {item.jenis_bukti_label ?? item.jenis_bukti ?? '—'}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    {item.file_bukti_url ? (
                                                        <button
                                                            onClick={() => setPreviewFile({ url: item.file_bukti_url, nama: item.nama_file_bukti, noBukti: item.no_bukti })}
                                                            className="flex items-center gap-1.5 px-2.5 py-1.5 bg-blue-50 border border-blue-100 rounded-lg text-[9px] font-black text-blue-600 uppercase tracking-widest hover:bg-blue-100 transition-all"
                                                        >
                                                            <Eye className="w-3 h-3" /> LIHAT
                                                        </button>
                                                    ) : (
                                                        <span className="text-[9px] text-gray-300 font-bold uppercase tracking-widest">Tidak ada</span>
                                                    )}
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <Link href={route('anggaran.toggle-spj', item.id)} method="patch" preserveScroll as="button" className="inline-block transition-transform hover:scale-105 active:scale-95" title="Klik untuk mengubah status SPJ">
                                                        <Badge color={spjCfg.color} icon={spjCfg.icon}>
                                                            {spjCfg.label}
                                                        </Badge>
                                                    </Link>
                                                </td>
                                                <td className="px-4 py-4 whitespace-nowrap">
                                                    <div className="flex items-center gap-2">
                                                        <a href={route('laporan-keuangan.pdf-spp', item.id)} target="_blank" rel="noreferrer" title="Cetak SPP"
                                                            className="px-2 py-1.5 bg-indigo-50 text-indigo-600 border border-indigo-100 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all flex items-center gap-1">
                                                            <FileText className="w-3 h-3" /> SPP
                                                        </a>
                                                        <a href={route('laporan-keuangan.pdf-kwitansi', item.id)} target="_blank" rel="noreferrer" title="Cetak Kwitansi"
                                                            className="px-2 py-1.5 bg-teal-50 text-teal-600 border border-teal-100 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-teal-100 transition-all flex items-center gap-1">
                                                            <FileText className="w-3 h-3" /> KWITANSI
                                                        </a>
                                                        <div className="w-px h-4 bg-gray-200 mx-1"></div>
                                                        <button onClick={() => setDetailItem(item)}
                                                            className="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-xl transition-all" title="Lihat Detail">
                                                            <Eye className="w-3.5 h-3.5" />
                                                        </button>
                                                        <Link href={route('anggaran.edit-pengeluaran', item.id)}
                                                            className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Edit Pengeluaran">
                                                            <Edit2 className="w-3.5 h-3.5" />
                                                        </Link>
                                                        <button onClick={() => handleDelete(item.id, item.nama_pengeluaran)}
                                                            className="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus Pengeluaran">
                                                            <Trash2 className="w-3.5 h-3.5" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                                <tfoot>
                                    <tr className="bg-gray-50/80 border-t border-gray-100">
                                        <td colSpan={3} className="px-4 py-3">
                                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Pengeluaran</span>
                                        </td>
                                        <td className="px-4 py-3">
                                            <span className="text-sm font-black text-blue-700">
                                                {formatRupiah(apbdes.histori_pengeluarans.reduce((s, i) => s + Number(i.jumlah), 0))}
                                            </span>
                                        </td>
                                        <td colSpan={4} />
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    ) : (
                        <div className="p-16 text-center">
                            <FileText className="w-12 h-12 text-gray-200 mx-auto mb-4" />
                            <h3 className="text-base font-black text-gray-400 uppercase italic tracking-tighter">Belum Ada Pengeluaran</h3>
                            <p className="text-[10px] text-gray-300 font-bold uppercase tracking-widest mt-2 mb-6">Klik "Tambah Pengeluaran" untuk mencatat realisasi</p>
                            <Link href={route('anggaran.create-pengeluaran', { apbdes_id: apbdes.id })} className="inline-flex items-center gap-2 px-8 py-4 bg-green-600 text-white rounded-2xl text-xs font-black shadow-xl shadow-green-200 hover:bg-green-700 transition-all uppercase tracking-widest">
                                <Plus className="w-4 h-4" /> TAMBAH PENGELUARAN PERTAMA
                            </Link>
                        </div>
                    )}
                </TableCard>
            </div>
        </AuthenticatedLayout>
    );
}
