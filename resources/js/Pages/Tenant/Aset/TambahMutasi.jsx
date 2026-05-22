import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { TrendingUp, TrendingDown, Save, AlertTriangle } from 'lucide-react';

const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n ?? 0);
const fmtQty = (n) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n ?? 0);

const JENIS_OPTIONS = [
    { value: 'tambah', label: '➕ Bertambah — beli baru, hibah, pengadaan' },
    { value: 'kurang', label: '➖ Berkurang — rusak total, dijual, hilang' },
];

const KONDISI_OPTIONS = [
    { value: 'baik',         label: '✅ Baik' },
    { value: 'rusak_ringan', label: '⚠️ Rusak Ringan' },
    { value: 'rusak_berat',  label: '❌ Rusak Berat' },
];

export default function TambahMutasi({ auth, inventaris, tahun, semester }) {
    const { data, setData, post, processing, errors } = useForm({
        tahun:      tahun,
        semester:   semester,
        tanggal:    '',   // user isi tanggal kejadian mutasi
        jenis:      'tambah',
        kwantitas:  '',
        nilai:      '',
        keterangan: '',
        kondisi:    '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('aset.mutasi.store', inventaris.id));
    };

    // Auto-detect tahun & semester dari tanggal
    const handleTanggalChange = (val) => {
        const updates = { tanggal: val };
        if (val) {
            const d = new Date(val);
            if (!isNaN(d)) {
                updates.tahun    = d.getFullYear();
                updates.semester = d.getMonth() < 6 ? 1 : 2;
            }
        }
        setData((prev) => ({ ...prev, ...updates }));
    };

    const isTambah = data.jenis === 'tambah';
    const saldoBaru = isTambah
        ? inventaris.saldo_kwantitas + (parseFloat(data.kwantitas) || 0)
        : inventaris.saldo_kwantitas - (parseFloat(data.kwantitas) || 0);
    const nilaiSaldoBaru = isTambah
        ? inventaris.saldo_nilai + (parseFloat(data.nilai) || 0)
        : inventaris.saldo_nilai - (parseFloat(data.nilai) || 0);
    const isOverSaldo = !isTambah && (parseFloat(data.kwantitas) || 0) > inventaris.saldo_kwantitas;

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Mutasi Aset">
            <Head title="Tambah Mutasi Aset" />

            <div className="space-y-5 animate-in fade-in duration-500 pb-20">

                {/* PageHeader */}
                <PageHeader
                    icon={isTambah ? TrendingUp : TrendingDown}
                    title="Tambah Mutasi Aset"
                    subtitle={inventaris.nama_display}
                    backHref={route('aset.inventaris.index', { tahun, semester })}
                />

                {/* Info aset saat ini */}
                <div className="bg-gray-900 rounded-3xl p-6 grid grid-cols-3 gap-4">
                    <div className="text-center">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Kode Barang</p>
                        <p className="text-white font-mono font-bold text-sm">{inventaris.barang?.kode_barang ?? '-'}</p>
                    </div>
                    <div className="text-center border-x border-white/10">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                        <p className="text-white font-black text-xl">{fmtQty(inventaris.saldo_kwantitas)}</p>
                        <p className="text-gray-400 text-xs">{inventaris.satuan}</p>
                    </div>
                    <div className="text-center">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Nilai Saat Ini</p>
                        <p className="text-yellow-300 font-black text-sm">{fmt(inventaris.saldo_nilai)}</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-5">

                    {/* Jenis Mutasi */}
                    <FormCard icon={isTambah ? TrendingUp : TrendingDown} title="Jenis Mutasi">
                        <div className="grid grid-cols-2 gap-3 mb-4">
                            {[
                                { val: 'tambah', icon: TrendingUp,   label: 'Bertambah', color: 'green' },
                                { val: 'kurang', icon: TrendingDown, label: 'Berkurang', color: 'red'   },
                            ].map(({ val, icon: Icon, label, color }) => (
                                <button
                                    key={val}
                                    type="button"
                                    onClick={() => setData('jenis', val)}
                                    className={`flex items-center gap-3 p-4 rounded-2xl border-2 transition-all text-left ${
                                        data.jenis === val
                                            ? color === 'green'
                                                ? 'border-green-500 bg-green-50'
                                                : 'border-red-500 bg-red-50'
                                            : 'border-gray-100 hover:border-gray-200'
                                    }`}
                                >
                                    <Icon className={`w-5 h-5 ${data.jenis === val ? (color === 'green' ? 'text-green-600' : 'text-red-600') : 'text-gray-400'}`} />
                                    <span className={`text-xs font-black uppercase tracking-widest ${data.jenis === val ? (color === 'green' ? 'text-green-700' : 'text-red-700') : 'text-gray-500'}`}>
                                        {label}
                                    </span>
                                </button>
                            ))}
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {/* Tanggal dulu — auto-detect tahun & semester */}
                            <div className="sm:col-span-2">
                                <FormField label="Tanggal Kejadian Mutasi" required error={errors.tanggal}>
                                    <input
                                        type="date"
                                        value={data.tanggal}
                                        onChange={(e) => handleTanggalChange(e.target.value)}
                                        className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                                    />
                                    <p className="text-[10px] text-gray-400 font-semibold ml-1 mt-1">
                                        {isTambah ? 'Tanggal barang diterima/dibeli.' : 'Tanggal barang rusak/hilang/dijual.'} Tahun & semester otomatis terdeteksi.
                                    </p>
                                </FormField>
                            </div>

                            <FormField label="Tahun" required error={errors.tahun}>
                                <input
                                    type="number"
                                    min="1945"
                                    max={new Date().getFullYear() + 1}
                                    value={data.tahun}
                                    onChange={(e) => setData('tahun', Number(e.target.value))}
                                    className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all"
                                    placeholder="2025"
                                />
                            </FormField>

                            <FormField.Select
                                label="Semester" required
                                value={data.semester}
                                onChange={(e) => setData('semester', Number(e.target.value))}
                                error={errors.semester}
                                options={[
                                    { value: 1, label: 'Semester 1 (Jan–Jun)' },
                                    { value: 2, label: 'Semester 2 (Jul–Des)' },
                                ]}
                            />
                        </div>
                    </FormCard>

                    {/* Jumlah */}
                    <FormCard icon={isTambah ? TrendingUp : TrendingDown}
                        title={isTambah ? 'Jumlah Penambahan' : 'Jumlah Pengurangan'}>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <FormField label={`Kwantitas (${inventaris.satuan})`} required error={errors.kwantitas}>
                                <input
                                    type="number" step="0.01" min="0.01"
                                    value={data.kwantitas}
                                    onChange={(e) => setData('kwantitas', e.target.value)}
                                    className={`w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold outline-none focus:ring-4 transition-all ${
                                        isOverSaldo
                                            ? 'border-red-400 focus:ring-red-500/10 focus:border-red-500'
                                            : 'border-gray-100 focus:ring-green-500/10 focus:border-green-500'
                                    }`}
                                    placeholder="0"
                                />
                                {isOverSaldo && (
                                    <p className="text-[10px] font-bold text-red-600 uppercase tracking-tight ml-1 flex items-center gap-1 mt-1">
                                        <AlertTriangle className="w-3 h-3" />
                                        Melebihi saldo ({fmtQty(inventaris.saldo_kwantitas)} {inventaris.satuan})
                                    </p>
                                )}
                            </FormField>

                            <FormField.Input
                                label="Nilai (Rp)" required
                                type="number" step="1" min="0"
                                value={data.nilai}
                                onChange={(e) => setData('nilai', e.target.value)}
                                error={errors.nilai}
                                placeholder="0"
                            />

                            <div className="sm:col-span-2">
                                <FormField.Input
                                    label="Keterangan / Alasan"
                                    placeholder={isTambah ? 'Pengadaan APBDes 2025, Hibah dari Kabupaten...' : 'Rusak total, dijual, hilang, disumbangkan...'}
                                    value={data.keterangan}
                                    onChange={(e) => setData('keterangan', e.target.value)}
                                    error={errors.keterangan}
                                />
                            </div>

                            {/* Update kondisi hanya saat berkurang */}
                            {!isTambah && (
                                <div className="sm:col-span-2">
                                    <FormField.Select
                                        label="Update Kondisi Aset (opsional)"
                                        value={data.kondisi}
                                        onChange={(e) => setData('kondisi', e.target.value)}
                                        error={errors.kondisi}
                                        placeholder="Biarkan kondisi saat ini"
                                        options={KONDISI_OPTIONS}
                                    />
                                </div>
                            )}
                        </div>
                    </FormCard>

                    {/* Preview saldo baru */}
                    {(data.kwantitas || data.nilai) && (
                        <div className={`rounded-3xl p-5 border-2 ${isTambah ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'}`}>
                            <p className={`text-[10px] font-black uppercase tracking-widest mb-3 ${isTambah ? 'text-green-600' : 'text-red-600'}`}>
                                Preview Saldo Setelah Mutasi
                            </p>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Kwantitas Baru</p>
                                    <p className={`font-black text-2xl ${isOverSaldo ? 'text-red-600' : isTambah ? 'text-green-700' : 'text-red-700'}`}>
                                        {fmtQty(Math.max(0, saldoBaru))} {inventaris.satuan}
                                    </p>
                                </div>
                                <div>
                                    <p className="text-gray-400 text-[10px] font-bold uppercase tracking-widest">Nilai Baru</p>
                                    <p className={`font-black text-lg ${isTambah ? 'text-green-700' : 'text-red-700'}`}>
                                        {fmt(Math.max(0, nilaiSaldoBaru))}
                                    </p>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Tombol */}
                    <div className="flex gap-3">
                        <Link href={route('aset.inventaris.index', { tahun, semester })}
                            className="flex-1 sm:flex-none px-8 py-3 bg-gray-100 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all text-center">
                            Batal
                        </Link>
                        <button type="submit" disabled={processing || isOverSaldo}
                            className={`flex-1 sm:flex-none flex items-center justify-center gap-2 px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest disabled:opacity-60 transition-all shadow-lg text-white ${
                                isTambah ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'
                            }`}>
                            <Save className="w-4 h-4" />
                            {processing ? 'Menyimpan...' : isTambah ? 'Catat Penambahan' : 'Catat Pengurangan'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}

