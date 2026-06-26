import React, { useState } from 'react';
import { X, CheckSquare, Square, Download, Loader2 } from 'lucide-react';
import { cn } from '@/lib/utils';

const AVAILABLE_COLUMNS = [
    { id: 'nik', label: 'NIK' },
    { id: 'nama', label: 'Nama Lengkap' },
    { id: 'jenis_kelamin', label: 'Jenis Kelamin' },
    { id: 'tempat_lahir', label: 'Tempat Lahir' },
    { id: 'tanggal_lahir', label: 'Tanggal Lahir' },
    { id: 'agama', label: 'Agama' },
    { id: 'pendidikan', label: 'Pendidikan' },
    { id: 'pekerjaan', label: 'Pekerjaan' },
    { id: 'status_perkawinan', label: 'Status Perkawinan' },
    { id: 'kedudukan_keluarga', label: 'Kedudukan Dlm Keluarga' },
    { id: 'alamat', label: 'Alamat' },
    { id: 'rt', label: 'RT' },
    { id: 'rw', label: 'RW' },
    { id: 'dusun', label: 'Dusun' },
    { id: 'nkk', label: 'No. KK' },
    { id: 'nama_ayah', label: 'Nama Ayah' },
    { id: 'nama_ibu', label: 'Nama Ibu' },
    { id: 'golongan_darah', label: 'Golongan Darah' },
    { id: 'no_akta_lahir', label: 'No. Akta Lahir' },
    { id: 'telepon', label: 'Telepon' },
    { id: 'warganegara', label: 'Kewarganegaraan' },
    { id: 'status_asuransi', label: 'Status Asuransi' },
    { id: 'jenis_cacat', label: 'Jenis Cacat' },
    { id: 'sakit_menahun', label: 'Sakit Menahun' },
    { id: 'keterangan', label: 'Keterangan' },
];

export default function ExportModal({ isOpen, onClose, onExport, isExporting }) {
    // Default checked columns
    const [selectedCols, setSelectedCols] = useState([
        'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'rt', 'rw', 'dusun'
    ]);

    if (!isOpen) return null;

    const toggleColumn = (id) => {
        setSelectedCols(prev => 
            prev.includes(id) ? prev.filter(col => col !== id) : [...prev, id]
        );
    };

    const toggleAll = () => {
        if (selectedCols.length === AVAILABLE_COLUMNS.length) {
            setSelectedCols([]);
        } else {
            setSelectedCols(AVAILABLE_COLUMNS.map(c => c.id));
        }
    };

    const handleExport = () => {
        onExport(selectedCols);
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div className="bg-white rounded-3xl w-full max-w-2xl mx-4 overflow-hidden shadow-2xl flex flex-col max-h-[90vh] animate-in zoom-in-95 duration-200">
                {/* Header */}
                <div className="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50/50">
                    <div>
                        <h2 className="text-xl font-bold text-gray-900">Custom Export Excel</h2>
                        <p className="text-sm text-gray-500 mt-1">Pilih kolom data yang ingin diekspor</p>
                    </div>
                    <button 
                        onClick={onClose}
                        className="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-500"
                    >
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Body */}
                <div className="p-6 overflow-y-auto flex-1">
                    <div className="flex items-center justify-between mb-4">
                        <button 
                            onClick={toggleAll}
                            className="flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700"
                        >
                            {selectedCols.length === AVAILABLE_COLUMNS.length ? (
                                <><CheckSquare className="w-4 h-4" /> Unselect All</>
                            ) : (
                                <><Square className="w-4 h-4" /> Select All</>
                            )}
                        </button>
                        <span className="text-sm text-gray-500">
                            {selectedCols.length} kolom dipilih
                        </span>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        {AVAILABLE_COLUMNS.map((col) => {
                            const isSelected = selectedCols.includes(col.id);
                            return (
                                <label 
                                    key={col.id} 
                                    className={cn(
                                        "flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all duration-200",
                                        isSelected 
                                            ? "border-blue-500 bg-blue-50/50 ring-1 ring-blue-500/20" 
                                            : "border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50"
                                    )}
                                >
                                    <div className={cn(
                                        "mt-0.5 rounded-md flex items-center justify-center w-5 h-5 transition-colors shrink-0",
                                        isSelected ? "bg-blue-600 text-white" : "border-2 border-gray-300"
                                    )}>
                                        {isSelected && <CheckSquare className="w-4 h-4" />}
                                    </div>
                                    <span className={cn(
                                        "text-sm font-medium leading-tight",
                                        isSelected ? "text-blue-900" : "text-gray-700"
                                    )}>
                                        {col.label}
                                    </span>
                                </label>
                            );
                        })}
                    </div>
                </div>

                {/* Footer */}
                <div className="p-6 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
                    <button 
                        onClick={onClose}
                        className="px-5 py-2.5 rounded-xl font-medium text-gray-600 hover:bg-gray-200 transition-colors"
                        disabled={isExporting}
                    >
                        Batal
                    </button>
                    <button 
                        onClick={handleExport}
                        disabled={selectedCols.length === 0 || isExporting}
                        className="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium flex items-center gap-2 transition-all shadow-lg shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {isExporting ? (
                            <><Loader2 className="w-5 h-5 animate-spin" /> Sedang Mengekspor...</>
                        ) : (
                            <><Download className="w-5 h-5" /> Export Sekarang</>
                        )}
                    </button>
                </div>
            </div>
        </div>
    );
}
