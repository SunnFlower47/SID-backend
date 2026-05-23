import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import AnggaranProgressBar from '@/Components/Keuangan/AnggaranProgressBar';
import { BIDANG_LIST, BIDANG_COLOR, BIDANG_MAP, SUB_BIDANG, SUMBER_DANA_LIST } from '@/Constants/keuangan';
import { BarChart3, ArrowLeft, Save, AlertTriangle, FolderOpen, Wallet } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import { PageHeader, FormField, FormCard } from '@/Components/Shared';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function Edit({ auth, apbdes }) {
    const { data, setData, put, processing, errors } = useForm({
        bidang:        apbdes.bidang        ?? '',
        sub_bidang:    apbdes.sub_bidang    ?? '',
        kegiatan:      apbdes.kegiatan      ?? '',
        kode_rekening: apbdes.kode_rekening ?? '',
        nama_rekening: apbdes.nama_rekening ?? '',
        jenis:         apbdes.jenis         ?? 'pendapatan',
        sumber_dana:   apbdes.sumber_dana   ?? '',
        anggaran:      apbdes.anggaran      ?? '',
        keterangan:    apbdes.keterangan    ?? '',
    });

    const subBidangOptions = data.bidang ? (SUB_BIDANG[data.bidang] ?? []) : [];
    const bidangColor      = BIDANG_COLOR[data.bidang] ?? {};

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('anggaran.update-apbdes', apbdes.id));
    };

    const isBelowRealisasi = Number(data.anggaran) < Number(apbdes.realisasi);

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Rekening APBDes">
            <Head title="Edit Rekening APBDes" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title={apbdes.nama_rekening}
                    subtitle={`Edit Rekening APBDes · ${apbdes.kode_rekening}`}
                    icon={BarChart3}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('transparansi-desa.apbdes'),
                            variant: 'ghost'
                        }
                    ]}
                />

                {/* Budget Warning */}
                {isBelowRealisasi && (
                    <div className="bg-red-50 border border-red-100 rounded-2xl p-4 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-red-700 uppercase tracking-tighter italic">Peringatan: Anggaran Lebih Kecil dari Realisasi</p>
                            <p className="text-[10px] font-bold text-red-500 uppercase tracking-wider mt-0.5">
                                Anggaran tidak boleh kurang dari realisasi yang sudah ada ({formatRupiah(apbdes.realisasi)})
                            </p>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* Main Form */}
                        <div className="lg:col-span-2 space-y-6">
                            
                            <FormCard title="Bidang (Permendagri 20/2018)" icon={FolderOpen} bodyClass="p-6 sm:p-8 space-y-6">
                                <FormField label="Bidang" required error={errors.bidang}>
                                    <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                        {BIDANG_LIST.map(b => {
                                            const cfg = BIDANG_COLOR[b.value];
                                            const isSelected = Number(data.bidang) === b.value;
                                            return (
                                                <button key={b.value} type="button"
                                                    onClick={() => setData(prev => ({ ...prev, bidang: b.value, sub_bidang: '' }))}
                                                    className={cn(
                                                        'text-left p-3 rounded-xl border-2 transition-all text-[9px] font-black uppercase tracking-wider leading-tight',
                                                        isSelected ? `${cfg.border} ${cfg.bg} ${cfg.text} shadow-sm` : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200'
                                                    )}>
                                                    <span className={cn('block text-[8px] mb-0.5 font-black', isSelected ? cfg.text : 'text-gray-400')}>Bidang {b.value}</span>
                                                    {BIDANG_MAP[b.value]}
                                                </button>
                                            );
                                        })}
                                    </div>
                                </FormField>

                                {/* Sub-Bidang */}
                                {data.bidang && (
                                    <div className="animate-in slide-in-from-top-1 duration-200">
                                        <FormField.Select 
                                            label="Sub-Bidang" 
                                            error={errors.sub_bidang}
                                            value={data.sub_bidang} 
                                            onChange={e => setData('sub_bidang', e.target.value)}
                                            options={subBidangOptions}
                                            placeholder="-- Pilih Sub-Bidang (Opsional) --"
                                        />
                                    </div>
                                )}

                                {/* Kegiatan */}
                                <FormField.Input 
                                    label="Nama Kegiatan" 
                                    error={errors.kegiatan}
                                    value={data.kegiatan} 
                                    onChange={e => setData('kegiatan', e.target.value)}
                                    placeholder="Opsional — nama spesifik kegiatan"
                                />
                            </FormCard>

                            <FormCard title="Detail Rekening" icon={Wallet} bodyClass="p-6 sm:p-8 space-y-6">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <FormField.Select 
                                        label="Jenis Rekening" 
                                        error={errors.jenis}
                                        value={data.jenis}
                                        onChange={e => setData('jenis', e.target.value)}
                                        options={[
                                            { value: 'pendapatan', label: 'Pendapatan' },
                                            { value: 'belanja', label: 'Belanja' },
                                            { value: 'pembiayaan', label: 'Pembiayaan' }
                                        ]}
                                    />

                                    <FormField label="Sumber Dana" error={errors.sumber_dana}>
                                        <select value={data.sumber_dana} onChange={e => setData('sumber_dana', e.target.value)}
                                            className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all appearance-none cursor-pointer">
                                            <option value="">-- Pilih Sumber Dana --</option>
                                            {SUMBER_DANA_LIST.map(group => (
                                                <optgroup key={group.group} label={group.group}>
                                                    {group.options.map(opt => <option key={opt.value} value={opt.value}>{opt.label}</option>)}
                                                </optgroup>
                                            ))}
                                        </select>
                                    </FormField>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <FormField.Input 
                                        label="Kode Rekening" 
                                        error={errors.kode_rekening}
                                        value={data.kode_rekening} 
                                        onChange={e => setData('kode_rekening', e.target.value)}
                                        inputClassName="font-mono"
                                    />

                                    <FormField.Input 
                                        label="Jumlah Anggaran (Rp)" 
                                        error={errors.anggaran}
                                        type="number" 
                                        value={data.anggaran} 
                                        onChange={e => setData('anggaran', e.target.value)}
                                        min={apbdes.realisasi} 
                                        inputClassName={cn(isBelowRealisasi && 'border-red-300 bg-red-50')}
                                    />
                                </div>

                                <FormField.Input 
                                    label="Nama Rekening" 
                                    error={errors.nama_rekening}
                                    value={data.nama_rekening} 
                                    onChange={e => setData('nama_rekening', e.target.value)}
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

                        {/* Sidebar: Current Status */}
                        <div className="space-y-4 sticky top-6 self-start">
                            <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
                                <h3 className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">Status Saat Ini</h3>
                                <div className="space-y-2 text-sm">
                                    <div className="flex justify-between py-2 border-b border-gray-50">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tahun</span>
                                        <span className="text-xs font-black text-gray-900">{apbdes.tahun}</span>
                                    </div>
                                    <div className="flex justify-between py-2 border-b border-gray-50">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Realisasi</span>
                                        <span className="text-xs font-black text-blue-600">{formatRupiah(apbdes.realisasi)}</span>
                                    </div>
                                    <div className="flex justify-between py-2">
                                        <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sisa</span>
                                        <span className="text-xs font-black text-green-600">{formatRupiah(apbdes.sisa_anggaran)}</span>
                                    </div>
                                </div>
                                <AnggaranProgressBar anggaran={Number(apbdes.anggaran)} realisasi={Number(apbdes.realisasi)} height="h-2" />
                            </div>

                            <button
                                type="submit"
                                disabled={processing || isBelowRealisasi}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN PERUBAHAN'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
