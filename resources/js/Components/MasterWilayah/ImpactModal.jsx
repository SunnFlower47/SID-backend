import React from 'react';
import { 
    AlertTriangle, 
    ArrowRight, 
    Users, 
    CheckCircle2, 
    X,
    Loader2,
    Info
} from 'lucide-react';

export default function ImpactModal({ isOpen, onClose, data, onConfirm, processing }) {
    if (!isOpen || !data) return null;

    const { before, after, affected_count, sample, entity } = data;

    return (
        <div className="fixed inset-0 z-[100] overflow-y-auto">
            <div className="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div className="fixed inset-0 transition-opacity" aria-hidden="true" onClick={onClose}>
                    <div className="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>
                </div>

                <span className="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div className="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-100">
                    <div className="bg-amber-50 px-6 py-4 flex items-center justify-between border-b border-amber-100">
                        <div className="flex items-center gap-3">
                            <div className="p-2 bg-amber-100 rounded-xl">
                                <AlertTriangle className="w-5 h-5 text-amber-600" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black text-amber-900 uppercase tracking-tight">Analisis Dampak Perubahan</h3>
                                <p className="text-[10px] text-amber-700 font-bold uppercase tracking-widest opacity-80">Pratinjau sebelum menyimpan data</p>
                            </div>
                        </div>
                        <button onClick={onClose} className="text-amber-400 hover:text-amber-600 transition-colors">
                            <X className="w-5 h-5" />
                        </button>
                    </div>

                    <div className="p-6 space-y-6">
                        {/* Comparison */}
                        <div className="grid grid-cols-7 items-center gap-4">
                            <div className="col-span-3 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                <p className="text-[10px] font-black text-gray-400 uppercase mb-2 tracking-widest">Sebelum</p>
                                <div className="space-y-1">
                                    <p className="text-sm font-black text-gray-900 uppercase">{entity.toUpperCase()} {before.rt || before.kode || before.nama}</p>
                                    <p className="text-[10px] font-bold text-gray-500 uppercase italic">RW {before.rw || '—'} | {before.dusun || '—'}</p>
                                </div>
                            </div>
                            <div className="col-span-1 flex justify-center">
                                <div className="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center">
                                    <ArrowRight className="w-4 h-4 text-blue-600" />
                                </div>
                            </div>
                            <div className="col-span-3 p-4 bg-blue-50/50 rounded-2xl border border-blue-100">
                                <p className="text-[10px] font-black text-blue-400 uppercase mb-2 tracking-widest">Sesudah</p>
                                <div className="space-y-1">
                                    <p className="text-sm font-black text-blue-700 uppercase">{entity.toUpperCase()} {after.rt || after.kode || after.nama}</p>
                                    <p className="text-[10px] font-bold text-blue-500 uppercase italic">RW {after.rw || '—'} | {after.dusun || '—'}</p>
                                </div>
                            </div>
                        </div>

                        {/* Impact Stats */}
                        <div className="flex items-center gap-4 p-4 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <div className="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                <Users className="w-6 h-6 text-indigo-600" />
                            </div>
                            <div>
                                <p className="text-xs font-black text-gray-900 uppercase">Dampak Kependudukan</p>
                                <p className="text-[10px] font-bold text-indigo-600 uppercase tracking-tighter">
                                    Sebanyak <span className="text-sm font-black">{affected_count} Jiwa</span> akan mengalami perubahan alamat administratif otomatis.
                                </p>
                            </div>
                        </div>

                        {/* Sample List */}
                        {sample && sample.length > 0 && (
                            <div className="space-y-3">
                                <div className="flex items-center justify-between">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Contoh Data Terdampak (Top 10)</p>
                                    <div className="flex items-center gap-1 text-[9px] font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full">
                                        <Info className="w-3 h-3" />
                                        <span>Update Otomatis</span>
                                    </div>
                                </div>
                                <div className="max-h-[200px] overflow-y-auto rounded-2xl border border-gray-50 divide-y divide-gray-50">
                                    {sample.map((p, i) => (
                                        <div key={i} className="px-4 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                            <div className="flex items-center gap-3">
                                                <div className="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center text-[10px] font-black text-gray-400">
                                                    {i + 1}
                                                </div>
                                                <div>
                                                    <p className="text-[10px] font-black text-gray-900 uppercase">{p.nama}</p>
                                                    <p className="text-[9px] font-bold text-gray-400">NIK: {p.nik}</p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-[9px] font-bold text-gray-400 italic uppercase">No. KK</p>
                                                <p className="text-[9px] font-black text-gray-600">{p.kartu_keluarga?.nomor_kk ?? '—'}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3">
                        <button 
                            onClick={onClose}
                            className="px-4 py-2 text-xs font-black text-gray-500 uppercase hover:text-gray-700 transition-colors"
                        >
                            Batalkan
                        </button>
                        <button 
                            onClick={onConfirm}
                            disabled={processing}
                            className="flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-xl text-xs font-black uppercase shadow-lg shadow-blue-900/20 hover:scale-105 active:scale-95 transition-all disabled:opacity-50"
                        >
                            {processing ? (
                                <>
                                    <Loader2 className="w-4 h-4 animate-spin" />
                                    Memproses Snapshot...
                                </>
                            ) : (
                                <>
                                    <CheckCircle2 className="w-4 h-4" />
                                    Lanjutkan & Simpan
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
