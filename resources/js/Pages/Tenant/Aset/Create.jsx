import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageHeader, FormCard, FormField } from '@/Components/Shared';
import { Package, Calculator, Save, Info } from 'lucide-react';

const fmt = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(n ?? 0);
const fmtQty = (n) => new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(n ?? 0);

export default function Create({ auth, kategoris, tahun, semester }) {
    const [selectedKategori, setSelectedKategori] = useState('');
    const [barangOptions, setBarangOptions]       = useState([]);
    const [selectedBarang, setSelectedBarang]     = useState(null);

    const { data, setData, post, processing, errors } = useForm({
        aset_barang_id:       '',
        nama_barang_override: '',
        satuan:               '',
        kondisi:              'baik',
        lokasi:               '',
        tanggal_perolehan:    '',   // ← KOSONG, user wajib isi tanggal asli perolehan
        asal_usul:            'APBDes',
        keterangan:           '',
        no_polisi:            '',
        no_mesin:             '',
        no_rangka:            '',
        no_bpkb:              '',
        no_sertifikat:        '',
        // Mutasi pertama — auto-sync dari tanggal_perolehan
        tahun:                tahun ?? new Date().getFullYear(),
        semester:             semester ?? (new Date().getMonth() < 6 ? 1 : 2),
        tanggal:              '',
        kwantitas:            '',
        nilai:                '',
        keterangan_mutasi:    '',
    });

    const handleKategoriChange = (val) => {
        setSelectedKategori(val);
        const kat = kategoris.find((k) => k.id === Number(val));
        setBarangOptions(kat?.barangs ?? []);
        setData({
            ...data,
            aset_barang_id: '',
            satuan: '',
            no_polisi: '',
            no_mesin: '',
            no_rangka: '',
            no_bpkb: '',
            no_sertifikat: ''
        });
        setSelectedBarang(null);
    };

    const handleBarangChange = (barangId) => {
        const barang = barangOptions.find((b) => b.id === Number(barangId));
        setSelectedBarang(barang ?? null);
        setData({
            ...data,
            aset_barang_id: barangId,
            satuan: barang?.satuan_default ?? data.satuan,
        });
    };


    // Saat tanggal perolehan diisi: sync otomatis ke mutasi pertama (tanggal, tahun, semester)
    const handleTanggalPerolehanChange = (val) => {
        const updates = { tanggal_perolehan: val, tanggal: val };
        if (val) {
            const d = new Date(val);
            if (!isNaN(d)) {
                updates.tahun    = d.getFullYear();
                updates.semester = d.getMonth() < 6 ? 1 : 2;
            }
        }
        setData((prev) => ({ ...prev, ...updates }));
    };

    const saldoAkhirQty   = parseFloat(data.kwantitas) || 0;
    const saldoAkhirNilai = parseFloat(data.nilai)     || 0;

    const selectedKategoriObj = kategoris.find((k) => k.id === Number(selectedKategori));
    const showVehicleFields   = selectedKategoriObj?.kode === '3';
    const showLandFields      = selectedKategoriObj?.kode === '2';

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('aset.inventaris.store'));
    };

    const SEMESTER_LABEL = { 1: 'Semester 1 (Jan–Jun)', 2: 'Semester 2 (Jul–Des)' };

    return (
        <AuthenticatedLayout user={auth.user} title="Tambah Aset Baru">
            <Head title="Tambah Aset Baru" />

            {/* Lebar sama dengan Index — tidak dibatasi max-w */}
            <div className="space-y-5 animate-in fade-in duration-500 pb-20">

                <PageHeader
                    icon={Package}
                    title="Tambah Aset Baru"
                    subtitle={`Tahun ${data.tahun} — ${SEMESTER_LABEL[data.semester]}`}
                    backHref={route('aset.inventaris.index', { tahun, semester })}
                />

                <form onSubmit={handleSubmit} className="space-y-5">

                    {/* ── 1. Identifikasi Tipe Barang ───────────────────────── */}
                    <FormCard icon={Package} title="Tipe / Kode Barang (Referensi)">
                        {/* Info box */}
                        <div className="flex gap-3 p-3 bg-blue-50 border border-blue-100 rounded-2xl mb-4">
                            <Info className="w-4 h-4 text-blue-500 shrink-0 mt-0.5" />
                            <p className="text-xs text-blue-700 font-semibold leading-relaxed">
                                <b>Kode barang</b> merujuk pada <b>tipe/jenis</b> barang (contoh: 3.06.01.01 = Peralatan Studio Audio).
                                <br />Nama spesifik aset milik desa diisi di bagian berikutnya.
                            </p>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <FormField.Select
                                label="Golongan Aset"
                                required
                                value={selectedKategori}
                                onChange={(e) => handleKategoriChange(e.target.value)}
                                options={[
                                    { value: '', label: 'Pilih golongan...' },
                                    ...kategoris.map((k) => ({ value: k.id, label: `${k.kode} — ${k.nama}` }))
                                ]}
                            />

                            <div className="space-y-1">
                                <FormField.Select
                                    label="Kode & Tipe Barang"
                                    required
                                    value={data.aset_barang_id}
                                    onChange={(e) => handleBarangChange(e.target.value)}
                                    error={errors.aset_barang_id}
                                    options={[
                                        { value: '', label: 'Pilih tipe barang...' },
                                        ...barangOptions.map((b) => ({ value: b.id, label: `${b.kode_barang} — ${b.nama_barang}` }))
                                    ]}
                                />
                                {selectedBarang && (
                                    <p className="text-[10px] text-gray-400 font-mono ml-1 mt-1">
                                        Tipe: <span className="text-green-600 font-bold">{selectedBarang.kode_barang}</span> — {selectedBarang.nama_barang}
                                    </p>
                                )}
                            </div>
                        </div>
                    </FormCard>

                    {/* ── 2. Nama & Identitas Spesifik ──────────────────────── */}
                    <FormCard icon={Package} title="Nama & Identitas Aset Desa">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {/* Nama spesifik — field UTAMA & WAJIB */}
                            <div className="sm:col-span-2">
                                <FormField.Input
                                    label="Nama Spesifik Aset"
                                    required
                                    placeholder="Contoh: Laptop Dell Inspiron 15, Speaker Yamaha DBR10, Meja Rapat Kayu Jati..."
                                    value={data.nama_barang_override}
                                    onChange={(e) => setData('nama_barang_override', e.target.value)}
                                    error={errors.nama_barang_override}
                                />
                            </div>

                            <FormField.Input
                                label="Satuan" required
                                placeholder="unit, buah, m², set..."
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
                                placeholder="Kantor Desa, Balai Pertemuan, Pos Kamling..."
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
                                required
                                type="date"
                                value={data.tanggal_perolehan}
                                onChange={(e) => handleTanggalPerolehanChange(e.target.value)}
                                error={errors.tanggal_perolehan}
                            />

                            {/* Conditional Fields for Peralatan & Mesin */}
                            {showVehicleFields && (
                                <>
                                    <FormField.Input
                                        label="Nomor Polisi (Opsional)"
                                        placeholder="Contoh: T 1234 AB"
                                        value={data.no_polisi}
                                        onChange={(e) => setData('no_polisi', e.target.value)}
                                        error={errors.no_polisi}
                                    />
                                    <FormField.Input
                                        label="Nomor BPKB (Opsional)"
                                        placeholder="Contoh: N-1234567"
                                        value={data.no_bpkb}
                                        onChange={(e) => setData('no_bpkb', e.target.value)}
                                        error={errors.no_bpkb}
                                    />
                                    <FormField.Input
                                        label="Nomor Mesin (Opsional)"
                                        placeholder="Masukkan nomor mesin..."
                                        value={data.no_mesin}
                                        onChange={(e) => setData('no_mesin', e.target.value)}
                                        error={errors.no_mesin}
                                    />
                                    <FormField.Input
                                        label="Nomor Rangka (Opsional)"
                                        placeholder="Masukkan nomor rangka..."
                                        value={data.no_rangka}
                                        onChange={(e) => setData('no_rangka', e.target.value)}
                                        error={errors.no_rangka}
                                    />
                                </>
                            )}

                            {/* Conditional Fields for Tanah */}
                            {showLandFields && (
                                <div className="sm:col-span-2">
                                    <FormField.Input
                                        label="Nomor Sertifikat / Bukti Kepemilikan (Opsional)"
                                        placeholder="Contoh: Sertifikat Hak Pakai No. 12/Cibatu"
                                        value={data.no_sertifikat}
                                        onChange={(e) => setData('no_sertifikat', e.target.value)}
                                        error={errors.no_sertifikat}
                                    />
                                </div>
                            )}

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

                    {/* ── 3. Perolehan Awal (Mutasi Pertama) ────────────────── */}
                    <FormCard icon={Calculator} title="Perolehan Awal — Jumlah & Nilai">
                        <div className="flex gap-3 p-3 bg-amber-50 border border-amber-100 rounded-2xl mb-4">
                            <Info className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                            <p className="text-xs text-amber-700 font-semibold">
                                Isi jumlah dan nilai pada saat aset ini <b>pertama kali diterima/dibeli</b>.
                                Perubahan selanjutnya dicatat lewat menu <b>Tambah Mutasi</b>.
                            </p>
                        </div>

                        {/* Status: Saldo Awal vs Bertambah */}
                        {data.tanggal_perolehan ? (() => {
                            const isLamaTahun = data.tahun < (tahun ?? new Date().getFullYear());
                            const isSamaTahun = data.tahun === (tahun ?? new Date().getFullYear());

                            if (isLamaTahun) {
                                // Aset lama — akan jadi Saldo Awal
                                return (
                                    <div className="flex gap-3 p-4 bg-blue-50 border border-blue-200 rounded-2xl mb-4">
                                        <div className="shrink-0 w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center">
                                            <span className="text-blue-600 font-black text-xs">SA</span>
                                        </div>
                                        <div>
                                            <p className="text-xs font-black text-blue-700 uppercase tracking-widest mb-1">Akan menjadi Saldo Awal ✅</p>
                                            <p className="text-xs text-blue-600 font-semibold">
                                                Karena diperoleh tahun <b>{data.tahun}</b>, aset ini akan muncul sebagai
                                                <b> Saldo Awal</b> di laporan tahun {tahun ?? new Date().getFullYear()} SM {data.semester === 1 ? '1 (Jan–Jun)' : '2 (Jul–Des)'}.
                                            </p>
                                        </div>
                                    </div>
                                );
                            } else if (isSamaTahun) {
                                // Aset tahun ini — akan jadi Bertambah
                                return (
                                    <div className="flex gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl mb-4">
                                        <div className="shrink-0 w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center">
                                            <span className="text-emerald-600 font-black text-xs">+</span>
                                        </div>
                                        <div>
                                            <p className="text-xs font-black text-emerald-700 uppercase tracking-widest mb-1">Akan menjadi Aset Baru (Bertambah) ✔</p>
                                            <p className="text-xs text-emerald-600 font-semibold">
                                                Diperoleh tahun <b>{data.tahun}</b> — akan muncul di kolom <b>Bertambah</b> laporan semester {data.semester}.
                                                Jika ini aset lama, ubah tanggal perolehan ke tahun yang benar.
                                            </p>
                                        </div>
                                    </div>
                                );
                            } else {
                                return (
                                    <div className="flex gap-3 p-3 bg-green-50 border border-green-100 rounded-2xl mb-4">
                                        <Info className="w-4 h-4 text-green-600 shrink-0 mt-0.5" />
                                        <p className="text-xs text-green-700 font-semibold">
                                            Dicatat: <b>Tahun {data.tahun} — Semester {data.semester}</b>
                                        </p>
                                    </div>
                                );
                            }
                        })() : (
                            <div className="flex gap-3 p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-4">
                                <Info className="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                                <div>
                                    <p className="text-xs font-black text-amber-700 uppercase tracking-widest mb-1">Isi Tanggal Perolehan dulu ☝️</p>
                                    <p className="text-xs text-amber-600 font-semibold">
                                        Tanggal perolehan menentukan apakah aset ini tercatat sebagai
                                        <b> Saldo Awal</b> (diperoleh sebelum tahun ini) atau <b>Aset Baru</b> (diperoleh tahun ini).
                                    </p>
                                </div>
                            </div>
                        )}

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <FormField.Input
                                label={`Kwantitas${data.satuan ? ` (${data.satuan})` : ''}`}
                                required
                                type="number"
                                step="0.01"
                                min="0.01"
                                placeholder="0"
                                value={data.kwantitas}
                                onChange={(e) => setData('kwantitas', e.target.value)}
                                error={errors.kwantitas}
                            />

                            <FormField.Input
                                label="Nilai (Rp)"
                                required
                                type="number"
                                step="1"
                                min="0"
                                placeholder="0"
                                value={data.nilai}
                                onChange={(e) => setData('nilai', e.target.value)}
                                error={errors.nilai}
                            />

                            <div className="sm:col-span-2">
                                <FormField.Input
                                    label="Keterangan Perolehan"
                                    placeholder="Pengadaan APBDes, Hibah dari Kabupaten..."
                                    value={data.keterangan_mutasi}
                                    onChange={(e) => setData('keterangan_mutasi', e.target.value)}
                                />
                            </div>
                        </div>

                        {/* Preview */}
                        {(data.kwantitas || data.nilai) && (
                            <div className="mt-4 grid grid-cols-2 gap-4 bg-gray-900 rounded-2xl p-4">
                                <div className="text-center">
                                    <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Kwantitas Awal</p>
                                    <p className="text-white font-black text-xl">{fmtQty(saldoAkhirQty)} <span className="text-gray-400 text-xs">{data.satuan}</span></p>
                                </div>
                                <div className="text-center border-l border-white/10">
                                    <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Nilai Awal</p>
                                    <p className="text-yellow-300 font-black text-lg">{fmt(saldoAkhirNilai)}</p>
                                </div>
                            </div>
                        )}
                    </FormCard>

                    {/* ── Tombol ────────────────────────────────────────────── */}
                    <div className="flex gap-3 justify-end">
                        <Link href={route('aset.inventaris.index', { tahun, semester })}
                            className="px-8 py-3 bg-gray-100 text-gray-600 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-gray-200 transition-all">
                            Batal
                        </Link>
                        <button type="submit" disabled={processing}
                            className="flex items-center gap-2 px-8 py-3 bg-green-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-green-700 disabled:opacity-60 transition-all shadow-lg">
                            <Save className="w-4 h-4" />
                            {processing ? 'Menyimpan...' : 'Simpan Aset Baru'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
