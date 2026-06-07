import React, { useState, useEffect } from 'react';
import { 
    Layers, Plus, Trash2, Edit2, CheckCircle2, XCircle, FileText, 
    Save, Loader2, UploadCloud, ChevronDown, ChevronUp, GripVertical 
} from 'lucide-react';
import { cn } from '@/lib/utils';
import axios from 'axios';

export default function SubTemplateManager({ suratType }) {
    const [templates, setTemplates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    
    // Form state untuk create/edit
    const [editingId, setEditingId] = useState(null);
    const [formData, setFormData] = useState(null);

    // Fetch data
    useEffect(() => {
        fetchTemplates();
    }, [suratType.id]);

    const fetchTemplates = async () => {
        try {
            setLoading(true);
            const res = await axios.get(route('admin.surat-type.templates.index', suratType.id));
            setTemplates(res.data);
        } catch (error) {
            console.error("Failed to load sub-templates", error);
        } finally {
            setLoading(false);
        }
    };

    const handleCreateNew = () => {
        setEditingId('new');
        setFormData({
            kode: '',
            nama: '',
            deskripsi: '',
            file_template: null,
            form_json: [],
            is_active: true,
            gender_filter: 'all',
            urutan: templates.length + 1
        });
    };

    const handleEdit = (tmpl) => {
        setEditingId(tmpl.id);
        setFormData({
            kode: tmpl.kode,
            nama: tmpl.nama,
            deskripsi: tmpl.deskripsi || '',
            file_template: null, // Don't set file input value
            current_file: tmpl.file_template,
            form_json: tmpl.form_json || [],
            is_active: tmpl.is_active,
            gender_filter: tmpl.gender_filter || 'all',
            urutan: tmpl.urutan
        });
    };

    const handleCancelEdit = () => {
        setEditingId(null);
        setFormData(null);
    };

    const handleSave = async (e) => {
        e.preventDefault();
        setSaving(true);
        
        const data = new FormData();
        Object.keys(formData).forEach(key => {
            if (key === 'form_json') {
                data.append(key, JSON.stringify(formData[key]));
            } else if (key === 'file_template') {
                if (formData[key]) data.append(key, formData[key]);
            } else if (key === 'is_active') {
                data.append(key, formData[key] ? 1 : 0);
            } else if (formData[key] !== null && formData[key] !== undefined) {
                data.append(key, formData[key]);
            }
        });

        try {
            if (editingId === 'new') {
                await axios.post(route('admin.surat-type.templates.store', suratType.id), data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            } else {
                // Gunakan POST method untuk route UPDATE karena file upload di Laravel kadang gagal via PUT/PATCH form-data
                await axios.post(route('admin.surat-type.templates.update', {
                    surat_type: suratType.id, 
                    template: editingId
                }), data, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                });
            }
            await fetchTemplates();
            setEditingId(null);
            setFormData(null);
        } catch (error) {
            console.error("Save failed", error.response?.data);
            alert(error.response?.data?.message || "Terjadi kesalahan saat menyimpan data.");
        } finally {
            setSaving(false);
        }
    };

    const handleDelete = async (id) => {
        if (!confirm('Yakin ingin menghapus sub-template ini?')) return;
        try {
            await axios.delete(route('admin.surat-type.templates.destroy', {
                surat_type: suratType.id, 
                template: id
            }));
            await fetchTemplates();
        } catch (error) {
            console.error("Delete failed", error);
            alert("Gagal menghapus sub-template.");
        }
    };

    // Helper form json management
    const addField = () => {
        setFormData(prev => ({
            ...prev,
            form_json: [...prev.form_json, {
                _id: `f_${Date.now()}`,
                name: '', label: '', type: 'text', required: false, placeholder: '', options: []
            }]
        }));
    };

    const updateField = (index, key, value) => {
        const newFields = [...formData.form_json];
        newFields[index][key] = value;
        setFormData(prev => ({ ...prev, form_json: newFields }));
    };

    const removeField = (index) => {
        const newFields = [...formData.form_json];
        newFields.splice(index, 1);
        setFormData(prev => ({ ...prev, form_json: newFields }));
    };

    const moveField = (index, direction) => {
        const newFields = [...formData.form_json];
        if (index + direction < 0 || index + direction >= newFields.length) return;
        const temp = newFields[index];
        newFields[index] = newFields[index + direction];
        newFields[index + direction] = temp;
        setFormData(prev => ({ ...prev, form_json: newFields }));
    };

    if (loading) return (
        <div className="p-8 text-center bg-gray-50 rounded-2xl border border-gray-100 flex flex-col items-center justify-center">
            <Loader2 className="w-8 h-8 text-blue-500 animate-spin mb-3" />
            <p className="text-xs font-bold text-gray-500 uppercase tracking-widest">Memuat Sub-Template...</p>
        </div>
    );

    return (
        <div className="space-y-6">
            <div className="flex items-center justify-between">
                <div>
                    <h3 className="text-sm font-black text-gray-900 uppercase italic tracking-tighter flex items-center gap-2">
                        <Layers className="w-5 h-5 text-indigo-600" />
                        Kelola Sub-Template ({templates.length})
                    </h3>
                    <p className="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">
                        Tambahkan formulir khusus dan file Word untuk masing-masing sub-template.
                    </p>
                </div>
                {!editingId && (
                    <button 
                        type="button"
                        onClick={handleCreateNew}
                        className="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200"
                    >
                        <Plus className="w-4 h-4 mr-2" />
                        TAMBAH SUB-TEMPLATE
                    </button>
                )}
            </div>

            {/* List Templates */}
            {!editingId && templates.length > 0 && (
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {templates.map((tmpl) => (
                        <div key={tmpl.id} className="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                            <div>
                                <div className="flex justify-between items-start mb-2">
                                    <div className="flex gap-2 items-center">
                                        <span className={cn(
                                            "px-2 py-1 text-[9px] font-black uppercase tracking-widest rounded-lg",
                                            tmpl.is_active ? "bg-green-50 text-green-600" : "bg-red-50 text-red-600"
                                        )}>
                                            {tmpl.is_active ? 'AKTIF' : 'NON-AKTIF'}
                                        </span>
                                        <span className="px-2 py-1 bg-gray-100 text-gray-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                            {tmpl.kode}
                                        </span>
                                        {tmpl.gender_filter !== 'all' && (
                                            <span className="px-2 py-1 bg-purple-50 text-purple-600 text-[9px] font-black uppercase tracking-widest rounded-lg">
                                                KHUSUS {tmpl.gender_filter === 'L' ? 'LAKI-LAKI' : 'PEREMPUAN'}
                                            </span>
                                        )}
                                    </div>
                                    <div className="flex gap-1">
                                        <button onClick={() => handleEdit(tmpl)} className="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-all"><Edit2 className="w-4 h-4" /></button>
                                        <button onClick={() => handleDelete(tmpl.id)} className="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-all"><Trash2 className="w-4 h-4" /></button>
                                    </div>
                                </div>
                                <h4 className="font-bold text-gray-900 mb-1">{tmpl.nama}</h4>
                                {tmpl.deskripsi && <p className="text-xs text-gray-500 line-clamp-2 mb-3">{tmpl.deskripsi}</p>}
                            </div>
                            
                            <div className="bg-gray-50 rounded-xl p-3 border border-gray-100 flex items-center gap-3">
                                <FileText className="w-8 h-8 text-blue-500" />
                                <div className="overflow-hidden">
                                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">File Template Word</p>
                                    <p className="text-xs font-bold text-gray-700 truncate" title={tmpl.file_template}>
                                        {tmpl.file_template || <span className="text-red-500 italic">Belum diupload</span>}
                                    </p>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {!editingId && templates.length === 0 && (
                <div className="text-center py-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <Layers className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <p className="text-[10px] font-black text-gray-400 uppercase tracking-widest">Belum ada sub-template</p>
                </div>
            )}

            {/* Form Editor */}
            {editingId && (
                <div className="bg-indigo-50/30 border border-indigo-100 rounded-3xl p-6 shadow-sm animate-in fade-in slide-in-from-bottom-4 duration-300">
                    <div className="space-y-6">
                        <div className="flex items-center justify-between border-b border-indigo-100 pb-4">
                            <h4 className="font-black text-indigo-900 uppercase italic tracking-tighter">
                                {editingId === 'new' ? 'Buat Sub-Template Baru' : 'Edit Sub-Template'}
                            </h4>
                            <div className="flex items-center gap-4">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <div className="relative">
                                        <input 
                                            type="checkbox" 
                                            className="sr-only" 
                                            checked={formData.is_active}
                                            onChange={(e) => setFormData({...formData, is_active: e.target.checked})}
                                        />
                                        <div className={cn("block w-10 h-6 rounded-full transition-all", formData.is_active ? "bg-green-500" : "bg-gray-300")}></div>
                                        <div className={cn("dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all", formData.is_active && "transform translate-x-4")}></div>
                                    </div>
                                    <span className="text-[10px] font-black uppercase tracking-widest text-gray-600">Aktif</span>
                                </label>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div className="space-y-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Kode / ID Singkat (Unik)</label>
                                <input 
                                    type="text" required
                                    value={formData.kode}
                                    onChange={e => setFormData({...formData, kode: e.target.value})}
                                    className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all"
                                    placeholder="Misal: N1, WALI, SUKET_PENGANTAR"
                                />
                            </div>
                            <div className="space-y-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nama Sub-Template</label>
                                <input 
                                    type="text" required
                                    value={formData.nama}
                                    onChange={e => setFormData({...formData, nama: e.target.value})}
                                    className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all"
                                    placeholder="Misal: Surat Keterangan Untuk Nikah (N1)"
                                />
                            </div>
                            
                            <div className="space-y-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Target Gender (Filter Khusus)</label>
                                <select 
                                    value={formData.gender_filter}
                                    onChange={e => setFormData({...formData, gender_filter: e.target.value})}
                                    className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all"
                                >
                                    <option value="all">Semua Jenis Kelamin (Default)</option>
                                    <option value="L">Laki-laki (Hanya muncul jika pemohon Laki-laki)</option>
                                    <option value="P">Perempuan (Hanya muncul jika pemohon Perempuan)</option>
                                </select>
                                <p className="text-[9px] text-gray-400 font-bold uppercase ml-1 italic mt-1">Berguna untuk Surat Keterangan Wali (Hanya Perempuan)</p>
                            </div>

                            <div className="space-y-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Upload Template (.docx)</label>
                                <input 
                                    type="file" accept=".docx"
                                    onChange={e => setFormData({...formData, file_template: e.target.files[0]})}
                                    className="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {formData.current_file && !formData.file_template && (
                                    <p className="text-[9px] text-green-600 font-bold uppercase ml-1 italic mt-1">File saat ini: {formData.current_file}</p>
                                )}
                            </div>

                            <div className="md:col-span-2 space-y-1">
                                <label className="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Deskripsi / Keterangan</label>
                                <textarea 
                                    value={formData.deskripsi}
                                    onChange={e => setFormData({...formData, deskripsi: e.target.value})}
                                    className="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-indigo-500 transition-all"
                                    rows="2"
                                />
                            </div>
                        </div>

                        {/* Custom Form JSON per sub-template */}
                        <div className="mt-8 border border-gray-200 rounded-2xl p-5 bg-white">
                            <div className="flex items-center justify-between mb-4">
                                <div>
                                    <h5 className="font-black text-gray-900 uppercase tracking-widest text-[11px]">Field Form Khusus Sub-Template</h5>
                                    <p className="text-[9px] text-gray-400 font-bold">Field di sini akan muncul SEBAGAI TAMBAHAN HANYA KETIKA sub-template ini dicentang.</p>
                                </div>
                                <button type="button" onClick={addField} className="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 font-black text-[9px] uppercase tracking-widest rounded-lg flex items-center">
                                    <Plus className="w-3 h-3 mr-1" /> Tambah Field
                                </button>
                            </div>

                            {formData.form_json.length === 0 ? (
                                <p className="text-xs text-gray-400 text-center py-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                    Tidak ada field khusus.
                                </p>
                            ) : (
                                <div className="space-y-3">
                                    {formData.form_json.map((field, idx) => (
                                        <div key={idx} className="flex flex-col gap-2 bg-gray-50 p-3 rounded-xl border border-gray-100 relative">
                                            <div className="absolute top-2 right-2 flex items-center gap-1">
                                                <button type="button" onClick={() => moveField(idx, -1)} disabled={idx === 0} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition-all disabled:opacity-30 disabled:hover:bg-transparent">
                                                    <ChevronUp className="w-4 h-4" />
                                                </button>
                                                <button type="button" onClick={() => moveField(idx, 1)} disabled={idx === formData.form_json.length - 1} className="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-100 rounded-lg transition-all disabled:opacity-30 disabled:hover:bg-transparent">
                                                    <ChevronDown className="w-4 h-4" />
                                                </button>
                                                <div className="w-px h-4 bg-gray-300 mx-1"></div>
                                                <button type="button" onClick={() => removeField(idx)} className="p-1.5 text-red-500 hover:bg-red-100 rounded-lg transition-all">
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                            <div className="flex flex-wrap md:flex-nowrap items-center gap-3 pr-[100px]">
                                                <input 
                                                    type="text" placeholder="DB Name (misal: nama_ayah)" 
                                                    value={field.name} onChange={e => updateField(idx, 'name', e.target.value)}
                                                    className="w-full md:w-1/4 px-3 py-2 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500"
                                                />
                                                <input 
                                                    type="text" placeholder="Label Form (misal: Nama Ayah)" 
                                                    value={field.label} onChange={e => updateField(idx, 'label', e.target.value)}
                                                    className="w-full md:w-1/3 px-3 py-2 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500"
                                                />
                                                <select 
                                                    value={field.type} onChange={e => updateField(idx, 'type', e.target.value)}
                                                    className="w-full md:w-1/4 px-3 py-2 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500"
                                                >
                                                    <option value="text">Teks Pendek</option>
                                                    <option value="textarea">Teks Panjang</option>
                                                    <option value="date">Tanggal</option>
                                                    <option value="number">Angka</option>
                                                    <option value="select">Dropdown (Select)</option>
                                                </select>
                                            </div>
                                            <div className="flex flex-col gap-2 w-full">
                                                <input 
                                                    type="text" placeholder="Placeholder (Petunjuk Pengisian)..." 
                                                    value={field.placeholder || ''} onChange={e => updateField(idx, 'placeholder', e.target.value)}
                                                    className="w-full px-3 py-2 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500 bg-white"
                                                />
                                                {field.type === 'select' && (
                                                    <input 
                                                        type="text" placeholder="Opsi Dropdown (pisahkan dengan koma, misal: Islam,Kristen,Katolik)" 
                                                        value={Array.isArray(field.options) ? field.options.join(',') : (field.options || '')} 
                                                        onChange={e => updateField(idx, 'options', e.target.value.split(',').map(s => s.trim()))}
                                                        className="w-full px-3 py-2 text-xs font-bold border-orange-200 bg-orange-50 text-orange-800 rounded-lg focus:ring-orange-500"
                                                    />
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="flex justify-end gap-3 pt-4 border-t border-indigo-100">
                            <button type="button" onClick={handleCancelEdit} className="px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                                Batal
                            </button>
                            <button type="button" onClick={handleSave} disabled={saving} className="px-5 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest text-white bg-indigo-600 hover:bg-indigo-700 transition-all flex items-center gap-2 disabled:opacity-50 shadow-md shadow-indigo-200">
                                {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                                Simpan Sub-Template
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
