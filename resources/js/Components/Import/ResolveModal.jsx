import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { AlertTriangle, MapPin, User, CheckCircle2, ArrowRight } from 'lucide-react';

export default function ResolveModal({ isOpen, onClose, conflict, rws }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        action: 'fix_fields',
        nama_new: '',
        nik_new: '',
        nkk_new: '',
        alamat_new: '',
        dusun_new: '',
        rw_new: '',
        rt_new: '',
        rt_id: '',
        rw_id: ''
    });

    useEffect(() => {
        if (conflict && isOpen) {
            let defaultAction = 'fix_fields';
            if (conflict.issue_type === 'nik_conflict') {
                defaultAction = 'update_existing_from_incoming';
            } else if (conflict.issue_type === 'wilayah_conflict') {
                defaultAction = 'create_override';
            }

            reset({
                action: defaultAction,
                nama_new: conflict.nama || '',
                nik_new: conflict.nik || '',
                nkk_new: conflict.nkk || '',
                alamat_new: conflict.payload_raw?.alamat || conflict.payload_raw?.domisili || '',
                dusun_new: conflict.dusun_raw || '',
                rw_new: conflict.rw_raw || '',
                rt_new: conflict.rt_raw || '',
                rt_id: '',
                rw_id: ''
            });
        }
    }, [conflict, isOpen]);

    if (!isOpen || !conflict) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('import-conflicts.resolve', conflict.id), {
            preserveScroll: true,
            onSuccess: () => onClose()
        });
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onClick={onClose} />
            
            <div className="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h2 className="text-xl font-black text-gray-900 uppercase">Perbaiki Data Import</h2>
                        <p className="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Lakukan penyesuaian data sebelum diproses</p>
                    </div>
                    <button onClick={onClose} className="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-200 transition-all">
                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div className="p-6 overflow-y-auto flex-1 custom-scrollbar space-y-6">
                    {/* Info Issue */}
                    <div className="bg-rose-50 border border-rose-100 rounded-2xl p-4 flex gap-4">
                        <div className="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center shrink-0">
                            <AlertTriangle className="w-5 h-5" />
                        </div>
                        <div>
                            <div className="text-[10px] font-black text-rose-600 uppercase tracking-widest">Masalah Ditemukan:</div>
                            <div className="text-sm font-bold text-rose-900 mt-0.5">{conflict.reason}</div>
                        </div>
                    </div>

                    {/* Comparison for NIK conflict */}
                    {conflict.issue_type === 'nik_conflict' && conflict.existing_resident && (
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {/* Existing */}
                            <div className="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                                <div className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span className="w-2 h-2 rounded-full bg-slate-400"></span> Database Lama
                                </div>
                                <div className="space-y-2">
                                    <div>
                                        <div className="text-[9px] font-bold text-slate-400 uppercase">Nama</div>
                                        <div className="text-sm font-bold text-slate-700">{conflict.existing_resident.nama}</div>
                                    </div>
                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <div className="text-[9px] font-bold text-slate-400 uppercase">NIK</div>
                                            <div className="text-xs font-mono font-bold text-slate-600">{conflict.existing_resident.nik}</div>
                                        </div>
                                        <div>
                                            <div className="text-[9px] font-bold text-slate-400 uppercase">NKK</div>
                                            <div className="text-xs font-mono font-bold text-slate-600">{conflict.existing_resident.nkk}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {/* New Excel Data */}
                            <div className="bg-blue-50/50 rounded-2xl p-4 border border-blue-100">
                                <div className="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                    <span className="w-2 h-2 rounded-full bg-blue-500"></span> Data Import (Baru)
                                </div>
                                <div className="space-y-2">
                                    <div>
                                        <div className="text-[9px] font-bold text-blue-400 uppercase">Nama</div>
                                        <div className={`text-sm font-bold ${conflict.existing_resident.nama !== data.nama_new ? 'text-rose-600' : 'text-blue-900'}`}>{data.nama_new || conflict.nama}</div>
                                    </div>
                                    <div className="grid grid-cols-2 gap-2">
                                        <div>
                                            <div className="text-[9px] font-bold text-blue-400 uppercase">NIK</div>
                                            <div className={`text-xs font-mono font-bold ${conflict.existing_resident.nik !== data.nik_new ? 'text-rose-600' : 'text-blue-800'}`}>{data.nik_new || conflict.nik}</div>
                                        </div>
                                        <div>
                                            <div className="text-[9px] font-bold text-blue-400 uppercase">NKK</div>
                                            <div className={`text-xs font-mono font-bold ${conflict.existing_resident.nkk !== data.nkk_new ? 'text-rose-600' : 'text-blue-800'}`}>{data.nkk_new || conflict.nkk}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    <form id="resolveForm" onSubmit={handleSubmit} className="space-y-6">
                        {/* Fields */}
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div className="md:col-span-2">
                                <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">Nama Lengkap</label>
                                <input 
                                    type="text" 
                                    value={data.nama_new} 
                                    onChange={e => setData('nama_new', e.target.value)}
                                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                />
                                {errors.nama_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nama_new}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">NIK (16 Digit)</label>
                                <input 
                                    type="text" 
                                    value={data.nik_new} 
                                    onChange={e => setData('nik_new', e.target.value)}
                                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                />
                                {errors.nik_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nik_new}</p>}
                            </div>
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">NKK (16 Digit)</label>
                                <input 
                                    type="text" 
                                    value={data.nkk_new} 
                                    onChange={e => setData('nkk_new', e.target.value)}
                                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                />
                                {errors.nkk_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nkk_new}</p>}
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-[10px] font-black text-gray-400 uppercase mb-2">Alamat Lengkap</label>
                                <textarea 
                                    value={data.alamat_new} 
                                    onChange={e => setData('alamat_new', e.target.value)}
                                    rows="2" 
                                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                ></textarea>
                            </div>
                        </div>

                        {/* Wilayah Fields */}
                        <div className="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <h4 className="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Penyesuaian Wilayah</h4>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-[9px] font-bold text-gray-500 uppercase mb-1">Dusun</label>
                                    <input 
                                        type="text" 
                                        value={data.dusun_new} 
                                        onChange={e => setData('dusun_new', e.target.value)}
                                        className="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs font-bold" 
                                    />
                                </div>
                                <div>
                                    <label className="block text-[9px] font-bold text-gray-500 uppercase mb-1">RW</label>
                                    <input 
                                        type="text" 
                                        value={data.rw_new} 
                                        onChange={e => setData('rw_new', e.target.value)}
                                        className="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs font-bold" 
                                    />
                                </div>
                                <div>
                                    <label className="block text-[9px] font-bold text-gray-500 uppercase mb-1">RT</label>
                                    <input 
                                        type="text" 
                                        value={data.rt_new} 
                                        onChange={e => setData('rt_new', e.target.value)}
                                        className="w-full bg-white border-gray-200 rounded-lg px-3 py-2 text-xs font-bold" 
                                    />
                                </div>
                            </div>
                        </div>

                        {/* Action Selection */}
                        <div className="space-y-3">
                            <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Keputusan Akhir:</label>
                            
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {conflict.issue_type === 'nik_conflict' && (
                                    <>
                                        <label className={`relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all ${data.action === 'update_existing_from_incoming' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 hover:border-blue-200'}`}>
                                            <input type="radio" name="action" value="update_existing_from_incoming" checked={data.action === 'update_existing_from_incoming'} onChange={e => setData('action', e.target.value)} className="hidden" />
                                            <span className="text-xs font-black text-gray-900 uppercase">Timpa Data Lama</span>
                                            <span className="text-[10px] text-gray-500 font-bold mt-1">Update database dengan data baru ini.</span>
                                        </label>
                                        <label className={`relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all ${data.action === 'change_incoming_nik' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 hover:border-blue-200'}`}>
                                            <input type="radio" name="action" value="change_incoming_nik" checked={data.action === 'change_incoming_nik'} onChange={e => setData('action', e.target.value)} className="hidden" />
                                            <span className="text-xs font-black text-gray-900 uppercase">Simpan Sebagai Baru</span>
                                            <span className="text-[10px] text-gray-500 font-bold mt-1">Gunakan NIK baru yang diketik di atas.</span>
                                        </label>
                                    </>
                                )}

                                {conflict.issue_type === 'wilayah_conflict' && (
                                    <>
                                        <label className={`relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all ${data.action === 'create_override' ? 'border-emerald-600 bg-emerald-50' : 'border-gray-100 hover:border-emerald-200'}`}>
                                            <input type="radio" name="action" value="create_override" checked={data.action === 'create_override'} onChange={e => setData('action', e.target.value)} className="hidden" />
                                            <span className="text-xs font-black text-gray-900 uppercase">Buat Wilayah Baru</span>
                                            <span className="text-[10px] text-gray-500 font-bold mt-1">Tambahkan RW/RT ini ke Master.</span>
                                        </label>
                                    </>
                                )}

                                {conflict.issue_type !== 'nik_conflict' && conflict.issue_type !== 'wilayah_conflict' && (
                                    <label className={`relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all ${data.action === 'fix_fields' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 hover:border-blue-200'}`}>
                                        <input type="radio" name="action" value="fix_fields" checked={data.action === 'fix_fields'} onChange={e => setData('action', e.target.value)} className="hidden" />
                                        <span className="text-xs font-black text-gray-900 uppercase">Perbaiki & Lanjutkan</span>
                                        <span className="text-[10px] text-gray-500 font-bold mt-1">Simpan perbaikan data ini.</span>
                                    </label>
                                )}
                                
                                <label className={`relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all ${data.action === 'skip' ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-300'}`}>
                                    <input type="radio" name="action" value="skip" checked={data.action === 'skip'} onChange={e => setData('action', e.target.value)} className="hidden" />
                                    <span className="text-xs font-black text-gray-900 uppercase">Lewati (Abaikan)</span>
                                    <span className="text-[10px] text-gray-500 font-bold mt-1">Jangan proses baris data ini.</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <div className="p-6 bg-white border-t border-gray-100 flex gap-3">
                    <button type="button" onClick={onClose} className="flex-1 px-6 py-3 bg-gray-50 text-gray-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        form="resolveForm"
                        disabled={processing}
                        className="flex-1 px-6 py-3 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all disabled:opacity-50"
                    >
                        {processing ? 'Menyimpan...' : 'Terapkan Perbaikan'}
                    </button>
                </div>
            </div>
        </div>
    );
}
