import React, { useEffect, useState } from 'react';
import { useForm, router } from '@inertiajs/react';
import { AlertTriangle, MapPin, User, CheckCircle2, X, ChevronDown } from 'lucide-react';
import { cn } from '@/lib/utils';

// ── Helper: Digit Counter Badge ──────────────────────────────────────
const DigitCounter = ({ value, required = 16 }) => {
    const clean = (value || '').replace(/\D/g, '');
    const ok = clean.length === required;
    return (
        <span className={cn('text-[10px] font-black tabular-nums', ok ? 'text-green-600' : 'text-amber-500')}>
            {clean.length}/{required}
        </span>
    );
};

// ── Panel: invalid_nik ────────────────────────────────────────────────
function InvalidNikPanel({ conflict, data, setData, errors }) {
    return (
        <div className="space-y-5">
            <div className="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <p className="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-1">NIK Bermasalah dari Excel</p>
                <p className="text-lg font-mono font-black text-amber-900">{conflict.nik || '—'}</p>
                <p className="text-[10px] text-amber-600 font-bold mt-1">{conflict.reason}</p>
            </div>
            <div>
                <div className="flex items-center justify-between mb-2">
                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest">NIK yang Benar (16 Digit)</label>
                    <DigitCounter value={data.nik_new} />
                </div>
                <input
                    type="text"
                    inputMode="numeric"
                    maxLength={16}
                    value={data.nik_new}
                    onChange={e => setData('nik_new', e.target.value.replace(/\D/g, ''))}
                    placeholder="Masukkan NIK yang benar..."
                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                    autoFocus
                />
                {errors.nik_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nik_new}</p>}
            </div>
            <input type="hidden" value="fix_fields" onChange={() => {}} />
        </div>
    );
}

// ── Panel: invalid_nkk ────────────────────────────────────────────────
function InvalidNkkPanel({ conflict, data, setData, errors }) {
    return (
        <div className="space-y-5">
            <div className="bg-amber-50 border border-amber-100 rounded-2xl p-4">
                <p className="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-1">NKK Bermasalah dari Excel</p>
                <p className="text-lg font-mono font-black text-amber-900">{conflict.nkk || '—'}</p>
                <p className="text-[10px] text-amber-600 font-bold mt-1">{conflict.reason}</p>
            </div>
            <div>
                <div className="flex items-center justify-between mb-2">
                    <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest">NKK yang Benar (16 Digit)</label>
                    <DigitCounter value={data.nkk_new} />
                </div>
                <input
                    type="text"
                    inputMode="numeric"
                    maxLength={16}
                    value={data.nkk_new}
                    onChange={e => setData('nkk_new', e.target.value.replace(/\D/g, ''))}
                    placeholder="Masukkan NKK yang benar..."
                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                    autoFocus
                />
                {errors.nkk_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nkk_new}</p>}
            </div>
        </div>
    );
}

