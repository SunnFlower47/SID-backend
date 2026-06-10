import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Calendar, Wallet, AlertTriangle, FileText } from 'lucide-react';
import { cn } from '@/lib/utils';
import { PageHeader, FormField, FormCard } from '@/Components/Shared';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;
const formatDate   = (d) => d ? new Date(d).toISOString().split('T')[0] : '';

export default function AddExpenditure({ auth, apbdesList = [], tahunList = [], tahun, jenis, taxRates }) {
    const { data, setData, post, processing, errors } = useForm({
        apbdes_id:           '',
        jenis_transaksi:     'belanja',
        nama_pengeluaran:    '',
        nama_penerima:       '',
        jumlah:              '',
        tanggal_pengeluaran: new Date().toISOString().split('T')[0],
        keterangan:          '',
        pajak_ppn:           '',
        pajak_pph21:         '',
        pajak_pph22:         '',
        pajak_pph23:         '',
    });

    const [activeTaxes, setActiveTaxes] = useState({
        ppn: false,
        pph21: false,
        pph22: false,
        pph23: false
    });

    const handleTaxToggle = (taxType, isChecked) => {
        setActiveTaxes(prev => ({ ...prev, [taxType]: isChecked }));
        
        const amt = Number(data.jumlah) || 0;
        let dpp = amt;
        
        // PPN & PPh22 biasanya DPP-nya = Harga / 1.11 (jika harga sudah termasuk PPN 11%)
        // Untuk penyederhanaan kalkulator awam: kita hitung DPP dari jumlah jika PPN aktif
        if (taxType === 'ppn' || taxType === 'pph22') {
            dpp = Math.round(amt / (1 + (taxRates.ppn / 100)));
        }

        if (isChecked) {
            let taxVal = 0;
            if (taxType === 'ppn') taxVal = Math.round(dpp * (taxRates.ppn / 100));
            if (taxType === 'pph21') taxVal = Math.round(amt * (taxRates.pph21 / 100));
            if (taxType === 'pph22') taxVal = Math.round(dpp * (taxRates.pph22 / 100));
            if (taxType === 'pph23') taxVal = Math.round(amt * (taxRates.pph23 / 100));
            
            setData(`pajak_${taxType}`, taxVal);
        } else {
            setData(`pajak_${taxType}`, '');
        }
    };

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
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
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

                                    <FormField.Select
                                        label="Jenis Transaksi"
                                        error={errors.jenis_transaksi}
                                        value={data.jenis_transaksi}
                                        onChange={e => setData('jenis_transaksi', e.target.value)}
                                        required
                                    >
                                        <option value="belanja">Belanja Kegiatan (SPP Definitif/SPJ)</option>
                                        <option value="pencairan_panjar">Pencairan Panjar (Uang Muka/Kasbon)</option>
                                        <option value="kembali_sisa">Pengembalian Sisa Panjar</option>
                                    </FormField.Select>
                                </div>

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
                                        label="Nama Penerima (Toko/Vendor/Orang)" 
                                        error={errors.nama_penerima}
                                        value={data.nama_penerima} 
                                        onChange={e => setData('nama_penerima', e.target.value)}
                                        placeholder="Kosongkan jika ingin ditulis tangan saat dicetak" 
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

                            {/* Seksi Pajak */}
                            <FormCard title="Kalkulator & Pencatatan Pajak" icon={Wallet} bodyClass="p-6 sm:p-8 space-y-6">
                                <div className="space-y-6">
                                    {/* Pilihan Pajak */}
                                    <div className="space-y-4">
                                        {/* PPN */}
                                        <div className="flex flex-col sm:flex-row sm:items-start gap-4 p-4 border rounded-xl transition-colors hover:bg-gray-50/50 activeTaxes.ppn ? 'border-green-300 bg-green-50/20' : 'border-gray-100'">
                                            <div className="flex items-start gap-3 flex-1">
                                                <input type="checkbox" id="tax_ppn" checked={activeTaxes.ppn} onChange={e => handleTaxToggle('ppn', e.target.checked)} className="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                                <div>
                                                    <label htmlFor="tax_ppn" className="text-sm font-bold text-gray-800 cursor-pointer">PPN (Pajak Pertambahan Nilai)</label>
                                                    <p className="text-xs text-gray-500 mt-0.5">Berlaku untuk pengadaan barang/jasa dari toko/rekanan PKP (Pengusaha Kena Pajak).</p>
                                                </div>
                                            </div>
                                            {activeTaxes.ppn && (
                                                <div className="w-full sm:w-48 shrink-0">
                                                    <input type="number" placeholder="Nominal Rp" value={data.pajak_ppn} onChange={e => setData('pajak_ppn', e.target.value)} className="w-full text-sm rounded-lg border-green-300 focus:ring-green-500 focus:border-green-500 bg-white font-bold text-green-700" />
                                                </div>
                                            )}
                                        </div>

                                        {/* PPh 21 */}
                                        <div className="flex flex-col sm:flex-row sm:items-start gap-4 p-4 border rounded-xl transition-colors hover:bg-gray-50/50 activeTaxes.pph21 ? 'border-green-300 bg-green-50/20' : 'border-gray-100'">
                                            <div className="flex items-start gap-3 flex-1">
                                                <input type="checkbox" id="tax_pph21" checked={activeTaxes.pph21} onChange={e => handleTaxToggle('pph21', e.target.checked)} className="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                                <div>
                                                    <label htmlFor="tax_pph21" className="text-sm font-bold text-gray-800 cursor-pointer">PPh Pasal 21</label>
                                                    <p className="text-xs text-gray-500 mt-0.5">Pemotongan pajak untuk gaji, honorarium, atau upah yang dibayarkan ke orang pribadi.</p>
                                                </div>
                                            </div>
                                            {activeTaxes.pph21 && (
                                                <div className="w-full sm:w-48 shrink-0">
                                                    <input type="number" placeholder="Nominal Rp" value={data.pajak_pph21} onChange={e => setData('pajak_pph21', e.target.value)} className="w-full text-sm rounded-lg border-green-300 focus:ring-green-500 focus:border-green-500 bg-white font-bold text-green-700" />
                                                </div>
                                            )}
                                        </div>

                                        {/* PPh 22 */}
                                        <div className="flex flex-col sm:flex-row sm:items-start gap-4 p-4 border rounded-xl transition-colors hover:bg-gray-50/50 activeTaxes.pph22 ? 'border-green-300 bg-green-50/20' : 'border-gray-100'">
                                            <div className="flex items-start gap-3 flex-1">
                                                <input type="checkbox" id="tax_pph22" checked={activeTaxes.pph22} onChange={e => handleTaxToggle('pph22', e.target.checked)} className="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                                <div>
                                                    <label htmlFor="tax_pph22" className="text-sm font-bold text-gray-800 cursor-pointer">PPh Pasal 22</label>
                                                    <p className="text-xs text-gray-500 mt-0.5">Pemotongan untuk belanja barang dengan nilai &gt; Rp 2.000.000 (tidak dipecah-pecah).</p>
                                                </div>
                                            </div>
                                            {activeTaxes.pph22 && (
                                                <div className="w-full sm:w-48 shrink-0">
                                                    <input type="number" placeholder="Nominal Rp" value={data.pajak_pph22} onChange={e => setData('pajak_pph22', e.target.value)} className="w-full text-sm rounded-lg border-green-300 focus:ring-green-500 focus:border-green-500 bg-white font-bold text-green-700" />
                                                </div>
                                            )}
                                        </div>

                                        {/* PPh 23 */}
                                        <div className="flex flex-col sm:flex-row sm:items-start gap-4 p-4 border rounded-xl transition-colors hover:bg-gray-50/50 activeTaxes.pph23 ? 'border-green-300 bg-green-50/20' : 'border-gray-100'">
                                            <div className="flex items-start gap-3 flex-1">
                                                <input type="checkbox" id="tax_pph23" checked={activeTaxes.pph23} onChange={e => handleTaxToggle('pph23', e.target.checked)} className="mt-1 w-5 h-5 text-green-600 rounded border-gray-300 focus:ring-green-500" />
                                                <div>
                                                    <label htmlFor="tax_pph23" className="text-sm font-bold text-gray-800 cursor-pointer">PPh Pasal 23</label>
                                                    <p className="text-xs text-gray-500 mt-0.5">Pemotongan atas sewa kendaraan/alat, dividen, bunga, atau jasa selain PPh 21.</p>
                                                </div>
                                            </div>
                                            {activeTaxes.pph23 && (
                                                <div className="w-full sm:w-48 shrink-0">
                                                    <input type="number" placeholder="Nominal Rp" value={data.pajak_pph23} onChange={e => setData('pajak_pph23', e.target.value)} className="w-full text-sm rounded-lg border-green-300 focus:ring-green-500 focus:border-green-500 bg-white font-bold text-green-700" />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                </div>
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
