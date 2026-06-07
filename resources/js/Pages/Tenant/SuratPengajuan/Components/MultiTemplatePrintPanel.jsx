import React, { useState, useEffect } from 'react';
import { Printer, CheckSquare, Square, Download, Loader2, FileText, X } from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';
import Swal from 'sweetalert2';

export default function MultiTemplatePrintPanel({ suratPengajuan, suratType }) {
    const [selectedTemplates, setSelectedTemplates] = useState([]);
    const [printing, setPrinting] = useState(false);

    // Filter templates berdasarkan gender
    const activeTemplates = (suratType.templates || []).filter(t => {
        if (!t.is_active) return false;
        
        // Pengecekan gender dari data penduduk
        // Catatan: Jika ada isian manual domisili (p.data_tambahan.jenis_kelamin), kita cek itu juga
        const pemohonGender = suratPengajuan.penduduk?.jenis_kelamin || suratPengajuan.data_tambahan?.jenis_kelamin;
        
        if (t.gender_filter === 'L' && pemohonGender === 'P') return false;
        if (t.gender_filter === 'P' && pemohonGender === 'L') return false;
        
        return true;
    });

    // Pre-select berdasarkan pilihan saat Create, atau pilih semua jika belum ada
    useEffect(() => {
        if (activeTemplates.length > 0 && selectedTemplates.length === 0) {
            const savedIds = suratPengajuan.data_tambahan?._selected_template_ids;
            if (Array.isArray(savedIds) && savedIds.length > 0) {
                const preselect = activeTemplates.filter(t => savedIds.includes(t.id)).map(t => t.id);
                setSelectedTemplates(preselect.length > 0 ? preselect : activeTemplates.map(t => t.id));
            } else {
                setSelectedTemplates(activeTemplates.map(t => t.id));
            }
        }
    }, [activeTemplates]);

    const toggleTemplate = (id) => {
        setSelectedTemplates(prev => 
            prev.includes(id) 
                ? prev.filter(t => t !== id)
                : [...prev, id]
        );
    };

    const toggleAll = () => {
        if (selectedTemplates.length === activeTemplates.length) {
            setSelectedTemplates([]); // Deselect all
        } else {
            setSelectedTemplates(activeTemplates.map(t => t.id)); // Select all
        }
    };

    const handlePrint = async () => {
        if (selectedTemplates.length === 0) {
            Swal.fire('Oops...', 'Pilih minimal 1 dokumen untuk dicetak.', 'warning');
            return;
        }

        setPrinting(true);
        try {
            const res = await axios.post(
                route('admin.surat-pengajuan.generate-multi', suratPengajuan.id),
                { template_ids: selectedTemplates },
                { responseType: 'blob' }
            );

            // Extract filename from Content-Disposition if possible
            const contentDisposition = res.headers['content-disposition'];
            let filename = `surat_${suratPengajuan.nomor_surat}.zip`; // fallback
            
            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="?(.+?)"?$/);
                if (filenameMatch && filenameMatch.length === 2) {
                    filename = filenameMatch[1];
                }
            } else if (selectedTemplates.length === 1) {
                // If single file, the server returns a docx, not a zip
                const selected = activeTemplates.find(t => t.id === selectedTemplates[0]);
                filename = `${selected.kode}_${suratPengajuan.nomor_surat}.docx`.replace(/[\/\\:*?"<>|]/g, '-');
            }

            // Create download link
            const url = window.URL.createObjectURL(new Blob([res.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

        } catch (error) {
            console.error('Print failed:', error);
            
            // Try to parse blob error as JSON
            if (error.response && error.response.data instanceof Blob) {
                try {
                    const text = await error.response.data.text();
                    const json = JSON.parse(text);
                    Swal.fire('Gagal', json.message || 'Terjadi kesalahan saat memproses dokumen.', 'error');
                } catch (e) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat memproses dokumen.', 'error');
                }
            } else {
                Swal.fire('Gagal', 'Terjadi kesalahan saat menghubungi server.', 'error');
            }
        } finally {
            setPrinting(false);
        }
    };

    if (activeTemplates.length === 0) {
        return (
            <div className="bg-yellow-50 rounded-3xl p-6 border border-yellow-100 text-center">
                <FileText className="w-8 h-8 text-yellow-400 mx-auto mb-2" />
                <p className="text-xs font-bold text-yellow-800">Tidak ada sub-template yang cocok/aktif untuk dicetak.</p>
            </div>
        );
    }

    return (
        <div className="bg-indigo-50/30 rounded-3xl border border-indigo-100 overflow-hidden shadow-sm animate-in slide-in-from-right-6 duration-500 delay-400 mt-6">
            <div className="p-6 border-b border-indigo-100 bg-indigo-50/50 flex items-center gap-3">
                <Printer className="w-5 h-5 text-indigo-600" />
                <div>
                    <h3 className="text-sm font-black text-indigo-900 uppercase italic tracking-tighter">Cetak Multi-Dokumen</h3>
                    <p className="text-[9px] font-bold text-indigo-500 uppercase tracking-widest mt-0.5">Pilih dokumen yang ingin didownload</p>
                </div>
            </div>
            
            <div className="p-4 bg-white/50 space-y-2">
                <div className="flex justify-between items-center mb-3 px-2">
                    <button 
                        type="button"
                        onClick={toggleAll}
                        className="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest transition-colors flex items-center gap-1.5"
                    >
                        {selectedTemplates.length === activeTemplates.length ? <Square className="w-3.5 h-3.5" /> : <CheckSquare className="w-3.5 h-3.5" />}
                        {selectedTemplates.length === activeTemplates.length ? 'BATALKAN SEMUA' : 'PILIH SEMUA'}
                    </button>
                    <span className="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                        {selectedTemplates.length} dari {activeTemplates.length} terpilih
                    </span>
                </div>

                {activeTemplates.map(t => {
                    const isSelected = selectedTemplates.includes(t.id);
                    return (
                        <div 
                            key={t.id}
                            onClick={() => toggleTemplate(t.id)}
                            className={cn(
                                "flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all",
                                isSelected ? "bg-white border-indigo-200 shadow-sm" : "bg-gray-50 border-transparent hover:bg-white hover:border-gray-200"
                            )}
                        >
                            <div className={cn("shrink-0", isSelected ? "text-indigo-600" : "text-gray-300")}>
                                {isSelected ? <CheckSquare className="w-5 h-5" /> : <Square className="w-5 h-5" />}
                            </div>
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <span className="px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[9px] font-black uppercase tracking-widest rounded">
                                        {t.kode}
                                    </span>
                                    <h4 className="text-xs font-bold text-gray-900 truncate">{t.nama}</h4>
                                </div>
                                {t.gender_filter !== 'all' && (
                                    <p className="text-[9px] font-black text-purple-500 uppercase tracking-widest mt-1">
                                        Khusus {t.gender_filter === 'L' ? 'Laki-laki' : 'Perempuan'}
                                    </p>
                                )}
                            </div>
                        </div>
                    );
                })}
            </div>

            <div className="p-4 bg-white border-t border-indigo-100">
                <button 
                    onClick={handlePrint}
                    disabled={printing || selectedTemplates.length === 0}
                    className="w-full py-4 bg-gradient-to-r from-indigo-600 to-indigo-800 text-white rounded-2xl text-xs font-black shadow-lg shadow-indigo-200 hover:shadow-xl hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:scale-100 disabled:shadow-none uppercase tracking-widest"
                >
                    {printing ? <Loader2 className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
                    {selectedTemplates.length > 1 ? 'DOWNLOAD SEMUA (.ZIP)' : 'DOWNLOAD DOKUMEN (.DOCX)'}
                </button>
            </div>
        </div>
    );
}
