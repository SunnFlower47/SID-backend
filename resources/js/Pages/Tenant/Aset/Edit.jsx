import React, { useState, useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Package, Save, Info } from 'lucide-react';

const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n ?? 0);
const fmtQty = (n) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n ?? 0);

export default function Edit({ auth, inventaris, kategoris }) {
    const [selectedKategori, setSelectedKategori] = useState(
        inventaris.barang?.kategori?.id?.toString() ?? ''
    );
    const [barangOptions, setBarangOptions] = useState([]);

    useEffect(() => {
        if (selectedKategori) {
            const kat = kategoris.find((k) => k.id === Number(selectedKategori));
            setBarangOptions(kat?.barangs ?? []);
        }
    }, [selectedKategori, kategoris]);

    const { data, setData, put, processing, errors } = useForm({
        aset_barang_id:        inventaris.aset_barang_id ?? '',
        nama_barang_override:  inventaris.nama_barang_override ?? '',
        satuan:                inventaris.satuan ?? '',
        kondisi:               inventaris.kondisi ?? 'baik',
        lokasi:                inventaris.lokasi ?? '',
        tanggal_perolehan:     inventaris.tanggal_perolehan ?? '',
        asal_usul:             inventaris.asal_usul ?? 'APBDes',
        keterangan:            inventaris.keterangan ?? '',
    });

    const handleKategoriChange = (val) => {
        setSelectedKategori(val);
        const kat = kategoris.find((k) => k.id === Number(val));
        setBarangOptions(kat?.barangs ?? []);
        setData('aset_barang_id', '');
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('aset.inventaris.update', inventaris.id));
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Data Aset">
            <Head title="Edit Data Aset" />

            {/* Lebar sama dengan Index */}
            <div className="space-y-5 animate-in fade-in duration-500 pb-20">

                <PageHeader
                    icon={Package}
                    title="Edit Data Aset"
                    subtitle={inventaris.nama_display ?? inventaris.nama_barang_override}
                    backHref={route('aset.inventaris.index')}
                />

                {/* Info saldo saat ini */}
                <div className="bg-gray-900 rounded-3xl p-5 grid grid-cols-3 gap-4">
                    <div className="text-center">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Kode Tipe</p>
                        <p className="text-white font-mono font-bold text-sm">{inventaris.barang?.kode_barang ?? '-'}</p>
                    </div>
                    <div className="text-center border-x border-white/10">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Saldo Saat Ini</p>
                        <p className="text-white font-black text-xl">{fmtQty(inventaris.saldo_kwantitas)} <span className="text-gray-400 text-xs">{inventaris.satuan}</span></p>
                    </div>
                    <div className="text-center">
                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Nilai Saat Ini</p>
                        <p className="text-yellow-300 font-black text-sm">{fmt(inventaris.saldo_nilai)}</p>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="space-y-5">

                    {/* ── Tipe / Kode Barang ────────────────────────────────── */}
                    <FormCard icon={Package} title="Tipe / Kode Barang (Referensi)">
                        <div className="flex gap-3 p-3 bg-blue-50 border border-blue-100 rounded-2xl mb-4">
                            <Info className="w-4 h-4 text-blue-500 shrink-0 mt-0.5" />
                            <p className="text-xs text-blue-700 font-semibold">
                                Kode merujuk pada <b>tipe/jenis</b> barang. Nama spesifik aset milik desa diisi di bawah.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <FormField label="Golongan Aset" required>
                                <select value={selectedKategori} onChange={(e) => handleKategoriChange(e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all">
                                    <option value="">Pilih golongan...</option>
                                    {kategoris.map((k) => (
                                        <option key={k.id} value={k.id}>{k.kode} — {k.nama}</option>
                                    ))}
                                </select>
                            </FormField>

                            <FormField label="Kode & Tipe Barang" required error={errors.aset_barang_id}>
                                <select value={data.aset_barang_id} onChange={(e) => setData('aset_barang_id', e.target.value)}
                                    className="w-full px-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all">
                                    <option value="">Pilih tipe barang...</option>
                                    {barangOptions.map((b) => (
                                        <option key={b.id} value={b.id}>{b.kode_barang} — {b.nama_barang}</option>
                                    ))}
                                </select>
                                {inventaris.barang && !selectedKategori && (
                                    <p className="text-[10px] text-gray-400 font-mono ml-1 mt-1">
                                        Saat ini: <span className="text-green-600 font-bold">{inventaris.barang.kode_barang}</span> — {inventaris.barang.nama_barang}
                                    </p>
                                )}
                            </FormField>
                        </div>
                    </FormCard>

                    {/* ── Nama & Identitas Spesifik ──────────────────────────── */}
                    <FormCard icon={Package} title="Nama & Identitas Aset Desa">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div className="sm:col-span-2">
                                <FormField.Input
                                    label="Nama Spesifik Aset"
                                    required
                                    placeholder="Contoh: Laptop Dell Inspiron 15, Speaker Yamaha DBR10..."
                                    value={data.nama_barang_override}
                                    onChange={(e) => setData('nama_barang_override', e.target.value)}
                                    error={errors.nama_barang_override}
                                />
                            </div>

                            <FormField.Input
                                label="Satuan" required
                                value={data.satuan}
                                onChange={(e) => setData('satuan', e.target.value)}
                                error={errors.satuan}
                            />

                            <FormField.Select
                                label="Kondisi" required
                                value={data.kondisi}
                                onChange={(e) => setData('kondisi', e.target.value)}
                                error={errors.kondisi}
                                options={[
                                    { value: 'baik',         label: '✅ Baik' },
                                    { value: 'rusak_ringan', label: '⚠️ Rusak Ringan' },
                                    { value: 'rusak_berat',  label: '❌ Rusak Berat' },
                                ]}
                            />

                            <FormField.Input
                                label="Lokasi Aset"
                                placeholder="Kantor Desa, Balai Pertemuan..."
                                value={data.lokasi}
                                onChange={(e) => setData('lokasi', e.target.value)}
                                error={errors.lokasi}
                            />

                            <FormField.Select
                                label="Asal Usul / Sumber" required
                                value={data.asal_usul}
                                onChange={(e) => setData('asal_usul', e.target.value)}
                                error={errors.asal_usul}
                                options={[
                                    { value: 'APBDes',             label: 'APBDes (Anggaran Desa)' },
                                    { value: 'Hibah',              label: 'Hibah' },
                                    { value: 'Aset Asli Desa',     label: 'Aset Asli Desa' },
                                    { value: 'Bantuan Pemerintah', label: 'Bantuan Pemerintah' },
                                    { value: 'Lainnya',            label: 'Lainnya' },
                                ]}
                            />

                            <FormField.Input
                                label="Tanggal Perolehan"
                                type="date"
                                value={data.tanggal_perolehan}
                                onChange={(e) => setData('tanggal_perolehan', e.target.value)}
                                error={errors.tanggal_perolehan}
                            />

                            <div className="sm:col-span-2">
                                <FormField.Textarea
                                    label="Keterangan"
                                    placeholder="Nomor seri, spesifikasi teknis, catatan lain..."
                                    value={data.keterangan}
                                    onChange={(e) => setData('keterangan', e.target.value)}
                                    rows={2}
                                />
                            </div>
                        </div>
                    </FormCard>

                    <div className="flex gap-3 justify-end">
                        <Link href={route('aset.inventaris.index')}
                            className="px-8 py-3 bg-gray-100 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                            Batal
                        </Link>
                        <button type="submit" disabled={processing}
                            className="flex items-center gap-2 px-8 py-3 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 disabled:opacity-60 transition-all shadow-lg">
                            <Save className="w-4 h-4" />
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