// ── Panel: wilayah_conflict ───────────────────────────────────────────
function WilayahConflictPanel({ conflict, data, setData, errors, rws }) {
    const [tab, setTab] = useState('pick'); // 'pick' | 'create'
    const [selectedRwId, setSelectedRwId] = useState('');

    const rwOptions = rws || [];
    const rtOptions = selectedRwId
        ? (rwOptions.find(rw => String(rw.id) === String(selectedRwId))?.rts || [])
        : [];

    const handleRwChange = (e) => {
        setSelectedRwId(e.target.value);
        setData({ ...data, rw_id: e.target.value, rt_id: '' });
    };

    return (
        <div className="space-y-5">
            <div className="bg-orange-50 border border-orange-100 rounded-2xl p-4">
                <p className="text-[10px] font-black text-orange-700 uppercase tracking-widest mb-1">Wilayah dari Excel (Tidak Dikenal)</p>
                <div className="flex gap-4 mt-1">
                    <div><span className="text-[9px] font-bold text-orange-400 uppercase">RT</span><p className="font-mono font-black text-orange-900">{conflict.rt_raw || '—'}</p></div>
                    <div><span className="text-[9px] font-bold text-orange-400 uppercase">RW</span><p className="font-mono font-black text-orange-900">{conflict.rw_raw || '—'}</p></div>
                    <div><span className="text-[9px] font-bold text-orange-400 uppercase">Dusun</span><p className="font-mono font-black text-orange-900">{conflict.dusun_raw || '—'}</p></div>
                </div>
            </div>

            {/* Tab Switcher */}
            <div className="flex gap-1 p-1 bg-gray-100 rounded-xl">
                <button type="button" onClick={() => { setTab('pick'); setData({ ...data, action: 'use_existing' }); }}
                    className={cn('flex-1 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all',
                        tab === 'pick' ? 'bg-white text-blue-700 shadow-sm' : 'text-gray-500 hover:text-gray-700')}>
                    Pilih dari Master Wilayah
                </button>
                <button type="button" onClick={() => { setTab('create'); setData({ ...data, action: 'create_override' }); }}
                    className={cn('flex-1 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all',
                        tab === 'create' ? 'bg-white text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700')}>
                    Buat Wilayah Baru
                </button>
            </div>

            {tab === 'pick' && (
                <div className="space-y-3">
                    <div>
                        <label className="block text-[10px] font-black text-gray-500 uppercase mb-2">Pilih RW</label>
                        <div className="relative">
                            <select value={selectedRwId} onChange={handleRwChange}
                                className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold appearance-none focus:ring-2 focus:ring-blue-500 pr-10">
                                <option value="">— Pilih RW —</option>
                                {rwOptions.map(rw => <option key={rw.id} value={rw.id}>RW {rw.kode}</option>)}
                            </select>
                            <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        </div>
                    </div>
                    {selectedRwId && (
                        <div>
                            <label className="block text-[10px] font-black text-gray-500 uppercase mb-2">Pilih RT</label>
                            <div className="relative">
                                <select value={data.rt_id} onChange={e => setData('rt_id', e.target.value)}
                                    className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold appearance-none focus:ring-2 focus:ring-blue-500 pr-10">
                                    <option value="">— Pilih RT —</option>
                                    {rtOptions.map(rt => <option key={rt.id} value={rt.id}>RT {rt.kode}</option>)}
                                </select>
                                <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            </div>
                        </div>
                    )}
                    {errors.rt_id && <p className="text-red-500 text-[10px] font-bold">{errors.rt_id}</p>}
                </div>
            )}

            {tab === 'create' && (
                <div className="space-y-3">
                    <div className="bg-emerald-50 border border-emerald-100 rounded-xl p-3">
                        <p className="text-[10px] font-bold text-emerald-700">RT/RW baru akan ditambahkan ke Master Wilayah dengan status <b>Perlu Review</b>. Jangan lupa verifikasi di halaman Master Wilayah nanti.</p>
                    </div>
                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="block text-[9px] font-black text-gray-500 uppercase mb-1">Kode RW Baru</label>
                            <input type="text" value={data.rw_new} onChange={e => setData('rw_new', e.target.value)}
                                placeholder={conflict.rw_raw || '001'}
                                className="w-full bg-gray-50 border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label className="block text-[9px] font-black text-gray-500 uppercase mb-1">Kode RT Baru</label>
                            <input type="text" value={data.rt_new} onChange={e => setData('rt_new', e.target.value)}
                                placeholder={conflict.rt_raw || '001'}
                                className="w-full bg-gray-50 border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono font-bold focus:ring-2 focus:ring-emerald-500" />
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

// ── Panel: nik_conflict ───────────────────────────────────────────────
function NikConflictPanel({ conflict, data, setData, errors }) {
    const existing = conflict.existing_resident;
    const isChangingNik = data.action === 'change_incoming_nik';

    return (
        <div className="space-y-5">
            {/* Side-by-side comparison */}
            <div className="grid grid-cols-2 gap-3">
                <div className="bg-slate-50 border border-slate-200 rounded-2xl p-4">
                    <p className="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <span className="w-2 h-2 rounded-full bg-slate-400 inline-block" /> Data di Database (Lama)
                    </p>
                    {existing ? (
                        <div className="space-y-2">
                            <div><p className="text-[9px] font-bold text-slate-400 uppercase">Nama</p><p className="text-sm font-bold text-slate-800">{existing.nama}</p></div>
                            <div><p className="text-[9px] font-bold text-slate-400 uppercase">NIK</p><p className="text-xs font-mono font-bold text-slate-600">{existing.nik}</p></div>
                            <div><p className="text-[9px] font-bold text-slate-400 uppercase">NKK</p><p className="text-xs font-mono font-bold text-slate-600">{existing.nkk}</p></div>
                        </div>
                    ) : <p className="text-xs text-slate-400 italic font-bold">Data tidak ditemukan</p>}
                </div>
                <div className="bg-blue-50/60 border border-blue-100 rounded-2xl p-4">
                    <p className="text-[9px] font-black text-blue-500 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <span className="w-2 h-2 rounded-full bg-blue-500 inline-block" /> Data dari Excel (Baru)
                    </p>
                    <div className="space-y-2">
                        <div><p className="text-[9px] font-bold text-blue-400 uppercase">Nama</p>
                            <p className={cn('text-sm font-bold', existing && existing.nama !== conflict.nama ? 'text-rose-600' : 'text-blue-900')}>{conflict.nama || '—'}</p></div>
                        <div><p className="text-[9px] font-bold text-blue-400 uppercase">NIK</p>
                            <p className="text-xs font-mono font-bold text-blue-800">{conflict.nik || '—'}</p></div>
                        <div><p className="text-[9px] font-bold text-blue-400 uppercase">NKK</p>
                            <p className={cn('text-xs font-mono font-bold', existing && existing.nkk !== conflict.nkk ? 'text-rose-600' : 'text-blue-800')}>{conflict.nkk || '—'}</p></div>
                    </div>
                </div>
            </div>

            {/* Decision */}
            <div className="space-y-2">
                <p className="text-[10px] font-black text-gray-500 uppercase tracking-widest">Keputusan Admin:</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label className={cn('flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all', data.action === 'update_existing_from_incoming' ? 'border-blue-600 bg-blue-50' : 'border-gray-100 hover:border-blue-200')}>
                        <input type="radio" name="action" value="update_existing_from_incoming" checked={data.action === 'update_existing_from_incoming'} onChange={e => setData('action', e.target.value)} className="hidden" />
                        <span className="text-xs font-black text-gray-900 uppercase">Orang Sama — Update Data</span>
                        <span className="text-[10px] text-gray-500 font-bold mt-1">Data lama akan ditimpa dengan data baru dari Excel.</span>
                    </label>
                    <label className={cn('flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all', data.action === 'change_incoming_nik' ? 'border-rose-600 bg-rose-50' : 'border-gray-100 hover:border-rose-200')}>
                        <input type="radio" name="action" value="change_incoming_nik" checked={data.action === 'change_incoming_nik'} onChange={e => setData('action', e.target.value)} className="hidden" />
                        <span className="text-xs font-black text-gray-900 uppercase">Orang Berbeda — Ganti NIK</span>
                        <span className="text-[10px] text-gray-500 font-bold mt-1">NIK di Excel salah. Masukkan NIK yang benar di bawah.</span>
                    </label>
                </div>
            </div>

            {/* NIK input only visible when 'ganti nik' is selected */}
            {isChangingNik && (
                <div>
                    <div className="flex items-center justify-between mb-2">
                        <label className="text-[10px] font-black text-gray-500 uppercase tracking-widest">NIK Orang Ini yang Sebenarnya</label>
                        <DigitCounter value={data.nik_new} />
                    </div>
                    <input type="text" inputMode="numeric" maxLength={16}
                        value={data.nik_new} onChange={e => setData('nik_new', e.target.value.replace(/\D/g, ''))}
                        placeholder="Masukkan NIK yang benar (16 digit)..."
                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-rose-500 transition-all" autoFocus />
                    {errors.nik_new && <p className="text-red-500 text-[10px] mt-1 font-bold">{errors.nik_new}</p>}
                </div>
            )}

            <div className="bg-amber-50 border border-amber-100 rounded-xl p-3">
                <p className="text-[10px] font-bold text-amber-700">⚠️ Setelah menyimpan keputusan ini, Anda perlu klik <b>"Konfirmasi Import"</b> di daftar untuk menerapkan data.</p>
            </div>
        </div>
    );
}

// ── Main Modal Component ───────────────────────────────────────────────
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
            const defaultAction = conflict.issue_type === 'nik_conflict'
                ? 'update_existing_from_incoming'
                : conflict.issue_type === 'wilayah_conflict'
                    ? 'use_existing'
                    : 'fix_fields';

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

    const isSimple = ['invalid_nik', 'invalid_nkk', 'wilayah_conflict'].includes(conflict.issue_type);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('import-conflicts.resolve', conflict.id), {
            preserveScroll: true,
            onSuccess: () => onClose()
        });
    };

    const typeConfig = {
        invalid_nik: { label: 'NIK Tidak Valid', color: 'text-amber-700 bg-amber-100', icon: User },
        invalid_nkk: { label: 'NKK Tidak Valid', color: 'text-amber-700 bg-amber-100', icon: User },
        wilayah_conflict: { label: 'Wilayah Tidak Dikenal', color: 'text-orange-700 bg-orange-100', icon: MapPin },
        nik_conflict: { label: 'NIK Sudah Terdaftar', color: 'text-rose-700 bg-rose-100', icon: User },
    };
    const cfg = typeConfig[conflict.issue_type] || { label: conflict.issue_type, color: 'text-gray-600 bg-gray-100', icon: AlertTriangle };
    const Icon = cfg.icon;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
            <div className="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onClick={onClose} />

            <div className="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                {/* Header */}
                <div className="p-6 border-b border-gray-100 flex items-center justify-between bg-slate-50">
                    <div className="flex items-center gap-3">
                        <div className={cn('w-10 h-10 rounded-2xl flex items-center justify-center', cfg.color)}>
                            <Icon className="w-5 h-5" />
                        </div>
                        <div>
                            <h2 className="text-sm font-black text-gray-900 uppercase tracking-tight">{conflict.nama || 'Data Bermasalah'}</h2>
                            <span className={cn('text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full', cfg.color)}>{cfg.label}</span>
                        </div>
                    </div>
                    <button onClick={onClose} className="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-200 transition-all">
                        <X className="w-5 h-5" />
                    </button>
                </div>

                {/* Body */}
                <form id="resolveForm" onSubmit={handleSubmit} className="p-6 overflow-y-auto flex-1 space-y-6">
                    {conflict.issue_type === 'invalid_nik' && <InvalidNikPanel conflict={conflict} data={data} setData={setData} errors={errors} />}
                    {conflict.issue_type === 'invalid_nkk' && <InvalidNkkPanel conflict={conflict} data={data} setData={setData} errors={errors} />}
                    {conflict.issue_type === 'wilayah_conflict' && <WilayahConflictPanel conflict={conflict} data={data} setData={setData} errors={errors} rws={rws} />}
                    {conflict.issue_type === 'nik_conflict' && <NikConflictPanel conflict={conflict} data={data} setData={setData} errors={errors} />}
                    {!['invalid_nik', 'invalid_nkk', 'wilayah_conflict', 'nik_conflict'].includes(conflict.issue_type) && (
                        <div className="bg-gray-50 rounded-2xl p-4">
                            <p className="text-xs font-bold text-gray-500">Jenis issue ini tidak memerlukan tindakan khusus. Klik "Lewati" untuk mengabaikan.</p>
                        </div>
                    )}
                </form>

                {/* Footer */}
                <div className="p-5 bg-white border-t border-gray-100 flex gap-3">
                    <button type="button" onClick={onClose}
                        className="px-5 py-3 bg-gray-50 text-gray-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button
                        type="button"
                        onClick={() => { setData('action', 'skip'); setTimeout(() => document.getElementById('resolveForm').requestSubmit(), 10); }}
                        className="px-5 py-3 bg-gray-100 text-gray-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">
                        Lewati
                    </button>
                    <button type="submit" form="resolveForm" disabled={processing}
                        className={cn(
                            'flex-1 px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition-all shadow-lg disabled:opacity-50',
                            isSimple
                                ? 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-emerald-200'
                                : 'bg-blue-600 text-white hover:bg-blue-700 shadow-blue-200'
                        )}>
                        {processing ? 'Memproses...' : isSimple ? '✓ Perbaiki & Import Sekarang' : 'Simpan Keputusan'}
                    </button>
                </div>
            </div>
        </div>
    );
}
