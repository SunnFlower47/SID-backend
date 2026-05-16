import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/react';
import { X } from 'lucide-react';

export default function CrudModal({ isOpen, onClose, type, mode, data, dusuns, rws, onPreviewRequest }) {
    const isEdit = mode === 'edit';
    
    // Default forms based on type
    const defaultData = {
        dusun: { nama: '', kode: '', is_active: true },
        rw: { kode: '', nama: '', is_active: true },
        rt: { kode: '', rw_id: '', dusun_id: '', nama: '', is_active: true }
    };

    const { data: formData, setData, post, put, processing, errors, reset, clearErrors } = useForm(defaultData[type] || {});

    useEffect(() => {
        if (isOpen) {
            clearErrors();
            if (isEdit && data) {
                // Populate data
                if (type === 'dusun') {
                    setData({ nama: data.nama || '', kode: data.kode || '', is_active: data.is_active ?? true });
                } else if (type === 'rw') {
                    setData({ kode: data.kode || '', nama: data.nama || '', is_active: data.is_active ?? true });
                } else if (type === 'rt') {
                    setData({ 
                        kode: data.kode || '', 
                        nama: data.nama || '', 
                        rw_id: data.rw_id || '', 
                        dusun_id: data.dusun_id || '', 
                        is_active: data.is_active ?? true 
                    });
                }
            } else {
                reset();
            }
        }
    }, [isOpen, data, type, isEdit]);

    if (!isOpen) return null;

    const handleSubmit = (e) => {
        e.preventDefault();
        
        // Intercept structural changes (RW or Dusun) for RTs during edit
        if (isEdit && type === 'rt' && (formData.rw_id != data.rw_id || formData.dusun_id != data.dusun_id)) {
            onClose();
            if (onPreviewRequest) {
                onPreviewRequest(type, data, formData);
            }
            return;
        }

        const routeName = `settings.wilayah.${type}.${isEdit ? 'update' : 'store'}`;
        const routeParams = isEdit ? { [type]: data.id } : undefined;

        if (isEdit) {
            put(route(routeName, routeParams), {
                onSuccess: () => onClose()
            });
        } else {
            post(route(routeName), {
                onSuccess: () => onClose()
            });
        }
    };

    const title = `${isEdit ? 'Edit' : 'Tambah'} ${type.toUpperCase()}`;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div className="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onClick={onClose} />
            
            <div className="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col">
                <div className="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50/50">
                    <h2 className="text-xl font-black text-gray-900 tracking-tight">{title}</h2>
                    <button onClick={onClose} className="p-2 bg-white text-gray-400 hover:text-red-500 rounded-xl hover:bg-gray-100 transition-all border border-gray-100">
                        <X className="w-5 h-5" />
                    </button>
                </div>

                <div className="p-6 overflow-y-auto custom-scrollbar max-h-[70vh]">
                    <form id="crudForm" onSubmit={handleSubmit} className="space-y-5">
                        
                        {type === 'dusun' && (
                            <>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Nama Dusun *</label>
                                    <input 
                                        type="text" 
                                        value={formData.nama} 
                                        onChange={e => setData('nama', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: Dusun Cikadu"
                                    />
                                    {errors.nama && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.nama}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Kode Dusun (Opsional)</label>
                                    <input 
                                        type="text" 
                                        value={formData.kode} 
                                        onChange={e => setData('kode', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Kosongkan jika tidak ada"
                                    />
                                    {errors.kode && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.kode}</p>}
                                </div>
                            </>
                        )}

                        {type === 'rw' && (
                            <>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Kode RW * (Maks 3 Angka)</label>
                                    <input 
                                        type="text" 
                                        value={formData.kode} 
                                        onChange={e => setData('kode', e.target.value)}
                                        maxLength="3"
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: 001"
                                    />
                                    {errors.kode && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.kode}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Nama RW / Nama Ketua (Opsional)</label>
                                    <input 
                                        type="text" 
                                        value={formData.nama} 
                                        onChange={e => setData('nama', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: RW 001 Sukamaju"
                                    />
                                    {errors.nama && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.nama}</p>}
                                </div>
                            </>
                        )}

                        {type === 'rt' && (
                            <>
                                <div className="grid grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Pilih RW *</label>
                                        <select 
                                            value={formData.rw_id} 
                                            onChange={e => setData('rw_id', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        >
                                            <option value="">-- Pilih RW --</option>
                                            {rws.map(rw => (
                                                <option key={rw.id} value={rw.id}>RW {rw.kode}</option>
                                            ))}
                                        </select>
                                        {errors.rw_id && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.rw_id}</p>}
                                        {isEdit && <p className="text-[9px] text-indigo-500 mt-1 italic font-bold">Mengubah RW akan memindahkan semua penduduk secara otomatis.</p>}
                                    </div>
                                    <div>
                                        <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Pilih Dusun</label>
                                        <select 
                                            value={formData.dusun_id} 
                                            onChange={e => setData('dusun_id', e.target.value)}
                                            className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        >
                                            <option value="">-- Tanpa Dusun --</option>
                                            {dusuns.map(dusun => (
                                                <option key={dusun.id} value={dusun.id}>{dusun.nama}</option>
                                            ))}
                                        </select>
                                        {errors.dusun_id && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.dusun_id}</p>}
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Kode RT * (Maks 3 Angka)</label>
                                    <input 
                                        type="text" 
                                        value={formData.kode} 
                                        onChange={e => setData('kode', e.target.value)}
                                        maxLength="3"
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                        placeholder="Contoh: 001"
                                    />
                                    {errors.kode && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.kode}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2">Nama RT / Nama Ketua (Opsional)</label>
                                    <input 
                                        type="text" 
                                        value={formData.nama} 
                                        onChange={e => setData('nama', e.target.value)}
                                        className="w-full bg-gray-50 border-gray-200 rounded-xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-500 transition-all"
                                    />
                                    {errors.nama && <p className="text-red-500 text-[10px] font-bold mt-1">{errors.nama}</p>}
                                </div>
                            </>
                        )}

                        <div className="pt-4 border-t border-gray-100">
                            <label className="flex items-center gap-3 cursor-pointer">
                                <div className="relative">
                                    <input 
                                        type="checkbox" 
                                        className="sr-only peer"
                                        checked={formData.is_active}
                                        onChange={e => setData('is_active', e.target.checked)}
                                    />
                                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span className="text-sm font-bold text-gray-700">Status Aktif</span>
                            </label>
                        </div>
                    </form>
                </div>

                <div className="p-6 bg-white border-t border-gray-100 flex gap-3">
                    <button type="button" onClick={onClose} className="flex-1 px-6 py-3 bg-gray-50 text-gray-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-gray-100 transition-all">
                        Batal
                    </button>
                    <button 
                        type="submit" 
                        form="crudForm"
                        disabled={processing}
                        className="flex-1 px-6 py-3 bg-blue-600 text-white rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all disabled:opacity-50"
                    >
                        {processing ? 'Menyimpan...' : 'Simpan Data'}
                    </button>
                </div>
            </div>
        </div>
    );
}
