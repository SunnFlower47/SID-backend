import React, { useState, useEffect } from 'react';
import { router } from '@inertiajs/react';
import { X, TrendingUp, Save, AlertTriangle, CheckCircle2 } from 'lucide-react';
import { cn } from '@/lib/utils';

const formatRupiah = (v) => `Rp ${Number(v || 0).toLocaleString('id-ID')}`;

export default function RealisasiModal({ proyek, onClose }) {
    const [realisasi, setRealisasi]   = useState(Number(proyek.realisasi) || 0);
    const [keterangan, setKeterangan] = useState('');
    const [loading, setLoading]       = useState(false);
    const [error, setError]           = useState('');

    const pct     = proyek.anggaran > 0 ? Math.min(100, Math.round((realisasi / Number(proyek.anggaran)) * 100)) : 0;
    const isValid = realisasi >= 0 && realisasi <= Number(proyek.anggaran);

    const barColor =
        pct >= 90 ? 'bg-green-500' :
        pct >= 60 ? 'bg-blue-500'  :
        pct >= 30 ? 'bg-yellow-400' :
                    'bg-gray-300';

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!isValid) { setError('Realisasi tidak boleh melebihi anggaran'); return; }
        setLoading(true);
        router.post(
            route('anggaran.update-realisasi-proyek', proyek.id),
            { realisasi, keterangan },
            {
                preserveScroll: true,
                onSuccess: () => { setLoading(false); onClose(); },
                onError: (e) => { setLoading(false); setError(Object.values(e).join(', ')); },
            }
        );
    };

    // Close on Escape
    useEffect(() => {
        const handler = (e) => { if (e.key === 'Escape') onClose(); };
        window.addEventListener('keydown', handler);
        return () => window.removeEventListener('keydown', handler);
    }, []);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-in fade-in duration-200">
            <div className="bg-white rounded-3xl shadow-2xl w-full max-w-md animate-in zoom-in-95 duration-300 overflow-hidden">
                {/* Modal Header */}
                <div className="bg-gradient-to-r from-green-600 to-green-700 p-6 relative">
                    <div className="flex items-center justify-between">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <TrendingUp className="w-5 h-5 text-yellow-300" />
                            </div>
                            <div>
                                <h2 className="text-sm font-black text-white uppercase italic tracking-tighter">Update Realisasi</h2>
                                <p className="text-[9px] font-bold text-green-100 uppercase tracking-widest mt-0.5 opacity-80">Perbarui Progress Proyek</p>
                            </div>
                        </div>
                        <button onClick={onClose} className="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-xl transition-all">
                            <X className="w-4 h-4" />
                        </button>
                    </div>
                </div>

                <form onSubmit={handleSubmit} className="p-6 space-y-5">
                    {/* Project Name */}
                    <div className="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p className="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Proyek</p>
                        <p className="text-xs font-black text-gray-900 uppercase italic tracking-tighter">{proyek.nama_proyek}</p>
                        <div className="flex gap-4 mt-2">
                            <div>
                                <p className="text-[8px] text-gray-400 font-black uppercase tracking-widest">Anggaran</p>
                                <p className="text-sm font-black text-gray-900">{formatRupiah(proyek.anggaran)}</p>
                            </div>
                            <div>
                                <p className="text-[8px] text-gray-400 font-black uppercase tracking-widest">Realisasi Lama</p>
                                <p className="text-sm font-black text-blue-600">{formatRupiah(proyek.realisasi)}</p>
                            </div>
                        </div>
                    </div>

                    {/* Realisasi Input */}
                    <div className="space-y-1.5">
                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                            Realisasi Baru (Rp) — Maks: {formatRupiah(proyek.anggaran)}
                        </label>
                        <input
                            type="number"
                            value={realisasi}
                            onChange={e => { setRealisasi(Number(e.target.value)); setError(''); }}
                            min="0"
                            max={proyek.anggaran}
                            className={cn(
                                'w-full border rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 text-center text-lg',
                                !isValid ? 'border-red-300 bg-red-50 focus:ring-red-300' : 'border-gray-200'
                            )}
                        />
                    </div>

                    {/* Live Progress Preview */}
                    <div className="space-y-2">
                        <div className="flex justify-between">
                            <span className="text-[9px] font-black text-gray-400 uppercase tracking-widest">Preview Progress</span>
                            <span className={cn('text-sm font-black', pct >= 90 ? 'text-green-600' : pct >= 60 ? 'text-blue-600' : 'text-yellow-600')}>{pct}%</span>
                        </div>
                        <div className="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div className={cn('h-full rounded-full transition-all duration-300', barColor)} style={{ width: `${pct}%` }} />
                        </div>
                        <div className="flex justify-between text-[8px] font-black text-gray-400 uppercase tracking-widest">
                            <span>0</span>
                            <span>{formatRupiah(realisasi)} / {formatRupiah(proyek.anggaran)}</span>
                        </div>
                    </div>

                    {/* Status Preview */}
                    {pct >= 100 && (
                        <div className="flex items-center gap-2 p-3 bg-green-50 border border-green-100 rounded-xl">
                            <CheckCircle2 className="w-4 h-4 text-green-600 shrink-0" />
                            <p className="text-[9px] font-black text-green-700 uppercase tracking-widest">Status akan otomatis berubah menjadi <b>SELESAI</b></p>
                        </div>
                    )}

                    {/* Keterangan */}
                    <div className="space-y-1.5">
                        <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Keterangan Update (Opsional)</label>
                        <textarea
                            value={keterangan}
                            onChange={e => setKeterangan(e.target.value)}
                            rows={2}
                            placeholder="Catatan progress terbaru..."
                            className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-green-500 resize-none"
                        />
                    </div>

                    {/* Error */}
                    {error && (
                        <div className="flex items-center gap-2 p-3 bg-red-50 border border-red-100 rounded-xl">
                            <AlertTriangle className="w-4 h-4 text-red-500 shrink-0" />
                            <p className="text-[9px] font-black text-red-600 uppercase tracking-widest">{error}</p>
                        </div>
                    )}

                    {/* Buttons */}
                    <div className="flex gap-3 pt-1">
                        <button type="button" onClick={onClose} className="flex-1 py-3 rounded-xl bg-gray-50 text-gray-600 text-xs font-black uppercase tracking-widest hover:bg-gray-100 border border-gray-200 transition-all">
                            BATAL
                        </button>
                        <button type="submit" disabled={loading || !isValid}
                            className="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-green-600 text-white text-xs font-black uppercase tracking-widest hover:bg-green-700 transition-all shadow-lg shadow-green-200 disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98]"
                        >
                            <Save className="w-3.5 h-3.5" />
                            {loading ? 'MENYIMPAN...' : 'SIMPAN'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
