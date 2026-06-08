import React from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { BIDANG_LIST, SUB_BIDANG, SUMBER_DANA_LIST, BIDANG_COLOR, BIDANG_MAP } from '@/Constants/keuangan';
import { BarChart3, ArrowLeft, Save, Info, FolderOpen, Wallet, AlertTriangle } from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import { PageHeader, FormField, FormCard } from '@/Components/Shared';

// Panduan kode rekening per Bidang/Jenis
const KODE_PANDUAN = {
    pendapatan: { prefix: '4', contoh: '4.1.1.001', deskripsi: 'Format: 4.x.x.xxx (Pendapatan)' },
    belanja:    { prefix: '5', contoh: '5.1.1.001', deskripsi: 'Format: 5.x.x.xxx (Belanja)' },
    pembiayaan: { prefix: '6', contoh: '6.1.1.001', deskripsi: 'Format: 6.x.x.xxx (Pembiayaan)' },
};

export default function Create({ auth, tahunList = [], currentYear }) {
    const { data, setData, post, processing, errors } = useForm({
        tahun:          currentYear ?? new Date().getFullYear(),
        bidang:         '',
        sub_bidang:     '',
        kegiatan:       '',
        jenis:          'belanja',
        sumber_dana:    '',
        kode_rekening:  '',
        nama_rekening:  '',
        anggaran:       '',
        keterangan:     '',
    });

    const subBidangOptions = data.bidang ? (SUB_BIDANG[data.bidang] ?? []) : [];
    const bidangColor      = BIDANG_COLOR[data.bidang] ?? {};
    const kodePanduan      = KODE_PANDUAN[data.jenis] ?? {};

    const handleBidangChange = (val) => {
        setData(prev => ({ ...prev, bidang: Number(val), sub_bidang: '', kegiatan: '' }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.store-tahunan'), {
            onSuccess: () => {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: 'Rekening APBDes berhasil ditambahkan.',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: { popup: 'rounded-3xl' },
                });
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Rekening APBDes">
            <Head title="Tambah Rekening APBDes" />

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title="Tambah Rekening APBDes"
                    subtitle="Sesuai Permendagri No. 20 Tahun 2018"
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

                {errors.error && (
                    <div className="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start gap-3">
                        <AlertTriangle className="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                        <div>
                            <p className="text-xs font-black text-red-700 uppercase tracking-tighter">Gagal Menyimpan</p>
                            <p className="text-[10px] font-bold text-red-600 mt-0.5 leading-relaxed">{errors.error}</p>
                        </div>
                    </div>
                )}

                <form onSubmit={handleSubmit}>
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {/* ── Main Form ─────────────────────── */}
                        <div className="lg:col-span-2 space-y-6">

                            {/* Seksi 1: Klasifikasi APBDes */}
                            <FormCard title="Klasifikasi APBDes (Permendagri 20/2018)" icon={FolderOpen} bodyClass="p-6 sm:p-8 space-y-5">

                                {/* Bidang */}
                                <FormField label="Bidang" required error={errors.bidang}>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                        {BIDANG_LIST.map(b => {
                                            const cfg = BIDANG_COLOR[b.value];
                                            const isSelected = Number(data.bidang) === b.value;
                                            return (
                                                <button
                                                    key={b.value}
                                                    type="button"
                                                    onClick={() => handleBidangChange(b.value)}
                                                    className={cn(
                                                        'text-left p-3 rounded-xl border-2 transition-all text-[9px] font-black uppercase tracking-wider leading-tight',
                                                        isSelected
                                                            ? `${cfg.border} ${cfg.bg} ${cfg.text} shadow-sm`
                                                            : 'border-gray-100 bg-gray-50 text-gray-500 hover:border-gray-200'
                                                    )}
                                                >
                                                    <span className={cn('block text-[8px] mb-0.5 font-black', isSelected ? cfg.text : 'text-gray-400')}>Bidang {b.value}</span>
                                                    {BIDANG_MAP[b.value]}
                                                </button>
                                            );
                                        })}
                                    </div>
                                    {!errors.bidang && <p className="text-[9px] font-bold text-gray-400 mt-1 ml-1">5 Bidang sesuai Permendagri No. 20 Tahun 2018</p>}
                                </FormField>

                                {/* Sub-Bidang — muncul setelah Bidang dipilih */}
                                {data.bidang && (
                                    <div className="animate-in slide-in-from-top-2 duration-200">
                                        <FormField.Select 
                                            label="Sub-Bidang" 
                                            error={errors.sub_bidang}
                                            value={data.sub_bidang}
                                            onChange={e => setData('sub_bidang', e.target.value)}
                                            options={subBidangOptions}
                                            placeholder="-- Pilih Sub-Bidang (Opsional) --"
                                        />
                                        {!errors.sub_bidang && <p className="text-[9px] font-bold text-gray-400 mt-1 ml-1">Opsional — pilih sub-bidang sesuai kegiatan</p>}
                                    </div>
                                )}

                                {/* Nama Kegiatan */}
                                <FormField.Input 
                                    label="Nama Kegiatan" 
                                    error={errors.kegiatan} 
                                    value={data.kegiatan}
                                    onChange={e => setData('kegiatan', e.target.value)}
                                    placeholder="Contoh: Penyediaan Penghasilan Tetap Perangkat Desa"
                                />
                                {!errors.kegiatan && <p className="-mt-3 text-[9px] font-bold text-gray-400 ml-1">Opsional — spesifikasi kegiatan dalam sub-bidang</p>}

                            </FormCard>

                            {/* Seksi 2: Detail Rekening */}
                            <FormCard title="Detail Rekening & Anggaran" icon={Wallet} bodyClass="p-6 sm:p-8 space-y-5">

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <FormField.Select 
                                        label="Tahun Anggaran" 
                                        required 
                                        error={errors.tahun}
                                        value={data.tahun} 
                                        onChange={e => setData('tahun', e.target.value)}
                                    >
                                        {(tahunList.length ? tahunList : [currentYear]).map(t => (
                                            <option key={t} value={t}>{t}</option>
                                        ))}
                                        {!tahunList.includes(currentYear) && (
                                            <option value={currentYear}>{currentYear} (Baru)</option>
                                        )}
                                    </FormField.Select>

                                    <FormField.Select 
                                        label="Jenis Rekening" 
                                        required 
                                        error={errors.jenis}
                                        value={data.jenis} 
                                        onChange={e => setData('jenis', e.target.value)}
                                        options={[
                                            { value: 'pendapatan', label: 'Pendapatan' },
                                            { value: 'belanja', label: 'Belanja' },
                                            { value: 'pembiayaan', label: 'Pembiayaan' }
                                        ]}
                                    />
                                </div>

                                {/* Sumber Dana — grouped optgroup */}
                                <FormField label="Sumber Dana" required error={errors.sumber_dana}>
                                    <select value={data.sumber_dana} onChange={e => setData('sumber_dana', e.target.value)}
                                        className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all appearance-none cursor-pointer">
                                        <option value="">-- Pilih Sumber Dana --</option>
                                        {SUMBER_DANA_LIST.map(group => (
                                            <optgroup key={group.group} label={group.group}>
                                                {group.options.map(opt => (
                                                    <option key={opt.value} value={opt.value}>{opt.label}</option>
                                                ))}
                                            </optgroup>
                                        ))}
                                    </select>
                                </FormField>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div className="space-y-1.5">
                                        <FormField.Input 
                                            label="Kode Rekening" 
                                            required 
                                            error={errors.kode_rekening}
                                            value={data.kode_rekening}
                                            onChange={e => setData('kode_rekening', e.target.value)}
                                            placeholder={kodePanduan.contoh ?? '5.1.1.001'}
                                            inputClassName="font-mono"
                                        />
                                        {!errors.kode_rekening && <p className="text-[9px] font-bold text-gray-400 ml-1">{kodePanduan.deskripsi}</p>}
                                    </div>

                                    <FormField.Input 
                                        label="Jumlah Anggaran (Rp)" 
                                        required 
                                        type="number"
                                        error={errors.anggaran}
                                        value={data.anggaran}
                                        onChange={e => setData('anggaran', e.target.value)}
                                        placeholder="0" 
                                        min="0"
                                    />
                                </div>

                                <FormField.Input 
                                    label="Nama / Uraian Rekening" 
                                    required 
                                    error={errors.nama_rekening}
                                    value={data.nama_rekening}
                                    onChange={e => setData('nama_rekening', e.target.value)}
                                    placeholder="Contoh: Belanja Pegawai"
                                />

                                <FormField.Textarea 
                                    label="Keterangan (Opsional)" 
                                    error={errors.keterangan}
                                    value={data.keterangan} 
                                    onChange={e => setData('keterangan', e.target.value)}
                                    rows={2} 
                                />
                            </FormCard>
                        </div>

                        {/* ── Sidebar ────────────────────────── */}
                        <div className="space-y-4 sticky top-6 self-start">
                            {/* Preview Klasifikasi */}
                            {data.bidang && (
                                <div className={cn('rounded-2xl border p-5 space-y-3 animate-in fade-in duration-300', bidangColor.border ?? 'border-gray-100', bidangColor.bg ?? 'bg-gray-50')}>
                                    <p className={cn('text-[9px] font-black uppercase tracking-widest', bidangColor.text ?? 'text-gray-500')}>Klasifikasi Terpilih</p>
                                    <div className="space-y-1.5">
                                        <p className="text-xs font-black text-gray-900">Bidang {data.bidang}</p>
                                        <p className="text-[10px] font-bold text-gray-600">{BIDANG_LIST.find(b => b.value === Number(data.bidang))?.label}</p>
                                        {data.sub_bidang && <p className="text-[9px] font-bold text-gray-500">{data.sub_bidang} — {subBidangOptions.find(s => s.value === data.sub_bidang)?.label?.split('—')[1]?.trim()}</p>}
                                        {data.kegiatan && <p className="text-[9px] font-bold text-gray-500 italic">{data.kegiatan}</p>}
                                    </div>
                                </div>
                            )}

                            {/* Panduan */}
                            <div className="bg-green-50 rounded-2xl border border-green-100 p-5">
                                <div className="flex items-center gap-2 mb-3">
                                    <Info className="w-4 h-4 text-green-600 shrink-0" />
                                    <h3 className="text-xs font-black text-green-800 uppercase italic tracking-tighter">Panduan</h3>
                                </div>
                                <ul className="space-y-1.5 text-[9px] font-bold text-green-700 uppercase tracking-wider leading-relaxed">
                                    <li>• Bidang wajib diisi sesuai struktur APBDes</li>
                                    <li>• Kode rekening Pendapatan: 4.x.x.xxx</li>
                                    <li>• Kode rekening Belanja: 5.x.x.xxx</li>
                                    <li>• Kode rekening Pembiayaan: 6.x.x.xxx</li>
                                    <li>• ADD berasal dari APBD Kabupaten, bukan dari Pusat</li>
                                    <li>• Rekening baru otomatis berstatus Disetujui</li>
                                </ul>
                            </div>

                            <button type="submit" disabled={processing}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
                                <Save className="w-4 h-4" />
                                {processing ? 'MENYIMPAN...' : 'SIMPAN REKENING'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
