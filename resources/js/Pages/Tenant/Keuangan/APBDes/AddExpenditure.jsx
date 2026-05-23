import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Calendar, Wallet, AlertTriangle, FileText } from 'lucide-react';
import { cn } from '@/lib/utils';
import { PageHeader, FormField, FormCard } from '@/Components/Shared';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;
const formatDate   = (d) => d ? new Date(d).toISOString().split('T')[0] : '';

export default function AddExpenditure({ auth, apbdesList = [], tahunList = [], tahun, jenis }) {
    const { data, setData, post, processing, errors } = useForm({
        apbdes_id:           '',
        nama_pengeluaran:    '',
        jumlah:              '',
        tanggal_pengeluaran: new Date().toISOString().split('T')[0],
        keterangan:          '',
    });

    const selectedApbdes = apbdesList.find(a => String(a.id) === String(data.apbdes_id));
    const sisaAnggaran   = selectedApbdes ? Number(selectedApbdes.sisa_anggaran) : null;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.store-pengeluaran'));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Pengeluaran">
            <Head title="Tambah Pengeluaran APBDes" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Catat Pengeluaran"
                    subtitle="Realisasi Anggaran Rekening APBDes"
                    icon={Wallet}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('transparansi-desa.apbdes'),
                            variant: 'ghost'
                        }
                    ]}
                />

                {apbdesList.length === 0 && (
                    <div className="bg-yellow-50 border border-yellow-100 rounded-2xl p-5 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-yellow-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-yellow-700 uppercase tracking-tighter italic">Tidak Ada Rekening Tersedia</p>
                            <p className="text-[10px] font-bold text-yellow-600 uppercase tracking-wider mt-0.5">
                                Tidak ada rekening {jenis} tahun {tahun} dengan sisa anggaran. Silakan tambah rekening atau pilih tahun/jenis lain.
                            </p>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div className="lg:col-span-2 space-y-6">
                            <FormCard title="Pilih Rekening & Catat Pengeluaran" icon={FileText} bodyClass="p-6 sm:p-8 space-y-6">
                                <FormField.Select 
                                    label="Rekening APBDes" 
                                    error={errors.apbdes_id}
                                    value={data.apbdes_id}
                                    onChange={e => setData('apbdes_id', e.target.value)}
                                    placeholder="-- Pilih Rekening --"
                                >
                                    {apbdesList.map(a => (
                                        <option key={a.id} value={a.id}>
                                            [{a.kode_rekening}] {a.nama_rekening} — Sisa: {formatRupiah(a.sisa_anggaran)}
                                        </option>
                                    ))}
                                </FormField.Select>

                                {selectedApbdes && (
                                    <div className="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-2">
                                        <p className="text-[9px] font-black text-blue-500 uppercase tracking-widest">Info Rekening Terpilih</p>
                                        <div className="flex gap-6">
                                            <div>
                                                <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Anggaran</p>
                                                <p className="text-sm font-black text-blue-900">{formatRupiah(selectedApbdes.anggaran)}</p>
                                            </div>
                                            <div>
                                                <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Realisasi</p>
                                                <p className="text-sm font-black text-blue-600">{formatRupiah(selectedApbdes.realisasi)}</p>
                                            </div>
                                            <div>
                                                <p className="text-[9px] text-blue-400 font-bold uppercase tracking-widest">Sisa</p>
                                                <p className="text-sm font-black text-green-700">{formatRupiah(selectedApbdes.sisa_anggaran)}</p>
                                            </div>
                                        </div>
                                    </div>
                                )}

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <FormField.Input 
                                        label="Nama Pengeluaran" 
                                        error={errors.nama_pengeluaran}
                                        value={data.nama_pengeluaran} 
                                        onChange={e => setData('nama_pengeluaran', e.target.value)}
                                        placeholder="Nama / deskripsi pengeluaran" 
                                    />
                                    
                                    <FormField.Input 
                                        label="Tanggal Pengeluaran" 
                                        error={errors.tanggal_pengeluaran}
                                        type="date" 
                                        value={data.tanggal_pengeluaran} 
                                        onChange={e => setData('tanggal_pengeluaran', e.target.value)}
                                    />
                                </div>

                                <FormField.Input 
                                    label={`Jumlah (Rp)${sisaAnggaran !== null ? ` — Maks: ${formatRupiah(sisaAnggaran)}` : ''}`} 
                                    error={errors.jumlah}
                                    type="number" 
                                    value={data.jumlah} 
                                    onChange={e => setData('jumlah', e.target.value)}
                                    max={sisaAnggaran ?? undefined} 
                                    min="0" 
                                    placeholder="0"
                                />

                                <FormField.Textarea 
                                    label="Keterangan (Opsional)" 
                                    error={errors.keterangan}
                                    value={data.keterangan} 
                                    onChange={e => setData('keterangan', e.target.value)}
                                    rows={3} 
                                />
                            </FormCard>
                        </div>

                        <div className="sticky top-6 self-start">
                            <button type="submit" disabled={processing || apbdesList.length === 0}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN PENGELUARAN'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
