import React, { useState, useRef } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage } from '@inertiajs/react';
import * as Icons from 'lucide-react';
import { cn } from '@/lib/utils';
import Swal from 'sweetalert2';
import axios from 'axios';
import { PageHeader } from '@/Components/Shared';

export default function ImportData() {
    const [previewLoading, setPreviewLoading] = useState(false);
    const [previewData, setPreviewData] = useState(null);
    const fileInputRef = useRef(null);

    const handleFileChange = async (e) => {
        const file = e.target.files[0];
        if (!file) {
            setPreviewData(null);
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        setPreviewLoading(true);
        try {
            const res = await axios.post(route('import.penduduk.preview'), formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            if (res.data.success) {
                setPreviewData(res.data);
            }
        } catch (error) {
            const msg = error.response?.data?.message || error.message || 'Gagal memproses preview';
            Swal.fire('Preview Gagal', msg, 'error');
            setPreviewData(null);
            if (fileInputRef.current) fileInputRef.current.value = '';
        } finally {
            setPreviewLoading(false);
        }
    };

    const handleDownloadInvalid = async () => {
        const file = fileInputRef.current?.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        try {
            const res = await axios.post(route('import.penduduk.preview-invalid-report'), formData, {
                responseType: 'blob'
            });
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `invalid_rows_penduduk_${new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-')}.xlsx`);
            document.body.appendChild(link);
            link.click();
            link.remove();
        } catch (error) {
            Swal.fire('Gagal', 'Tidak dapat mengunduh laporan invalid.', 'error');
        }
    };

    return (
        <AuthenticatedLayout title="Import Data">
            <Head title="Import Data" />

            <div className="space-y-6">
                {/* Header */}
                <PageHeader
                    title="Import Data"
                    subtitle="Import data massal dari file Excel ke sistem"
                    icon={Icons.Upload}
                />

                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Import Forms */}
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="bg-gray-50/50 px-6 py-4 border-b border-gray-100">
                            <h3 className="text-lg font-bold text-gray-800 flex items-center uppercase italic tracking-tighter">
                                <Icons.UploadCloud className="w-5 h-5 text-green-600 mr-3" />
                                Form Import Data
                            </h3>
                        </div>
                        <div className="p-6 space-y-6">

                            {/* Import Penduduk */}
                            <div className="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-5 border border-blue-100 shadow-sm">
                                <div className="flex items-center mb-4">
                                    <div className="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                                        <Icons.Users className="w-5 h-5 text-white" />
                                    </div>
                                    <h6 className="text-sm font-bold text-gray-900">Import Data Penduduk</h6>
                                </div>
                                <form action={route('import.penduduk')} method="POST" encType="multipart/form-data">
                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')} />
                                    <div className="mb-4">
                                        <label className="block text-sm font-bold text-gray-700 mb-2">Pilih File Excel</label>
                                        <input 
                                            type="file" 
                                            name="file" 
                                            accept=".xlsx,.xls" 
                                            required
                                            ref={fileInputRef}
                                            onChange={handleFileChange}
                                            className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        />
                                        <p className="text-xs text-gray-500 mt-2 font-medium">Format: .xlsx atau .xls (Max: 10MB). Preview otomatis saat file dipilih.</p>
                                    </div>

                                    {previewLoading && (
                                        <div className="mb-4 text-xs font-bold text-blue-700 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 flex items-center">
                                            <Icons.Loader2 className="w-4 h-4 animate-spin mr-2" /> Memproses preview...
                                        </div>
                                    )}

                                    {previewData && (
                                        <div className="mb-5 bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                                            <div className="grid grid-cols-3 gap-3 text-xs mb-4">
                                                <div className="bg-gray-50 rounded-xl p-3 border border-gray-100 text-center">
                                                    <span className="block text-gray-500 mb-1">Total Baris</span>
                                                    <span className="font-black text-gray-900 text-base">{previewData.summary.total_data_rows}</span>
                                                </div>
                                                <div className="bg-green-50 rounded-xl p-3 border border-green-100 text-center text-green-700">
                                                    <span className="block text-green-600/80 mb-1">Valid</span>
                                                    <span className="font-black text-base">{previewData.summary.valid_rows}</span>
                                                </div>
                                                <div className="bg-red-50 rounded-xl p-3 border border-red-100 text-center text-red-700">
                                                    <span className="block text-red-600/80 mb-1">Invalid</span>
                                                    <span className="font-black text-base">{previewData.summary.invalid_rows}</span>
                                                </div>
                                            </div>

                                            <div className="grid grid-cols-2 md:grid-cols-4 gap-2 text-[10px] font-bold mb-4">
                                                <div className="bg-red-50/50 rounded-lg p-2 border border-red-100 text-red-800">
                                                    Error NIK: <span className="float-right">{previewData.summary.column_error_counts?.nik || 0}</span>
                                                </div>
                                                <div className="bg-red-50/50 rounded-lg p-2 border border-red-100 text-red-800">
                                                    Error Nama: <span className="float-right">{previewData.summary.column_error_counts?.nama || 0}</span>
                                                </div>
                                                <div className="bg-red-50/50 rounded-lg p-2 border border-red-100 text-red-800">
                                                    Error No. KK: <span className="float-right">{previewData.summary.column_error_counts?.nkk || 0}</span>
                                                </div>
                                                <div className="bg-red-50/50 rounded-lg p-2 border border-red-100 text-red-800">
                                                    Error Wilayah: <span className="float-right">{previewData.summary.column_error_counts?.wilayah || 0}</span>
                                                </div>
                                            </div>

                                            <p className="text-[11px] text-gray-500 mb-3 font-medium">
                                                Menampilkan {previewData.preview.invalid_shown} dari {previewData.preview.invalid_total} baris invalid, dan {previewData.preview.valid_shown} dari {previewData.preview.valid_total} baris valid.
                                            </p>

                                            {previewData.preview.invalid?.length > 0 && (
                                                <div className="mb-4">
                                                    <p className="text-xs font-bold text-red-600 mb-2 flex items-center">
                                                        <Icons.AlertCircle className="w-3.5 h-3.5 mr-1.5" /> Detail baris invalid:
                                                    </p>
                                                    <div className="border border-red-100 rounded-xl bg-red-50/40 p-3 max-h-48 overflow-y-auto custom-scrollbar">
                                                        <ul className="list-disc ml-4 text-[11px] text-red-700 space-y-1.5 font-medium">
                                                            {previewData.preview.invalid.map((item, idx) => (
                                                                <li key={idx}>
                                                                    Baris {item.row} ({item.nik || '-'} / {item.nama || '-'}) 
                                                                    {(item.rt || item.rw) && ` (RT ${item.rt}/RW ${item.rw})`}
                                                                    {item.alamat && ` [${item.alamat}]`}: 
                                                                    <span className="font-bold ml-1 text-red-800">
                                                                        {item.errors_by_column ? Object.entries(item.errors_by_column)
                                                                            .filter(([k]) => k !== 'nik_info')
                                                                            .map(([k, v]) => `${k.toUpperCase()}: ${Array.isArray(v) ? v.join(' | ') : v}`).join(' ; ') 
                                                                            : (item.errors || []).join(', ')}
                                                                    </span>
                                                                </li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                </div>
                                            )}

                                            {previewData.preview.valid?.length > 0 && (
                                                <div className="mb-2">
                                                    <p className="text-xs font-bold text-green-600 mb-2 flex items-center">
                                                        <Icons.CheckCircle2 className="w-3.5 h-3.5 mr-1.5" /> Detail baris valid (Siap Import):
                                                    </p>
                                                    <div className="border border-green-100 rounded-xl bg-green-50/40 p-3 max-h-48 overflow-y-auto custom-scrollbar">
                                                        <ul className="list-disc ml-4 text-[11px] text-green-700 space-y-1.5 font-medium">
                                                            {previewData.preview.valid.map((item, idx) => (
                                                                <li key={idx}>
                                                                    Baris {item.row} ({item.nik || '-'} / {item.nama || '-'}) 
                                                                    {(item.rt || item.rw) && ` (RT ${item.rt}/RW ${item.rw})`}
                                                                    {item.alamat && ` [${item.alamat}]`}
                                                                    {item.info && <span className="text-blue-600 font-bold ml-1">[{item.info}]</span>}
                                                                </li>
                                                            ))}
                                                        </ul>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    <div className="flex flex-wrap gap-2">
                                        <button 
                                            type="button" 
                                            onClick={handleDownloadInvalid}
                                            disabled={!previewData || previewData.summary.invalid_rows === 0}
                                            className="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95"
                                        >
                                            <Icons.FileX className="w-4 h-4 mr-2" /> Download Invalid
                                        </button>
                                        <button 
                                            type="submit" 
                                            disabled={!previewData || previewData.summary.valid_rows === 0}
                                            className="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95"
                                        >
                                            <Icons.Upload className="w-4 h-4 mr-2" /> Import Valid
                                        </button>
                                        <a 
                                            href={route('import.template', 'penduduk')} 
                                            className="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all active:scale-95 ml-auto"
                                        >
                                            <Icons.Download className="w-4 h-4 mr-2" /> Template
                                        </a>
                                    </div>
                                </form>
                            </div>

                            {/* Import Bantuan Sosial */}
                            <div className="bg-gradient-to-br from-red-50 to-white rounded-2xl p-5 border border-red-100 shadow-sm">
                                <div className="flex items-center mb-4">
                                    <div className="w-10 h-10 bg-red-500 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                                        <Icons.Heart className="w-5 h-5 text-white" />
                                    </div>
                                    <h6 className="text-sm font-bold text-gray-900">Import Bantuan Sosial</h6>
                                </div>
                                <form action={route('import.bantuan-sosial')} method="POST" encType="multipart/form-data">
                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')} />
                                    <div className="mb-4">
                                        <label className="block text-sm font-bold text-gray-700 mb-2">Pilih File Excel</label>
                                        <input 
                                            type="file" 
                                            name="file" 
                                            accept=".xlsx,.xls" 
                                            required
                                            className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                                        />
                                    </div>
                                    <div className="flex gap-2">
                                        <button type="submit" className="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                                            <Icons.Upload className="w-4 h-4 mr-2" /> Import
                                        </button>
                                        <a href={route('import.template', 'bantuan_sosial')} className="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all active:scale-95">
                                            <Icons.Download className="w-4 h-4 mr-2" /> Template
                                        </a>
                                    </div>
                                </form>
                            </div>

                            {/* Import UMKM */}
                            <div className="bg-gradient-to-br from-purple-50 to-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                                <div className="flex items-center mb-4">
                                    <div className="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center mr-3 shadow-sm">
                                        <Icons.Store className="w-5 h-5 text-white" />
                                    </div>
                                    <h6 className="text-sm font-bold text-gray-900">Import Data UMKM</h6>
                                </div>
                                <form action={route('import.umkm')} method="POST" encType="multipart/form-data">
                                    <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')} />
                                    <div className="mb-4">
                                        <label className="block text-sm font-bold text-gray-700 mb-2">Pilih File Excel</label>
                                        <input 
                                            type="file" 
                                            name="file" 
                                            accept=".xlsx,.xls" 
                                            required
                                            className="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 bg-white text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                                        />
                                    </div>
                                    <div className="flex gap-2">
                                        <button type="submit" className="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm active:scale-95">
                                            <Icons.Upload className="w-4 h-4 mr-2" /> Import
                                        </button>
                                        <a href={route('import.template', 'umkm')} className="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all active:scale-95">
                                            <Icons.Download className="w-4 h-4 mr-2" /> Template
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {/* Guide & Instructions */}
                    <div className="space-y-6">
                        <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                            <div className="bg-blue-50/50 px-6 py-4 border-b border-blue-100">
                                <h3 className="text-sm font-bold text-blue-800 flex items-center">
                                    <Icons.Info className="w-4 h-4 text-blue-600 mr-2" />
                                    Petunjuk Import Data
                                </h3>
                            </div>
                            <div className="p-6">
                                <ul className="space-y-3">
                                    {[
                                        'Download template Excel yang disediakan terlebih dahulu.',
                                        'Isi data sesuai dengan format template yang ada.',
                                        'Upload file Excel yang sudah diisi ke dalam sistem.',
                                        'Untuk data Penduduk, sistem akan menampilkan preview baris yang valid dan invalid.',
                                        'Hanya baris data yang valid yang akan dimasukkan ke dalam sistem.'
                                    ].map((text, i) => (
                                        <li key={i} className="flex items-start">
                                            <Icons.CheckCircle2 className="w-4 h-4 text-green-500 mr-3 shrink-0 mt-0.5" />
                                            <span className="text-[13px] font-medium text-gray-600">{text}</span>
                                        </li>
                                    ))}
                                </ul>
                                <div className="mt-6 bg-yellow-50/80 border border-yellow-200/60 rounded-2xl p-4">
                                    <h4 className="text-[13px] font-bold text-yellow-800 mb-2 flex items-center">
                                        <Icons.AlertTriangle className="w-4 h-4 text-yellow-600 mr-2" /> Tips Penting:
                                    </h4>
                                    <ul className="text-xs text-yellow-700 font-medium space-y-1.5 ml-6 list-disc">
                                        <li>Pastikan format tanggal menggunakan <strong>YYYY-MM-DD</strong> (contoh: 2024-01-15).</li>
                                        <li>NIK harus unik dan tepat berjumlah <strong>16 digit angka</strong>.</li>
                                        <li>NKK harus tepat berjumlah <strong>16 digit angka</strong>.</li>
                                        <li>Kolom berupa pilihan (seperti agama, jenis kelamin) harus sesuai dengan teks yang ada.</li>
                                        <li>File Excel maksimal berukuran <strong>10MB</strong> (.xlsx atau .xls).</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {/* Custom scrollbar styles for preview boxes */}
            <style>{`
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background-color: #cbd5e1;
                    border-radius: 10px;
                }
            `}</style>
        </AuthenticatedLayout>
    );
}
