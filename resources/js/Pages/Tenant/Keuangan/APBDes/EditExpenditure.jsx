import React, { useState } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { ArrowLeft, Save, Edit2, Upload, X, Download, Eye, Trash2, FileText, Wallet } from 'lucide-react';
import { cn } from '@/lib/utils';
import { PageHeader, FormField, FormCard } from '@/Components/Shared';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function EditExpenditure({ auth, pengeluaran, jenisBuktiOptions = {}, taxRates }) {
    const apbdes      = pengeluaran.apbdes;
    const [previewFile, setPreviewFile] = useState(null);

    const { data, setData, post, processing, errors } = useForm({
        _method:              'PUT',
        nama_pengeluaran:     pengeluaran.nama_pengeluaran     ?? '',
        jumlah:               pengeluaran.jumlah               ?? '',
        tanggal_pengeluaran:  pengeluaran.tanggal_pengeluaran
            ? new Date(pengeluaran.tanggal_pengeluaran).toISOString().split('T')[0]
            : '',
        keterangan:           pengeluaran.keterangan           ?? '',
        no_bukti:             pengeluaran.no_bukti             ?? '',
        jenis_bukti:          pengeluaran.jenis_bukti          ?? 'kwitansi',
        file_bukti:           null,
        hapus_file:           false,
        pajak_ppn:            pengeluaran.pajak_ppn            ?? '',
        pajak_pph21:          pengeluaran.pajak_pph21          ?? '',
        pajak_pph22:          pengeluaran.pajak_pph22          ?? '',
        pajak_pph23:          pengeluaran.pajak_pph23          ?? '',
    });

    const hasPpn = Number(pengeluaran.pajak_ppn) > 0;
    const hasPph21 = Number(pengeluaran.pajak_pph21) > 0;
    const hasPph22 = Number(pengeluaran.pajak_pph22) > 0;
    const hasPph23 = Number(pengeluaran.pajak_pph23) > 0;

    const [activeTaxes, setActiveTaxes] = useState({
        ppn: hasPpn,
        pph21: hasPph21,
        pph22: hasPph22,
        pph23: hasPph23
    });

    const handleTaxToggle = (taxType, isChecked) => {
        setActiveTaxes(prev => ({ ...prev, [taxType]: isChecked }));
        
        const amt = Number(data.jumlah) || 0;
        let dpp = amt;
        
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

    const sisaAnggaran = apbdes
        ? Number(apbdes.anggaran) - Number(apbdes.realisasi) + Number(pengeluaran.jumlah)
        : null;

    // POST with _method=PUT for file upload (Laravel method spoofing)
    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('anggaran.update-pengeluaran', pengeluaran.id), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout user={auth.user} title="Edit Pengeluaran">
            <Head title="Edit Pengeluaran APBDes" />

            {/* Preview Modal */}
            {previewFile && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4" onClick={() => setPreviewFile(null)}>
                    <div className="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" onClick={e => e.stopPropagation()}>
                        <div className="flex items-center justify-between p-5 border-b border-gray-100">
                            <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{previewFile.nama}</p>
                            <div className="flex gap-2">
                                <a href={previewFile.url} download target="_blank" rel="noopener noreferrer"
                                    className="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-green-700 transition-all">
                                    <Download className="w-3 h-3" /> Download
                                </a>
                                <button onClick={() => setPreviewFile(null)} className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all"><X className="w-4 h-4" /></button>
                            </div>
                        </div>
                        <div className="h-[75vh] overflow-auto">
                            {previewFile.url.match(/\.(jpg|jpeg|png)$/i)
                                ? <img src={previewFile.url} alt="Preview" className="w-full h-full object-contain bg-gray-50 p-4" />
                                : <iframe src={previewFile.url} className="w-full h-full" title="Preview Bukti" />
                            }
                        </div>
                    </div>
                </div>
            )}

            <div className="space-y-6 animate-in fade-in duration-700 pb-20">
                {/* Header */}
                <PageHeader
                    title={pengeluaran.nama_pengeluaran}
                    subtitle={`Edit Pengeluaran · ${pengeluaran.no_bukti ?? 'Tanpa No. Bukti'} · ${apbdes?.kode_rekening}`}
                    icon={Edit2}
                    actions={[
                        {
                            label: 'KEMBALI',
                            icon: ArrowLeft,
                            href: route('anggaran.histori-pengeluaran', apbdes?.id),
                            variant: 'ghost'
                        }
                    ]}
                />

                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                        {/* Main Form */}
                        <div className="lg:col-span-2 space-y-6">
                            {/* Seksi 1: Informasi Pengeluaran */}
                            <FormCard title="Informasi Pengeluaran" icon={FileText} bodyClass="p-6 sm:p-8 space-y-5">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <FormField.Input 
                                        label="Nama Pengeluaran *" 
                                        error={errors.nama_pengeluaran}
                                        value={data.nama_pengeluaran} 
                                        onChange={e => setData('nama_pengeluaran', e.target.value)}
                                    />
                                    
                                    <FormField.Input 
                                        label="Tanggal *" 
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
                                />

                                <FormField.Textarea 
                                    label="Keterangan (Opsional)" 
                                    error={errors.keterangan}
                                    value={data.keterangan} 
                                    onChange={e => setData('keterangan', e.target.value)}
                                    rows={2} 
                                />
                            </FormCard>

                            {/* Seksi 2: Dokumen Bukti */}
                            <FormCard title="Dokumen Bukti Pembayaran" icon={Upload} bodyClass="p-6 sm:p-8 space-y-5">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div className="space-y-1.5">
                                        <FormField.Input 
                                            label="No. Bukti" 
                                            error={errors.no_bukti}
                                            value={data.no_bukti} 
                                            onChange={e => setData('no_bukti', e.target.value)}
                                            placeholder={pengeluaran.no_bukti ?? 'BKT-YYYY-XXXX'}
                                            inputClassName="font-mono"
                                        />
                                        {!errors.no_bukti && <p className="text-[9px] font-bold text-gray-400 ml-1">Kosongkan untuk tidak mengubah</p>}
                                    </div>
                                    
                                    <FormField.Select 
                                        label="Jenis Bukti" 
                                        error={errors.jenis_bukti}
                                        value={data.jenis_bukti} 
                                        onChange={e => setData('jenis_bukti', e.target.value)}
                                        options={Object.entries(jenisBuktiOptions).map(([v, l]) => ({ value: v, label: l }))}
                                    />
                                </div>

                                {/* File saat ini */}
                                {pengeluaran.file_bukti_url && !data.hapus_file && (
                                    <div className="flex items-center justify-between p-4 bg-blue-50 border border-blue-100 rounded-xl">
                                        <div className="flex items-center gap-3">
                                            <div className="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                                                <Eye className="w-4 h-4 text-blue-600" />
                                            </div>
                                            <div>
                                                <p className="text-xs font-black text-blue-900">File Saat Ini</p>
                                                <p className="text-[9px] font-bold text-blue-500 uppercase tracking-widest truncate max-w-[200px]">{pengeluaran.nama_file_bukti}</p>
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <button type="button"
                                                onClick={() => setPreviewFile({ url: pengeluaran.file_bukti_url, nama: pengeluaran.nama_file_bukti })}
                                                className="px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-blue-200 transition-all">
                                                LIHAT
                                            </button>
                                            <button type="button"
                                                onClick={() => setData('hapus_file', true)}
                                                className="px-3 py-1.5 bg-red-50 text-red-600 border border-red-100 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-red-100 transition-all flex items-center gap-1">
                                                <Trash2 className="w-3 h-3" /> HAPUS
                                            </button>
                                        </div>
                                    </div>
                                )}
                                {data.hapus_file && (
                                    <div className="flex items-center justify-between p-3 bg-red-50 border border-red-100 rounded-xl">
                                        <p className="text-[9px] font-black text-red-600 uppercase tracking-widest">File akan dihapus saat disimpan</p>
                                        <button type="button" onClick={() => setData('hapus_file', false)}
                                            className="text-[9px] font-black text-red-500 underline uppercase tracking-widest">BATALKAN</button>
                                    </div>
                                )}

                                <FormField label={pengeluaran.file_bukti_url ? 'Ganti File (PDF/JPG, maks 5MB)' : 'Upload Bukti (PDF/JPG, maks 5MB)'} error={errors.file_bukti}>
                                    <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                                        onChange={e => { setData('file_bukti', e.target.files[0]); setData('hapus_file', false); }}
                                        className="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-2 focus:ring-green-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[9px] file:font-black file:uppercase file:tracking-widest file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" />
                                </FormField>
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

                        {/* Sidebar */}
                        <div className="space-y-4 sticky top-6 self-start">
                            {apbdes && (
                                <div className="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                                    <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Rekening Terkait</p>
                                    <p className="text-xs font-black text-gray-900">[{apbdes.kode_rekening}] {apbdes.nama_rekening}</p>
                                    <div className="flex gap-4 mt-2">
                                        <div>
                                            <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Anggaran</p>
                                            <p className="text-sm font-black text-gray-900">{formatRupiah(apbdes.anggaran)}</p>
                                        </div>
                                        <div>
                                            <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Realisasi</p>
                                            <p className="text-sm font-black text-blue-600">{formatRupiah(apbdes.realisasi)}</p>
                                        </div>
                                    </div>
                                </div>
                            )}
                            <button type="submit" disabled={processing}
                                className="w-full flex items-center justify-center gap-3 px-8 py-5 bg-green-600 text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-green-200 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50">
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
