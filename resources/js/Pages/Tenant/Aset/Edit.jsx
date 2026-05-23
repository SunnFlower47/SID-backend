import React, { useState, useEffect } from 'react';
import { Head, Link, useForm, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Package, Save, Info, TrendingUp, TrendingDown, FileText, Gavel, Trash2 } from 'lucide-react';
import Swal from 'sweetalert2';

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

    const handleDeleteMutasi = (mutasi) => {
        Swal.fire({
            title: 'Hapus Mutasi?',
            html: `Hapus mutasi tanggal <b>${mutasi.tanggal ? new Date(mutasi.tanggal).toLocaleDateString('id-ID') : '-'}</b>?<br><small class="text-red-500">Jika mutasi ini berkurang, Berita Acara & SK terkait juga akan dihapus!</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#f3f4f6',
            confirmButtonText: 'YA, HAPUS',
            cancelButtonText: 'BATAL',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-2xl font-black text-xs uppercase tracking-widest',
                cancelButton: 'rounded-2xl font-black text-xs uppercase tracking-widest text-gray-500'
            },
        }).then((res) => {
            if (res.isConfirmed) {
                router.delete(route('aset.mutasi.destroy', mutasi.id), { preserveScroll: true });
            }
        });
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

                {/* ── Riwayat Mutasi Aset ────────────────────────────────── */}
                <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden p-6 space-y-4">
                    <div className="flex items-center justify-between flex-wrap gap-2">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                                <TrendingUp className="w-5 h-5" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black uppercase tracking-widest text-gray-800">Riwayat Mutasi & Penghapusan</h3>
                                <p className="text-[10px] font-semibold text-gray-400">Log transaksi penambahan, pengurangan, dan dokumen resmi aset</p>
                            </div>
                        </div>
                        <Link
                            href={route('aset.mutasi.create', { inventaris: inventaris.id })}
                            className="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                        >
                            Catat Mutasi Baru
                        </Link>
                    </div>

                    {(!inventaris.mutasis || inventaris.mutasis.length === 0) ? (
                        <p className="text-xs text-gray-400 text-center py-6 font-medium">Belum ada riwayat mutasi untuk aset ini.</p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-xs min-w-[768px]">
                                <thead>
                                    <tr className="bg-gray-50/50 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        <th className="px-4 py-3 text-left">Tanggal</th>
                                        <th className="px-4 py-3 text-center">Periode</th>
                                        <th className="px-4 py-3 text-center">Jenis</th>
                                        <th className="px-4 py-3 text-right">Kwantitas</th>
                                        <th className="px-4 py-3 text-right">Nilai (Rp)</th>
                                        <th className="px-4 py-3 text-left">Keterangan / Alasan</th>
                                        <th className="px-4 py-3 text-center">Dokumen / Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {inventaris.mutasis.map((m) => {
                                        const isTambah = m.jenis === 'tambah';
                                        return (
                                            <tr key={m.id} className="border-b border-gray-50 hover:bg-gray-50/30 transition-colors">
                                                <td className="px-4 py-3 font-semibold text-gray-700">
                                                    {m.tanggal ? new Date(m.tanggal).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}
                                                </td>
                                                <td className="px-4 py-3 text-center font-bold text-gray-500">
                                                    Tahun {m.tahun} SM {m.semester}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <span className={`inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider ${
                                                        isTambah ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
                                                    }`}>
                                                        {isTambah ? '➕ Bertambah' : '➖ Berkurang'}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-right font-mono font-bold text-gray-700">
                                                    {isTambah ? '+' : '-'}{fmtQty(m.kwantitas)}
                                                </td>
                                                <td className="px-4 py-3 text-right font-mono font-bold text-gray-700">
                                                    {fmt(m.nilai)}
                                                </td>
                                                <td className="px-4 py-3 text-gray-600 max-w-[200px] truncate" title={m.keterangan}>
                                                    {m.keterangan ?? '-'}
                                                </td>
                                                <td className="px-4 py-3 text-center">
                                                    <div className="flex items-center justify-center gap-2">
                                                        {/* Dokumen jika berkurang */}
                                                        {!isTambah && m.berita_acara_surat_id && (
                                                            <a
                                                                href={route('admin.surat-pengajuan.pdf', m.berita_acara_surat_id)}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-[9px] font-bold uppercase tracking-wider transition-all"
                                                                title="Cetak Berita Acara Penghapusan"
                                                            >
                                                                <FileText className="w-3.5 h-3.5" /> BAPA
                                                            </a>
                                                        )}
                                                        {!isTambah && m.sk_surat_id && (
                                                            <a
                                                                href={route('admin.surat-pengajuan.pdf', m.sk_surat_id)}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-600 rounded-xl text-[9px] font-bold uppercase tracking-wider transition-all"
                                                                title="Cetak SK Penghapusan Aset"
                                                            >
                                                                <Gavel className="w-3.5 h-3.5" /> SKPA
                                                            </a>
                                                        )}
                                                        {/* Tombol Hapus Mutasi (hanya jika bukan mutasi pertama/awal) */}
                                                        <button
                                                            type="button"
                                                            onClick={() => handleDeleteMutasi(m)}
                                                            className="p-1 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-lg transition-all"
                                                            title="Hapus Transaksi Mutasi"
                                                        >
                                                            <Trash2 className="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

            </div>
        </AuthenticatedLayout>
    );
}
